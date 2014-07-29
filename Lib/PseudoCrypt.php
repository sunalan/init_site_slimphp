<?php 

final class PseudoCrypt {

    private static $range = 62;
    private static $goldenPrimes36 = array(
        /*
        * Key..: next prime greater than 36 ^ n / 1.618033988749894848
        * Value: modular multiplicative inverse
        */
        '1' => '1',
        '23' => '11',
        '809' => '809',
        '28837' => '29485',
        '1038073' => '179017',
        '37370153' => '47534873',
        '1345325473' => '264202849',
        '48431716939' => '19727015779',
        '1743541808839' => '1532265214711',
        '62767505117101' => '67935388019749',
        '3656158440062987' => '3323780400057251',
        '131621703842267239' => '14056686818106199'
    );

    private static $goldenPrimes62 = array(
        /*
        * Key..: next prime greater than 62 ^ n / 1.618033988749894848
        * Value: modular multiplicative inverse
        */
        '1' => '1',
        '41' => '59',
        '2377' => '1677',
        '147299' => '187507',
        '9132313' => '5952585',
        '566201239' => '643566407',
        '35104476161' => '22071637057',
        '2176477521929' => '294289236153',
        '134941606358731' => '88879354792675',
        '8366379594239857' => '7275288500431249',
        '518715534842869223' => '280042546585394647'
    );

    private static $chars62 = array(
        /*
        * Ascii : 0-9
        */
        0=> 48, 1=>49, 2=>50, 3=>51, 4=>52, 5=>53, 6=>54, 7=>55, 8=>56, 9=>57,
        /*
        * Ascii : A-Z
        */
        10=>65, 11=>66, 12=>67, 13=>68, 14=>69, 15=>70, 16=>71, 17=>72, 18=>73, 19=>74, 20=>75,
        21=>76, 22=>77, 23=>78, 24=>79, 25=>80, 26=>81, 27=>82, 28=>83, 29=>84, 30=>85, 31=>86,
        32=>87, 33=>88, 34=>89, 35=>90,
        /*
        * Ascii : a-z
        */
        36=>97, 37=>98, 38=>99, 39=>100, 40=>101, 41=>102, 42=>103, 43=>104, 44=>105, 45=>106, 46=>107,
        47=>108, 48=>109, 49=>110, 50=>111, 51=>112, 52=>113, 53=>114, 54=>115, 55=>116, 56=>117, 57=>118,
        58=>119, 59=>120, 60=>121, 61=>122
    );

    /**
    * Calculates the hash 36 or 62 based range.
    */
    private static function baseX($int) {
        $key = '';
        while (bccomp($int, 0) > 0) {
            $mod = bcmod($int, self::$range);
            $key .= chr(self::$chars62[$mod]);
            $int = bcdiv($int, self::$range, 0);
        }
        return strrev($key);
    }

    /**
    * Inverse calculation for hashes 36 or 62 based range.
    */
    private static function unbaseX($key) {
        $int = 0;
        foreach (str_split(strrev($key)) as $i => $char) {
            $dec = array_search(ord($char), self::$chars62);
            $int = bcadd(bcmul($dec, bcpow(self::$range, $i)), $int);
        }
        return $int;
    }

    /**
    * Generates the hash 62 based chars(a-z, A-Z, 0-9) to a given int number.
    *
    * Important:
    *
    * – the biggest cryptable integer with hash length = 5 is 916 132 831.
    *
    * – the biggest cryptable integer with hash length = 6 is 56 800 235 583. It seems to be enough, no?
    */
    public static function hash($num, $len = 5) {
        $ceil = bcpow(self::$range, $len);
        $primes = array_keys(self::$range === 36 ? self::$goldenPrimes36 : self::$goldenPrimes62);
        $prime = $primes[$len];
        $dec = bcmod(bcmul($num, $prime, 0),$ceil);
        $hash = self::baseX($dec);
        return str_pad($hash, $len, '0', STR_PAD_LEFT);
    }

    /**
    * Generates the hash 36 based chars(A-Z, 0-9) to a given int number.
    *
    * Important:
    *
    * – the biggest cryptable integer with hash length = 5 is 60 466 175.
    *
    * – the biggest cryptable integer with hash length = 6 is 2 176 782 335.
    */
    public static function hash36($num, $len = 5) {
        self::$range = 36;
        return self::hash($num, $len);
    }

    /**
    * Converts a previous generated hash to its original integer.
    */
    public static function unhash($hash) {
        Object::tracert(__FILE__,__CLASS__,__METHOD__, self::UKEY);
        $len = strlen($hash);
        $ceil = bcpow(self::$range, $len);
        $mmiprimes = array_values(self::$range === 36 ? self::$goldenPrimes36 : self::$goldenPrimes62);
        $mmi = $mmiprimes[$len];
        $num = self::unbaseX($hash);
        $dec = bcmod(bcmul($num, $mmi), $ceil);
        return $dec;
    }
}