<?php

namespace App\Helpers;

use NumberFormatter;
use Exception;

class NumberHelper
{
    public static function terbilang($number)
    {
        try {
            if (class_exists('NumberFormatter')) {
                $f = new NumberFormatter('id', NumberFormatter::SPELLOUT);
                return $f->format($number);
            }
        } catch (Exception $e) {
            // Fallback to manual implementation
        }

        return self::manualTerbilang($number);
    }

    private static function manualTerbilang($number)
    {
        $number = abs($number);
        if ($number == 0)
            return "nol";

        $huruf = [
            "",
            "satu",
            "dua",
            "tiga",
            "empat",
            "lima",
            "enam",
            "tujuh",
            "delapan",
            "sembilan",
            "sepuluh",
            "sebelas"
        ];
        $temp = "";

        if ($number < 12) {
            $temp = " " . $huruf[$number];
        } else if ($number < 20) {
            $temp = self::manualTerbilang($number - 10) . " belas";
        } else if ($number < 100) {
            $temp = self::manualTerbilang($number / 10) . " puluh" . self::manualTerbilang($number % 10);
        } else if ($number < 200) {
            $temp = " seratus" . self::manualTerbilang($number - 100);
        } else if ($number < 1000) {
            $temp = self::manualTerbilang($number / 100) . " ratus" . self::manualTerbilang($number % 100);
        } else if ($number < 2000) {
            $temp = " seribu" . self::manualTerbilang($number - 1000);
        } else if ($number < 1000000) {
            $temp = self::manualTerbilang($number / 1000) . " ribu" . self::manualTerbilang($number % 1000);
        } else if ($number < 1000000000) {
            $temp = self::manualTerbilang($number / 1000000) . " juta" . self::manualTerbilang($number % 1000000);
        } else if ($number < 1000000000000) {
            $temp = self::manualTerbilang($number / 1000000000) . " milyar" . self::manualTerbilang(fmod($number, 1000000000));
        } else if ($number < 1000000000000000) {
            $temp = self::manualTerbilang($number / 1000000000000) . " trilyun" . self::manualTerbilang(fmod($number, 1000000000000));
        }

        return trim($temp);
    }
}
