<?php
namespace Stark;

use Stark\Interfaces\AbstractMapper;
use Stark\Interfaces\DomainObject;

class UnitOfWork
{

    /**
     * @var array objects that have been updated
     */
    private static $dirty = [];


    /**
     * @var array objects that are to be deleted
     */
    private static $deleted = [];

    /**
     * @var array objects that need to be created/inserted into the database
     */
    private static $new = [];

    /**
     * UnitOfWork constructor.
     */
    private function __construct()
    {

    }

    /**
     * @param \Stark\Interfaces\DomainObject $object
     * @param \Stark\Interfaces\AbstractMapper $mapper
     */
    public static function registerNew(DomainObject &$object, AbstractMapper &$mapper)
    {
        self::$new[] = [

            "mapper" => $mapper,
            "object" => $object
        ];

        return;
    }

    /**
     * @param \Stark\Interfaces\DomainObject $object
     * @param \Stark\Interfaces\AbstractMapper $mapper
     */
    public static function registerDirty(DomainObject &$object, AbstractMapper &$mapper)
    {
        self::$dirty[] = [

            "mapper" => $mapper,
            "object" => $object
        ];

        return;
    }

    /**
     * @param \Stark\Interfaces\DomainObject $object
     * @param \Stark\Interfaces\AbstractMapper $mapper
     */
    public static function registerDeleted(DomainObject &$object, AbstractMapper &$mapper)
    {
        self::$deleted[] = [

            "mapper" => $mapper,
            "object" => $object
        ];

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
        catch (\Exception $e)
        {
            return false;
        }

        self::$deleted = [];
        self::$new = [];
        self::$dirty = [];

        return TRUE;
    }
}