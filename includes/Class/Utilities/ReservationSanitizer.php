<?php

namespace Stark\Utilities;

use DateTime;

/**
 * Sanitizes user input for reservation times.
 *
 * @package Stark\Utilities
 */
class ReservationSanitizer
{
    /**
     * @var string TIME_REGEX for valid time format.
     */
    const TIME_REGEX = "/([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]/";

    /**
     * @var string DATE_FORMAT for valid date format.
     */
    const DATE_FORMAT = "Y-m-d H:i:s";

    public function __construct()
    {
    }

    /**
     * @@param string $dateAsString for the input date.
     * @@param string $timeAsString for the input time.
     *
     * @return string conversion of input date and input time, or null if could not be converted.
     */
    public function convertToDateTime($dateAsString, $timeAsString)
    {
        $dateAsString = trim($dateAsString);
        $timeAsString = trim($timeAsString);

        $timeHours = substr($timeAsString, 0, 2);
        if(strpos($timeHours, ":")){
            // Need to append a 0 to hours since it is single digit
            $timeAsString = "0" .  $timeAsString;
        }

        $isValidFormat = preg_match(self::TIME_REGEX, $timeAsString);
        if (!$isValidFormat) {
            $timeAsString = $timeAsString . ':00';
        }

        $isValidFormat = preg_match(self::TIME_REGEX, $timeAsString);
        if (!$isValidFormat) {
            $timeAsString = $timeAsString . ':00';
        }

        $isValidFormat = preg_match(self::TIME_REGEX, $timeAsString);
        if (!$isValidFormat) {
            return null;
        }

        $fullDateTime = $dateAsString . ' ' . $timeAsString;
        return $this->convertDateStringToDateTime($fullDateTime);
    }

    /**
     * @@param string $fullDateAsString for the date time.
     *
     * @return string conversion of input date and input time, or null if could not be converted.
     */
    private function convertDateStringToDateTime($fullDateAsString)
    {
        $dateTime = DateTime::createFromFormat(self::DATE_FORMAT, $fullDateAsString);

        $convertedDateTime = $dateTime->format(self::DATE_FORMAT);
        if (isset($dateTime) && $convertedDateTime == $fullDateAsString) {
            return $fullDateAsString;
        } else {
            return null;
        }
    }
}