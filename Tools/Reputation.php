<?php

namespace GrapheneNodeClient\Tools;


class Reputation
{
    /**
     * @param string $raw_reputation
     * @param int    $precision
     * @param int    $addNumber
     *
     * @return float|int
     */
    public static function calculate($raw_reputation, $precision = 3, $addNumber = 25)
    {
        $rating = 0;
        $raw_reputation = (double)$raw_reputation;
        if (round($raw_reputation) != 0) {
            $rating = abs(log10(abs($raw_reputation)) - 9) * 9 * ($raw_reputation > 0 ? 1 : -1) + $addNumber;
        }


        return round($rating, $precision);
    }
}