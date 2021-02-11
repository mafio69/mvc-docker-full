<?php
/**
 * Created by PhpStorm.
 * User: mf196
 * Date: 03.10.2018
 * Time: 13:05
 */

namespace Main\Tools;


class Regon
{

    static public function checkRegon($regon)
    {
        $reg = '/^[0-9]{9}$/';
        if (preg_match($reg, $regon) == false)
            return false;
        else {
            $digits = str_split($regon);
            $checksum = (8 * intval($digits[0]) + 9 * intval($digits[1]) + 2 * intval($digits[2]) + 3 * intval($digits[3]) + 4 * intval($digits[4]) + 5 * intval($digits[5]) + 6 * intval($digits[6]) + 7 * intval($digits[7])) % 11;
            if ($checksum == 10)
                $checksum = 0;

            return (intval($digits[8]) == $checksum);
        }
    }
}