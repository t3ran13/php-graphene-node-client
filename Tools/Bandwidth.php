<?php


namespace GrapheneNodeClient\Tools;



use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\Commands;
use GrapheneNodeClient\Connectors\ConnectorInterface;

class Bandwidth
{
    const BANDWIDTH_PRECISION = 1000000;

    /**
     * @param $trxString
     * @param $bandwidthPrecision
     *
     * @return float|int in bytes
     */
    public static function getTrxBandwidth($trxString, $bandwidthPrecision)
    {
        $trxBytes = mb_strlen($trxString, '8bit');

        return $bandwidthPrecision * $trxBytes;
    }

    /**
     * @param string $accountAverageBandwidth
     *
     * @return string in bytes
     */
    public static function getAccountUsedBandwidth($accountAverageBandwidth)
    {
        return gmp_strval(gmp_div_q($accountAverageBandwidth, self::BANDWIDTH_PRECISION, GMP_ROUND_MINUSINF));
    }

    /**
     * @param string $accountVShares
     * @param string $totalVestingShares
     * @param string $maxVirtualBandwidth
     *
     * @return string in bytes
     */
    public static function getAccountAvailableBandwidth($accountVShares, $totalVestingShares, $maxVirtualBandwidth)
    {
        return gmp_strval(gmp_div_q(
            gmp_div_q($maxVirtualBandwidth, self::BANDWIDTH_PRECISION, GMP_ROUND_MINUSINF),
            gmp_div_q($totalVestingShares, $accountVShares, GMP_ROUND_PLUSINF),
            GMP_ROUND_MINUSINF
        ));
    }

    /**

     * @param string             $accountName
     * @param string             $type 'market'/'forum'
     * @param ConnectorInterface $connector
     *
     * @return array ['used' => int, 'available' => int] in bytes. For upvotes and comments 1:1, for transfers 10:1
     * @throws \Exception
     */
    public static function getBandwidthByAccountName($accountName, $type, ConnectorInterface $connector)
    {
        $commands = new Commands($connector);
        $commands->get_dynamic_global_properties();
        $commandQueryData = new CommandQueryData();
        $answer = $commands->execute(
            $commandQueryData
        );
        if (!isset($answer['result'])) {
            throw new \Exception('wrong api answer for get_dynamic_global_properties');
        }
        $globalProp = $answer['result'];
        $maxVirtualBandwidth = $globalProp['max_virtual_bandwidth'];
        $totalVestingShares = str_replace(' VESTS', '', $globalProp['total_vesting_shares']);
        $totalVestingShares = substr($totalVestingShares, 0, strpos($totalVestingShares, '.'));

        $commands->get_accounts();
        $commandQueryData->setParams(
            [
                0 => [$accountName]
            ]
        );
        $answer = $commands->execute(
            $commandQueryData
        );
        if (!isset($answer['result'])) {
            throw new \Exception('wrong api answer for get_accounts');
        }
        $account = $answer['result'][0];
        $accountVShares = str_replace(' VESTS', '', $account['vesting_shares']);
        $accountVShares = substr($accountVShares, 0, strpos($accountVShares, '.'));

        $paramName = 'average_bandwidth';
        if ($type === 'market') {
            $paramName = 'average_market_bandwidth';
        }
        $accountAverageBandwidth = $account[$paramName];

        return [
            'used'      => (int)self::getAccountUsedBandwidth($accountAverageBandwidth),
            'available' => (int)self::getAccountAvailableBandwidth($accountVShares, $totalVestingShares, $maxVirtualBandwidth)
        ];
    }
}