<?php


namespace GrapheneNodeClient\Tools;


use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\CommandQueryDataInterface;
use GrapheneNodeClient\Commands\Single\GetBlockCommand;
use GrapheneNodeClient\Commands\Single\GetDynamicGlobalPropertiesCommand;
use GrapheneNodeClient\Commands\Single\LoginCommand;
use GrapheneNodeClient\Connectors\ConnectorInterface;
use GrapheneNodeClient\Tools\ChainOperations\OperationSerializer;
use t3ran13\ByteBuffer\ByteBuffer;

class Transaction
{
    const CHAIN_STEEM = ConnectorInterface::PLATFORM_STEEMIT;
    const CHAIN_GOLOS = ConnectorInterface::PLATFORM_GOLOS;
    const CHAIN_ID    = [
        self::CHAIN_GOLOS => '782a3039b478c839e4cb0c941ff4eaeb7df40bdd68bd441afd444b9da763de12',
        self::CHAIN_STEEM => '0000000000000000000000000000000000000000000000000000000000000000'
    ];

    public static function getChainId($chainName)
    {
        $answer = null;
        if (in_array($chainName, [self::CHAIN_GOLOS, self::CHAIN_STEEM], true)) {
            $answer = self::CHAIN_ID[$chainName];
        }

        return $answer;
    }

    /**
     * @param ConnectorInterface $connector
     * @param string             $expirationTime is string in DateInterval format, example 'PT2M'
     *
     * @return CommandQueryData
     * @throws \Exception
     */
    public static function init(ConnectorInterface $connector, $expirationTime = 'PT2M')
    {
        $tx = null;
        $chainName = $connector->getPlatform();

            $command = new GetDynamicGlobalPropertiesCommand($connector);
            $commandQueryData = new CommandQueryData();
            $properties = $command->execute(
                $commandQueryData,
                'result'
            );

            if (self::CHAIN_GOLOS === $chainName) {
                $blockId = $properties['head_block_number'] - 2;
            } elseif (self::CHAIN_STEEM === $chainName) {
//                $blockId = $properties['last_irreversible_block_num'];
                $blockId = $properties['head_block_number'] - 2;
            }
            $command = new GetBlockCommand($connector);
            $commandQueryData = new CommandQueryData();
            $commandQueryData->setParamByKey('0', $blockId);
            $block = $command->execute(
                $commandQueryData,
                'result'
            );

            if (isset($properties['head_block_number']) && isset($block['previous'])) {
                if (self::CHAIN_GOLOS === $chainName) {
                    $refBlockNum = ($properties['head_block_number'] - 3) & 0xFFFF;
                } elseif (self::CHAIN_STEEM === $chainName) {
//                    $refBlockNum = ($properties['last_irreversible_block_num'] - 1) & 0xFFFF;
                    $refBlockNum = ($properties['head_block_number'] - 3) & 0xFFFF;
                }

                $tx = new CommandQueryData();
                $buf = new ByteBuffer();
                $buf->write(hex2bin($block['previous']));

                $tx->setParams(
                    [[
                        'ref_block_num'    => $refBlockNum,
                        'ref_block_prefix' => $buf->readInt32lE(4),
                        'expiration'       => (new \DateTime($properties['time']))->add(new \DateInterval($expirationTime))->format('Y-m-d\TH:i:s\.000'),
                        'operations'       => [],
                        'extensions'       => [],
                        'signatures'       => []
                    ]]
                );
            }

        if (!($tx instanceof CommandQueryDataInterface)) {
            throw new \Exception('cant init Tx');
        }

        return $tx;
    }

    /**
     * @param string                    $chainName
     * @param CommandQueryDataInterface $trxData
     *
     * @return string
     */
    public static function getTxMsg($chainName, CommandQueryDataInterface $trxData)
    {

        //serialize transaction
        $trxParams = $trxData->getParams();
        $serBuffer = OperationSerializer::serializeTransaction($trxParams, new ByteBuffer());
        $serializedTx = self::getChainId($chainName) . bin2hex($serBuffer->read(0, $serBuffer->length()));

        return $serializedTx;
    }


