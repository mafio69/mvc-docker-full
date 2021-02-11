<?php
namespace Main\Tools;


class Nip
{
    public static function checkNip($nip): bool
    {
        $nipWithoutDashes = preg_replace("/-/", "", $nip);
        $reg = '/^[0-9]{10}$/';
        if (preg_match($reg, $nipWithoutDashes) == false)
            return false;
        else {
            $digits = str_split($nipWithoutDashes);
            $checksum = (6 * intval($digits[0]) + 5 * intval($digits[1]) + 7 * intval($digits[2]) + 2 * intval($digits[3]) + 3 * intval($digits[4]) + 4 * intval($digits[5]) + 5 * intval($digits[6]) + 6 * intval($digits[7]) + 7 * intval($digits[8])) % 11;

            return (intval($digits[9]) == $checksum);
        }
    }
}