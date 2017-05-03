<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Tools\Base58;

class ToWifCommand
{
    /**
     * @param string $user
     * @param string $password
     * @param string $action
     *
     * @return string
     */
    public static function execute($user, $password, $action)
    {

//        Auth.toWif = function (name, password, role) {
//            var seed = name + role + password;
//            var brainKey = seed.trim().split(/[\t\n\v\f\r ]+/).join(' ');
//            var hashSha256 = crypto.createHash('sha256').update(brainKey).digest();
//            var privKey = Buffer.concat([new Buffer([0x80]), hashSha256]);
//            var checksum = crypto.createHash('sha256').update(privKey).digest();
//            checksum = crypto.createHash('sha256').update(checksum).digest();
//            checksum = checksum.slice(0, 4);
//            var privWif = Buffer.concat([privKey, checksum]);
//            return bs58.encode(privWif);
//        };


        $seed = $user . $password . $action;
        $brainKey = implode(' ', preg_split('/[\t\n\v\f\r ]+/', trim($seed))); // var brainKey = seed.trim().split(/[\t\n\v\f\r ]+/).join(' ');
        $hashSha256 = hash('sha256', $brainKey, true); // var hashSha256 = crypto.createHash('sha256').update(brainKey).digest();
        $privKeyBin = hex2bin(0x80) . $hashSha256; // var privKey = Buffer.concat([new Buffer([0x80]), hashSha256]);
        $checksumBin = hash('sha256', $privKeyBin, true); // var checksum = crypto.createHash('sha256').update(privKey).digest();
        $checksumBin = hash('sha256', $checksumBin, true); // checksum = crypto.createHash('sha256').update(checksum).digest();
        $checksumBin = substr($checksumBin, 0, 4); // checksum = checksum.slice(0, 4);
        $privWifBin = hash('sha256', $privKeyBin . $checksumBin, true);// var privWif = Buffer.concat([privKey, checksum]);


        return Base58::encode($privWifBin);
    }
}