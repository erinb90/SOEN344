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
     * @param \DomainObject $object
     * @param \AbstractMapper $mapper
     */
    public static function registerNew(DomainObject &$object, AbstractMapper &$mapper)
    {
        self::$new[] = array
        (
            "mapper" => $mapper,
            "object" => $object
        );
        return;
    }

    /**
     * @param \DomainObject $object
     * @param \AbstractMapper $mapper
     */
    public static function registerDirty(DomainObject &$object, AbstractMapper &$mapper)
    {
        self::$dirty[] = array
        (
            "mapper" => $mapper,
            "object" => $object
        );
        return;
    }

    /**
     * @param \DomainObject $object
     * @param \AbstractMapper $mapper
     */
    public static function registerDeleted(DomainObject &$object, AbstractMapper &$mapper)
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
        try
        {

            foreach (self::$new as $objectMapper)
            {
                /**
                 * @var $object DomainObject
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
        }
        catch(Exception $e)
        {
            return false;
        }

        self::$deleted = array();
        self::$new = array();
        self::$dirty = array();

        return true;
    }
}