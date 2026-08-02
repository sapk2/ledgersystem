<?php

if (! function_exists('numberToWords')) {
    /**
     * Convert an amount to words in the Indian numbering system.
     *
     * e.g. 1250.50 → "One Thousand Two Hundred Fifty Rupees and Fifty Paisa Only"
     */
    function numberToWords(float $number): string
    {
        $sign = $number < 0 ? 'Minus ' : '';
        $number = abs($number);

        $whole = (int) floor($number);
        $paisa = (int) round(($number - $whole) * 100);

        $words = indianNumberToWords($whole);

        $result = $sign.($words === '' ? 'Zero' : ucfirst($words)).' Rupees';

        if ($paisa > 0) {
            $result .= ' and '.ucfirst(indianNumberToWords($paisa)).' Paisa';
        }

        return $result.' Only';
    }
}

if (! function_exists('indianNumberToWords')) {
    function indianNumberToWords(int $number): string
    {
        if ($number === 0) {
            return '';
        }

        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $words = '';

        $crore = intdiv($number, 10000000);
        $number %= 10000000;

        $lakh = intdiv($number, 100000);
        $number %= 100000;

        $thousand = intdiv($number, 1000);
        $number %= 1000;

        $hundred = intdiv($number, 100);
        $number %= 100;

        if ($crore > 0) {
            $words .= indianNumberToWords($crore).' Crore ';
        }
        if ($lakh > 0) {
            $words .= indianNumberToWords($lakh).' Lakh ';
        }
        if ($thousand > 0) {
            $words .= indianNumberToWords($thousand).' Thousand ';
        }
        if ($hundred > 0) {
            $words .= $ones[$hundred].' Hundred ';
        }
        if ($number > 0) {
            if ($words !== '') {
                $words .= 'and ';
            }
            if ($number < 20) {
                $words .= $ones[$number];
            } else {
                $words .= $tens[intdiv($number, 10)];
                $onesDigit = $number % 10;
                if ($onesDigit > 0) {
                    $words .= ' '.$ones[$onesDigit];
                }
            }
        }

        return trim($words);
    }
}
