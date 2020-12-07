<?php
/**
 * Created by PhpStorm.
 * User: Asist
 * Date: 23.07.2018
 * Time: 12:42
 */

namespace Main\Tools;


class Pesel
{


    public static function checkPesel($pesel)
    {
        if (!preg_match('/^[0-9]{11}$/', $pesel)  || $pesel == '00000000000') //sprawdzamy czy ciąg ma 11 cyfr
        {
            return FALSE;
        }

        $arrSteps = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3]; // tablica z odpowiednimi wagami
        $intSum = 0;
        for ($i = 0; $i < 10; $i++) {
            $intSum += $arrSteps[$i] * $pesel[$i]; //mnożymy każdy ze znaków przez wagć i sumujemy wszystko
        }
        $int = 10 - $intSum % 10; //obliczamy sumć kontrolną
        $intControlNr = ($int == 10) ? 0 : $int;
        if ($intControlNr == $pesel[10]) //sprawdzamy czy taka sama suma kontrolna jest w ciągu
        {
            return TRUE;
        }
        return FALSE;
    }

    public static function getAge($pesel)
    {
        if (!self::checkPesel($pesel))
            return FALSE;

        if (substr($pesel, 2, 2) > 0 && substr($pesel, 2, 2) < 13) {
            return (date('Y') - (1900 + substr($pesel, 0, 2)));
        } elseif (substr($pesel, 2, 2) > 20 && substr($pesel, 2, 2) < 23) {
            return (date('Y') - (2000 + substr($pesel, 0, 2)));
        } else {
            return FALSE;
        }

    }

    public static function getBirthday($pesel)
    {
        if (!self::checkPesel($pesel))
            return FALSE;

        if (substr($pesel, 2, 2) > 0 && substr($pesel, 2, 2) < 13) {
            $year = (1900 + substr($pesel, 0, 2));
            $month = substr($pesel, 2, 2);
            $day = substr($pesel, 4, 2);
            return ($year . '-' . $month . '-' . $day);
        } elseif (substr($pesel, 2, 2) > 20 && substr($pesel, 2, 2) < 33) {
            $year = (2000 + substr($pesel, 0, 2));
            $monthA = substr($pesel, 2, 1) - 2;
            $monthB = substr($pesel, 3, 1);
            $day = substr($pesel, 4, 2);
            return ($year . '-' . $monthA . $monthB . '-' . $day);
        } else {
            return "Error";
        }

    }

    public static function getSex($pesel)
    {
        if (!self::checkPesel($pesel))
            return FALSE;

        if (substr($pesel, 9, 1) % 2 == 0)
            return 'k';
        else
            return 'm';
    }
}