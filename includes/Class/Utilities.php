<?php

/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-01-21
 * Time: 9:01 PM
 */
class Utilities
{

    /**
     * @param $date string current date in question
     *
     * @return  array returns the start date as well as the end date
     */
    public static function getWeek($date)
    {
        $ts = strtotime($date);
        $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
        $start_date = date('Y-m-d', $start);
        $end_date = date('Y-m-d', strtotime('next saturday', $start));

        return array($start_date . " 00:00:00", $end_date . " 23:59:59");
    }


    /**
     * @param $date
     * @param string $format date format. Example:  Y-m-d H:i:s
     *
     * @return bool true if the date given matches the format provided
     */
    public static function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }
}