    /**
     * @param string                    $chainName
     * @param CommandQueryDataInterface $trxData
     * @param string[]                  $privateWIFs
     *
     * @return mixed
     * @throws \Exception
     */
    public static function sign($chainName, CommandQueryDataInterface $trxData, $privateWIFs)
    {
        //becouse spec256k1-php canonical sign trouble will use php hack.
        //If sign is not canonical, we have to chang msg (we will add 1 sec to tx expiration time) and try to sign again
        $nTries = 0;
        while (true) {
            $nTries++;
            $msg = self::getTxMsg($chainName, $trxData);

            try {
                foreach ($privateWIFs as $keyName => $privateWif) {
                    $index = count($trxData->getParams()[0]['signatures']);

                    /** @var CommandQueryData $trxData */
                    $trxData->setParamByKey('0:signatures:' . $index, self::signOperation($msg, $privateWif));
                }
                break;
            } catch (TransactionSignException $e) {
                if ($nTries > 200) {
                    //stop tries to find canonical sign
                    throw $e;
                    break;
                } else {
                    /** @var CommandQueryData $trxData */
                    $params = $trxData->getParams();
                    foreach ($params as $key => $tx) {
                        $tx['expiration'] = (new \DateTime($tx['expiration']))
                            ->add(new \DateInterval('PT0M1S'))
                            ->format('Y-m-d\TH:i:s\.000');
                        $params[$key] = $tx;
                    }
                    $trxData->setParams($params);
                }
            }
        }

        return $trxData;
    }


    /**
     * @param string $msg serialized Tx with prefix chain id
     * @param string $privateWif
     *
     * @return string hex
     * @throws \Exception
     */
    protected static function signOperation($msg, $privateWif)
    {
        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

        $msg32 = hash('sha256', hex2bin($msg), true);
        $privateKey = Auth::PrivateKeyFromWif($privateWif);

        /** @var resource $signature */
        $signatureRec = null;
        $i = 0;
        while (true) {
            if ($i === 1) {
                //sing always the same
                throw new TransactionSignException("Can't to find canonical signature, {$i} ties");
            }
            $i++;
//            echo "\n i=" . print_r($i, true) . '<pre>'; //FIXME delete it
            if (secp256k1_ecdsa_sign_recoverable($context, $signatureRec, $msg32, $privateKey) !== 1) {
                throw new TransactionSignException("Failed to create recoverable signature");
            }

            $signature = null;
            if (secp256k1_ecdsa_recoverable_signature_convert($context, $signature, $signatureRec) !== 1) {
                throw new TransactionSignException("Failed to create signature");
            }
            $der = null;
            if (secp256k1_ecdsa_signature_serialize_der($context, $der, $signature) !== 1) {
                throw new TransactionSignException("Failed to create DER");
            }
//            echo "\n" . print_r(bin2hex($der), true) . '<pre>'; //FIXME delete it
            if (self::isSignatureCanonical($der)) {
                break;
            }
        }

        $serializedSig = null;
        $recid = 0;
        secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $serializedSig, $recid,$signatureRec);

        $serializedSig = hex2bin(base_convert($recid + 4 + 27, 10, 16)) . $serializedSig;
        $length = strlen($serializedSig);
        if ($length !== 65) {
            throw new \Exception('Expecting 65 bytes for Tx signature, instead got ' . $length);
        }

        return bin2hex($serializedSig);
    }


    /**
     * @param string $der string of binary
     *
     * @return bool
     */
    public static function isSignatureCanonical($der)
    {
        $buffer = new ByteBuffer();
        $buffer->write($der);
//        lenR = der[3];
//        lenS = der[5 + lenR];
//        if (lenR === 32 && lenS === 32) {
        $lenR = $buffer->readInt8(3);
        $lenS = $buffer->readInt8(5 + $lenR);
//        echo "\n" . var_dump($lenR, $lenS) . '<pre>'; //FIXME delete it

        return $lenR === 32 && $lenS === 32;
    }




//    /**
//     * @param string $serializedSig binary string serialized signature
//     * @param string $skip skip the first byte with sing technical data (4 - compressed | 27 - compact)
//     *
//     * @return bool
//     */
//    public static function isSignatureCanonical($serializedSig, $skip)
//    {
//        //             test after secp256k1_ecdsa_recoverable_signature_serialize_compact
//        //        public static bool IsCanonical(byte[] sig, int skip)
//        //        {
//        //        return !((sig[skip + 0] & 0x80) > 0)
//        //        && !(sig[skip + 0] == 0 && !((sig[skip + 1] & 0x80) > 0))
//        //        && !((sig[skip + 32] & 0x80) > 0)
//        //        && !(sig[skip + 32] == 0 && !((sig[skip + 33] & 0x80) > 0));
//        //        }
//
//        $buffer = new ByteBuffer();
//        $buffer->write($serializedSig);
//
//        return !(($buffer->readInt8($skip + 0, 1) & 0x80) > 0)
//            && !($buffer->readInt8($skip + 0, 1) === 0 && !(($buffer->readInt8($skip + 1, 1) & 0x80) > 0))
//            && !(($buffer->readInt8($skip + 32, 1) & 0x80) > 0)
//            && !($buffer->readInt8($skip + 32, 1) === 0 && !(($buffer->readInt8($skip + 33, 1) & 0x80) > 0));
//    }
}