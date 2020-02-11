<?php


class Core_Sleep{
    /**
     * @param float $seconds
     * @return void
     */
    public static function sleep($seconds){
        $whole = floor($seconds);
        $fraction = $seconds - $whole;
        $fraction = $fraction * 1000000000; //nano seconds
        time_nanosleep($whole, $fraction);
    }
}