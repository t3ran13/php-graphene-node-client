<?php

namespace GrapheneNodeClient\Tools;


class Reputation
{
    public static function calculate($raw_reputation)
    {
        $repRawFloat = (float)$raw_reputation;
        $out = (log10(abs($repRawFloat)) - 9) * 9 * ($repRawFloat >= 0 ? 1 : -1) + 25;


        return $out;
    }
}