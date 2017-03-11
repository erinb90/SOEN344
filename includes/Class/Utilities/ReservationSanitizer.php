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
     * @var string TIME_REGEX for valid time format (H:i:s)
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
        $timeAppend = ':00';
        $dateAsString = trim($dateAsString);
        $timeAsString = trim($timeAsString);

        $isValidFormat = preg_match(self::TIME_REGEX, $timeAsString);
        if (!$isValidFormat) {
            $timeAsString = $timeAsString . $timeAppend;
        }

        $isValidFormat = preg_match(self::TIME_REGEX, $timeAsString);
        if (!$isValidFormat) {
            $timeAsString = $timeAsString . $timeAppend;
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

        if (isset($dateTime) && $dateTime->format(self::DATE_FORMAT) == $fullDateAsString) {
            return $fullDateAsString;
        } else {
            return null;
        }
    }
}