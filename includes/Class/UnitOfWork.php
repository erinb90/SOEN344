<?php

/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2017-01-20
 * Time: 5:19 PM
 */
class UnitOfWork
{


    private static $dirty = array();


    private static $deleted = array();


    private static $new = array();


    // Prevent instantiation of unit of work
    /**
     * UnitOfWork constructor.
     */
    private function __construct()
    {

    }


    /**
     * @param \stdClass $object
     * @param \AbstractMapper $mapper
     */
    public static function registerNew(stdClass &$object, AbstractMapper &$mapper)
    {
        self::$new[] = array
        (
            "mapper" => $mapper,
            "object" => $object
        );
        return;
    }

    /**
     * @param \stdClass $object
     * @param \AbstractMapper $mapper
     */
    public static function registerDirty(stdClass &$object, AbstractMapper &$mapper)
    {
        self::$dirty[] = array
        (
            "mapper" => $mapper,
            "object" => $object
        );
        return;
    }

    /**
     * @param \stdClass $object
     * @param \AbstractMapper $mapper
     */
    public static function registerDeleted(stdClass &$object, AbstractMapper &$mapper)
    {
        self::$deleted[] = array
        (
            "mapper" => $mapper,
            "object" => $object
        );
        return;
    }

    /**
     * @return bool
     */
    public static function commit()
    {

        foreach (self::$new as $objectMapper)
        {
            /**
             * @var $object stdClass
             * @var $mapper AbstractMapper
             */
            $object = $objectMapper ["object"];
            $mapper = $objectMapper ["mapper"];

            $mapper->insert($object);
        }

        foreach (self::$dirty as $objectMapper)
        {
            $object = $objectMapper ["object"];
            $mapper = $objectMapper ["mapper"];

            $mapper->update($object);
        }

        foreach (self::$deleted as $objectMapper)
        {
            $object = $objectMapper ["object"];
            $mapper = $objectMapper ["mapper"];

            $mapper->delete($object);
        }

        self::$deleted = array();
        self::$new = array();
        self::$dirty = array();

        return true;
    }
}