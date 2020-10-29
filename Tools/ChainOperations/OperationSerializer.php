<?php

namespace GrapheneNodeClient\Tools\ChainOperations;

use GrapheneNodeClient\Connectors\ConnectorInterface;
use GrapheneNodeClient\Tools\ChainOperations\TypeSerializers\TypeSerializerInterface;
use StephenHill\Base58;
use StephenHill\GMPService;
use t3ran13\ByteBuffer\ByteBuffer;

class OperationSerializer
{
    const TYPE_CHAIN_PROPERTIES      = 'chainProperties';
    const TYPE_DONATE_MEMO           = 'donateMemo';
    const TYPE_SET_EXTENSIONS        = 'setExtensions';
    const TYPE_SET_BENEFICIARIES     = 'setBeneficiaries';
    const TYPE_SET_FUTURE_EXTENSIONS = 'setFutureExtensions';
    const TYPE_SET_STRING            = 'setString';
    const TYPE_BENEFICIARY           = 'beneficiary';
    const TYPE_PUBLIC_KEY            = 'publicKey';
    const TYPE_STRING                = 'string';
    const TYPE_INT8                  = 'int8';
    const TYPE_INT16                 = 'int16';
    const TYPE_INT32                 = 'int32';
    const TYPE_INT64                 = 'int64';
    const TYPE_ASSET                 = 'asset';
    const TYPE_BOOL                  = 'bool';
    const TYPE_VARIANT_OBJECT        = 'variantObject';
    const TYPE_OPTIONAL_STRING       = 'optionalString';
    const TYPE_VOID                  = 'void';
    const TYPE_FUTURE_EXTENSIONS     = 'futureExtensions';

    /** @var array */
    protected static $opFieldsMap = [];

    /**
     * @param string      $chainName
     * @param array       $trxParams
     * @param null|Buffer $byteBuffer
     *
     * @return null|string|Buffer
     * @throws \Exception
     */
    public static function serializeTransaction($chainName, $trxParams, $byteBuffer = null)
    {
        $buffer = $byteBuffer === null ? (new ByteBuffer()) : $byteBuffer;

        $buffer->writeInt16LE($trxParams[0]['ref_block_num'], 0);
        $buffer->writeInt32LE($trxParams[0]['ref_block_prefix'], 2);
        $expirationSec = is_int($trxParams[0]['expiration']) ? $trxParams[0]['expiration'] : strtotime($trxParams[0]['expiration']);
        $buffer->writeInt32LE($expirationSec, 6);
        $buffer->writeInt8(count($trxParams[0]['operations']));


        //serialize only operations data
        foreach ($trxParams[0]['operations'] as $operation) {
            $opData = $operation[1];
            self::serializeOperation($chainName, $operation[0], $opData, $buffer);
        }


        $buffer->writeInt8(count($trxParams[0]['extensions']));
        foreach ($trxParams[0]['extensions'] as $extansion) {
            //is it used anywhere?
        }

        return $byteBuffer === null ? $buffer->getBuffer('H', 0, $buffer->getCurrentOffset()) : $byteBuffer;
    }


    /**
     * @param string     $chainName
     * @param string     $operationName
     * @param array      $data
     * @param ByteBuffer $byteBuffer
     *
     * @return ByteBuffer
     * @throws \Exception
     */
    public static function serializeOperation($chainName, $operationName, $data, $byteBuffer)
    {
        //operation id
        $opId = ChainOperations::getOperationId($chainName, $operationName);
        $byteBuffer->writeInt8($opId);

        foreach (self::getOpFieldsTypes($chainName, $operationName) as $field => $type) {
            self::serializeType($type, $data[$field], $byteBuffer, $chainName);
        }

        return $byteBuffer;
    }


