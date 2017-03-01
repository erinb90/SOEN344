<?php
namespace Stark;
use DateTime;

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

        return [$start_date . " 00:00:00", $end_date . " 23:59:59"];
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

    /**
     * @param $startTime string is the date in (Y-m-d H:i:s) format
     * @param $endTime string is the date in (Y-m-d H:i:s) format
     * @param int $repeats
     *
     * @return array returns an array of dates based on the original date
     */
    public static function getDateRepeats($startTime, $endTime, $repeats = 0)
    {

        $dates[]  = [
            "start" => $startTime,
            "end" => $endTime
        ];
        for($i = 0; $i <= $repeats -1; $i++)
        {

            $start = date('Y-m-d H:i:s', strtotime($startTime. ' + ' . $i*7 . ' days'));
            $end = date('Y-m-d H:i:s', strtotime($endTime. ' + ' . $i*7  . ' days'));
            $dates[] = [
              "start" => $start,
                "end" => $end
            ];
        }
        return $dates;

    }
}