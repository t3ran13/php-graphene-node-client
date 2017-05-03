<?php


namespace GrapheneNodeClient\Tools;

class Base58
{
    const ALPHABET = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    /**
     * Encode a string into base58.
     *
     * @param string $string
     * @return string
     * @throws \Exception
     */
    public static function encode($string)
    {
        $base = strlen(self::ALPHABET);
        // Type validation
        if (is_string($string) === false) {
            throw new \Exception('Argument $string must be a string.');
        }
        // If the string is empty, then the encoded string is obviously empty
        if (strlen($string) === 0) {
            return '';
        }
        // Now we need to convert the byte array into an arbitrary-precision decimal
        // We basically do this by performing a base256 to base10 conversion
        $hex = unpack('H*', $string);
        $hex = reset($hex);
        $decimal = gmp_init($hex, 16);
        // This loop now performs base 10 to base 58 conversion
        // The remainder or modulo on each loop becomes a base 58 character
        $output = '';
        while (gmp_cmp($decimal, $base) >= 0) {
            list($decimal, $mod) = gmp_div_qr($decimal, $base);
            $output .= self::ALPHABET[gmp_intval($mod)];
        }
        // If there's still a remainder, append it
        if (gmp_cmp($decimal, 0) > 0) {
            $output .= self::ALPHABET[gmp_intval($decimal)];
        }
        // Now we need to reverse the encoded data
        $output = strrev($output);
        // Now we need to add leading zeros
        $bytes = str_split($string);
        foreach ($bytes as $byte) {
            if ($byte === "\x00") {
                $output = self::ALPHABET[0] . $output;
                continue;
            }
            break;
        }
        return (string) $output;
    }

    /**
     * Decode base58 into a PHP string.
     *
     * @param string $base58
     * @return string
     * @throws \Exception
     */
    public static function decode($base58)
    {
        $base = strlen(self::ALPHABET);
        // Type Validation
        if (is_string($base58) === false) {
            throw new \Exception('Argument $base58 must be a string.');
        }
        // If the string is empty, then the decoded string is obviously empty
        if (strlen($base58) === 0) {
            return '';
        }
        $indexes = array_flip(str_split(self::ALPHABET));
        $chars = str_split($base58);
        // Check for invalid characters in the supplied base58 string
        foreach ($chars as $char) {
            if (isset($indexes[$char]) === false) {
                throw new \Exception('Argument $base58 contains invalid characters.');
            }
        }
        // Convert from base58 to base10
        $decimal = gmp_init($indexes[$chars[0]], 10);
        for ($i = 1, $l = count($chars); $i < $l; $i++) {
            $decimal = gmp_mul($decimal, $base);
            $decimal = gmp_add($decimal, $indexes[$chars[$i]]);
        }
        // Convert from base10 to base256 (8-bit byte array)
        $output = '';
        while (gmp_cmp($decimal, 0) > 0) {
            list($decimal, $byte) = gmp_div_qr($decimal, 256);
            $output = pack('C', gmp_intval($byte)) . $output;
        }
        // Now we need to add leading zeros
        foreach ($chars as $char) {
            if ($indexes[$char] === 0) {
                $output = "\x00" . $output;
                continue;
            }
            break;
        }
        return $output;
    }
}