    /**
     * @param string $chainName
     * @param string $operationName
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getOpFieldsTypes($chainName, $operationName)
    {
        if (!isset(self::$opFieldsMap[$chainName])) {
            switch ($chainName) {
                case ConnectorInterface::PLATFORM_GOLOS:
                    $op = ChainOperationsGolos::FIELDS_TYPES;
                    break;
                case ConnectorInterface::PLATFORM_HIVE:
                    $op = ChainOperationsHive::FIELDS_TYPES;
                    break;
                case ConnectorInterface::PLATFORM_STEEMIT:
                    $op = ChainOperationsSteem::FIELDS_TYPES;
                    break;
                case ConnectorInterface::PLATFORM_VIZ:
                    $op = ChainOperationsViz::FIELDS_TYPES;
                    break;
                case ConnectorInterface::PLATFORM_WHALESHARES:
                    $op = ChainOperationsWhaleshares::FIELDS_TYPES;
                    break;
                default:
                    throw new \Exception("There is no operations fields for '{$chainName}'");
            }

            self::$opFieldsMap[$chainName] = $op;
        }

        if (!isset(self::$opFieldsMap[$chainName][$operationName])) {
            throw new \Exception("There is no information about fields of operation:'{$operationName}'. Please add fields for this operation");
        }

        return self::$opFieldsMap[$chainName][$operationName];
    }


    /**
     * @param string     $type
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeType(string $type, $value, ByteBuffer $byteBuffer, string $chainName)
    {
        $serializer = 'serialize' . ucfirst($type);
        self::$serializer($value, $byteBuffer, $chainName);
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     */
    public static function serializeString($value, ByteBuffer $byteBuffer, string $chainName)
    {
        //Writes a UTF8 encoded string prefixed 32bit base 128 variable-length integer.
        $strLength = strlen($value);

        if ($strLength <= 128) {
            $byteBuffer->writeInt8($strLength);
        } elseif ($strLength <= 16511) {
            $strLength = ceil($strLength / 128) * 256
                + ($strLength - ceil($strLength / 128) * 128);
            $byteBuffer->writeInt16LE($strLength);
        } else {
            $n3 = ceil($strLength / (128 * 128));
            $n2 = ceil(($strLength - $n3 * 128 * 128) / 128);
            $strLength = $n3 * 256 * 256
                + $n2 * 256
                + ($strLength - $n3 * 128 * 128 - $n2 * 128);
            $byteBuffer->writeInt32LE($strLength);
            if ($strLength <= 16777215) {
                $byteBuffer->setCurrentOffset($byteBuffer->getCurrentOffset() - 1);
            }
        }
        $byteBuffer->writeVStringLE($value);
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     */
    public static function serializeInt8($value, ByteBuffer $byteBuffer, string $chainName)
    {
        $byteBuffer->writeInt8($value);
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     */
    public static function serializeInt16($value, ByteBuffer $byteBuffer, string $chainName)
    {
        $byteBuffer->writeInt16LE($value);
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     */
    public static function serializeInt32($value, ByteBuffer $byteBuffer, string $chainName)
    {
        $byteBuffer->writeInt32LE($value);
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     */
    public static function serializeInt64($value, ByteBuffer $byteBuffer, string $chainName)
    {
        $byteBuffer->writeInt64LE($value);
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     */
    public static function serializeAsset($value, ByteBuffer $byteBuffer, string $chainName)
    {
        list($amount, $symbol) = explode(' ', $value);

        $byteBuffer->writeInt64LE(str_replace('.', '', $amount));

        $dot = strpos($amount, '.');
        $precision = $dot === false ? 0 : strlen($amount) - $dot - 1;
        $byteBuffer->writeInt8($precision);

        $byteBuffer->writeVStringLE(strtoupper($symbol));
        for ($i = 0; $i < 7 - strlen($symbol); $i++) {
            $byteBuffer->writeInt8(0);
        }
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeSetString($value, ByteBuffer $byteBuffer, string $chainName)
    {
        $byteBuffer->writeInt8(count($value));
        foreach ($value as $string) {
            self::serializeString($string, $byteBuffer, $chainName);
        }
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeSetExtension($value, ByteBuffer $byteBuffer, string $chainName)
    {
        $byteBuffer->writeInt8(count($value));
        foreach ($value as $extension) {
            $byteBuffer->writeInt8($extension[0]);
            if ($extension[0] === 0) {
                self::serializeSetBeneficiaries($extension[1], $byteBuffer, $chainName);
            } else {
                throw new \Exception("There is no serializer logic for '{$extension[0]}' extension");
            }
        }
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeSetBeneficiaries($value, ByteBuffer $byteBuffer, string $chainName)
    {
        $byteBuffer->writeInt8(count($value['beneficiaries']));
        foreach ($value['beneficiaries'] as $beneficiary) {
            self::serializeBeneficiary($beneficiary, $byteBuffer, $chainName);
        }
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeBeneficiary($value, ByteBuffer $byteBuffer, string $chainName)
    {
        self::serializeString($value['account'], $byteBuffer, $chainName);
        self::serializeInt16($value['weight'], $byteBuffer, $chainName);
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeBool($value, ByteBuffer $byteBuffer, string $chainName)
    {
        self::serializeInt8($value ? 1 : 0, $byteBuffer, $chainName);
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializePublicKey($value, ByteBuffer $byteBuffer, string $chainName)
    {
        $clearPubKey = substr($value, 3);
        $base58 = new Base58(null, new GMPService()); //decode base 58 to str
        $stringPubKey = substr($base58->decode($clearPubKey), 0, 33);
        $byteBuffer->writeVStringLE($stringPubKey);
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeChainProperties($value, ByteBuffer $byteBuffer, string $chainName)
    {
        if (count($value) > 0) {
            foreach (self::getOpFieldsTypes($chainName, self::TYPE_CHAIN_PROPERTIES) as $field => $type) {
                self::serializeType($type, $value[$field], $byteBuffer, $chainName);
            }
        }
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeSetFutureExtensions($value, ByteBuffer $byteBuffer, string $chainName)
    {
        $byteBuffer->writeInt8(count($value));
        foreach ($value as $extension) {
            self::serializeFutureExtensions($extension, $byteBuffer, $chainName);
        }
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeFutureExtensions($value, ByteBuffer $byteBuffer, string $chainName)
    {
        throw new \Exception('There is no serializer logic for ' . self::TYPE_FUTURE_EXTENSIONS);
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeDonateMemo($value, ByteBuffer $byteBuffer, string $chainName)
    {
        if (count($value) > 0) {
            foreach (self::getOpFieldsTypes($chainName, self::TYPE_DONATE_MEMO) as $field => $type) {
                self::serializeType($type, $value[$field] ?? null, $byteBuffer, $chainName);
            }
        }
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeVariantObject($value, ByteBuffer $byteBuffer, string $chainName)
    {
        $byteBuffer->writeInt8(count($value));
        foreach ($value as $key => $val) {
            self::serializeString($key, $byteBuffer, $chainName);
            if (is_string($val)) {
                $prefix = 5;
                $type = self::TYPE_STRING;
            } elseif (is_int($val)) {
                $prefix = 2;
                $type = self::TYPE_INT64;
            } else {
                throw new \Exception('Correct value for ' . self::TYPE_VARIANT_OBJECT . 'is array pf numbers or strings');
            }

            self::serializeInt8($prefix, $byteBuffer, $chainName);
            self::serializeType($type, $val, $byteBuffer, $chainName);
        }
    }

    /**
     * @param mixed      $value
     * @param ByteBuffer $byteBuffer
     * @param string     $chainName
     *
     * @return void
     * @throws \Exception
     */
    public static function serializeOptionalString($value, ByteBuffer $byteBuffer, string $chainName)
    {
        if ($value === null) {
            self::serializeInt8(0, $byteBuffer, $chainName);
        } else {
            self::serializeInt8(1, $byteBuffer, $chainName);
            self::serializeString($value, $byteBuffer, $chainName);
        }
    }


}