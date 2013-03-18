<?php
/**
 * Created by JetBrains PhpStorm.
 * User: prof_lebrun
 * Date: 13-01-27
 * Time: 3:34 PM
 * To change this template use File | Settings | File Templates.
 */
class Geohash
{

#################################################################

# http://en.wikipedia.org/wiki/Geohash

# This is really just a flamework-ified version of:
# http://blog.dixo.net/downloads/geohash-php-class/

#################################################################

protected $geohash_alpha = "0123456789bcdefghjkmnpqrstuvwxyz";
protected $geohash_map = array();

    public function __construct()
    {
        for ($i = 0; $i < 32; $i++) {

            $k = substr($this->geohash_alpha, $i, 1);
            $v = str_pad(decbin($i), 5, "0", STR_PAD_LEFT);

            $this->geohash_map[$k] = $v;
        }
    }

    public function geo_geohash_encode($lat, $lon)
    {

        $plat = $this->_geo_geohash_precision($lat);
        $latbits = 1;

        $err = 45;

        while ($err > $plat) {
            $latbits++;
            $err /= 2;
        }

        $plon = $this->_geo_geohash_precision($lon);
        $lonbits = 1;
        $err = 90;

        while ($err > $plon) {

            $lonbits++;
            $err /= 2;
        }

        # bit counts need to be equal

        $bits = max($latbits, $lonbits);

        # as the hash create bits in groups of 5, lets not
        # waste any bits - lets bulk it up to a multiple of 5
        # and favour the longitude for any odd bits

        $lonbits = $bits;
        $latbits = $bits;
        $addlon = 1;

        while (($lonbits + $latbits) % 5 != 0) {

            $lonbits += $addlon;
            $latbits += !$addlon;
            $addlon = !$addlon;
        }

        # encode each as binary string

        $blat = $this->_geo_geohash_binencode($lat, -90, 90, $latbits);
        $blon = $this->_geo_geohash_binencode($lon, -180, 180, $lonbits);

        # merge lat and long together

        $binary = "";
        $uselon = 1;

        while (strlen($blat) + strlen($blon)) {

            if ($uselon) {

                $binary = $binary . substr($blon, 0, 1);
                $blon = substr($blon, 1);
            } else {

                $binary = $binary . substr($blat, 0, 1);
                $blat = substr($blat, 1);
            }

            $uselon = !$uselon;
        }

        # convert binary string to hash

        $hash = "";

        for ($i = 0; $i < strlen($binary); $i += 5) {

            $n = bindec(substr($binary, $i, 5));
            $hash = $hash . $this->geohash_alpha[$n];
        }


        return $hash;

    }

    public function geo_geohash_decode($hash)
    {

        # decode hash into binary string

        $binary = "";

        $hl = strlen($hash);

        for ($i = 0; $i < $hl; $i++) {
            $binary .= $this->geohash_map[substr($hash, $i, 1)];
        }

        # split the binary into lat and log binary strings

        $bl = strlen($binary);

        $blat = "";
        $blon = "";

        for ($i = 0; $i < $bl; $i++) {

            if ($i % 2) {
                $blat = $blat . substr($binary, $i, 1);
            } else {
                $blon = $blon . substr($binary, $i, 1);
            }
        }

        # now concert to decimal

        $lat = $this->_geo_geohash_bindecode($blat, -90, 90);
        $lon = $this->_geo_geohash_bindecode($blon, -180, 180);

        # figure out how precise the bit count makes this calculation

        $lat_err = $this->_geo_geohash_calc_error(strlen($blat), -90, 90);
        $lon_err = $this->_geo_geohash_calc_error(strlen($blon), -180, 180);

        # how many decimal places should we use? There's a little art to
        # this to ensure I get the same roundings as geohash.org

        $lat_places = max(1, -round(log10($lat_err))) - 1;
        $lon_places = max(1, -round(log10($lon_err))) - 1;

        # round it

        $lat = round($lat, $lat_places);
        $lon = round($lon, $lon_places);

        return array($lat, $lon);
    }

    private function _geo_geohash_precision($number)
    {

        $precision = 0;
        $pt = strpos($number, '.');

        if ($pt !== false) {
            $precision = -(strlen($number) - $pt - 1);
        }

        return pow(10, $precision) / 2;
    }

    private function _geo_geohash_binencode($number, $min, $max, $bitcount)
    {

        if ($bitcount == 0) {
            return "";
        }

        # this is our mid point - we will produce a bit to say
        # whether $number is above or below this mid point

        $mid = ($min + $max) / 2;

        if ($number > $mid) {
            return "1" . $this->_geo_geohash_binencode($number, $mid, $max, $bitcount - 1);
        }

        return "0" . $this->_geo_geohash_binencode($number, $min, $mid, $bitcount - 1);
    }

    private function _geo_geohash_bindecode($binary, $min, $max)
    {

        $mid = ($min + $max) / 2;

        if (strlen($binary) == 0) {
            return $mid;
        }

        $bit = substr($binary, 0, 1);
        $binary = substr($binary, 1);

        if ($bit == 1) {
            return $this->_geo_geohash_bindecode($binary, $mid, $max);
        }

        return $this->_geo_geohash_bindecode($binary, $min, $mid);
    }

#################################################################

    private function _geo_geohash_calc_error($bits, $min, $max)
    {

        $err = ($max - $min) / 2;

        while ($bits--) {
            $err /= 2;
        }

        return $err;
    }
}