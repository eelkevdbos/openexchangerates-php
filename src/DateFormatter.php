<?php namespace EvdB\OpenExchangeRates;

use DateTime;
use EvdB\OpenExchangeRates\Exception\InvalidDateArgument;

class DateFormatter
{

    /**
     * @param $input
     * @return DateTime
     * @throws InvalidDateArgument
     */
    protected static function factorFromString($input)
    {
        $time = strtotime($input);

        if ($time === false) {
            throw new InvalidDateArgument('String could not be parsed as a valid date');
        }

        $date = new DateTime();
        $date->setTimestamp($time);
        return $date;
    }

    /**
     * @param int $input
     * @return DateTime
     */
    protected static function factorFromInteger($input)
    {
        return DateTime::createFromFormat('U', $input);
    }

    /**
     * @param object $input
     * @return DateTime
     * @throws InvalidDateArgument
     */
    protected static function factorFromObject($input)
    {
        if ($input instanceof DateTime || (interface_exists('DateTimeInterface') && $input instanceof \DateTimeInterface)) {
            return $input;
        }

        throw new InvalidDateArgument('Object does not implement DateTimeInterface');
    }

    /**
     * @param mixed $input
     * @param string $type
     * @return DateTime
     * @throws InvalidDateArgument
     */
    protected static function factorDateTime($input, $type)
    {
        $creator = 'factorFrom' . ucfirst($type);

        if (method_exists(get_called_class(), $creator)) {
            return call_user_func(array(get_called_class(), $creator), $input);
        }

        throw new InvalidDateArgument('Could not create a valid DateTime instance from argument');
    }

    /**
     * @param mixed $input
     * @param string $format
     * @return string
     * @throws InvalidDateArgument
     */
    public static function format($input, $format)
    {
        return static::factorDateTime($input, gettype($input))->format($format);
    }
}