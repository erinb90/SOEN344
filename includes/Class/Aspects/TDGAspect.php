<?php
/**
 * Created by PhpStorm.
 * User: Erin
 * Date: 3/28/2017
 * Time: 4:00 PM
 */

namespace Stark\Aspects;


use Go\Aop\Aspect;
use Go\Aop\Intercept\FieldAccess;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\After;
use Go\Lang\Annotation\Before;
use Go\Lang\Annotation\Around;
use Go\Lang\Annotation\Pointcut;
use Stark\Registry;

/**
 * Class TDGAspect
 * @package Stark\Aspects
 *
 * Intercepts insert, delete and update methods of each TDG and performs logic common to all of them
 */
class TDGAspect implements Aspect
{

    /**
     * Method that will be called instead of real method
     *
     * @param MethodInvocation $invocation Invocation
     * @Around("execution(public Stark\TDG\*->insert(*))")
     */
    public function aroundInsert(MethodInvocation $invocation)
    {

        $lastId = -1;

        Registry::getConnection()->beginTransaction();

        try
        {
            $invocation->proceed();
            $lastId = Registry::getConnection()->lastInsertId();
        }
        catch (\Exception $e)
        {
            Registry::getConnection()->rollBack();
        }

        Registry::getConnection()->commit();

        return $lastId;

    }

    /**
     * Method that will be called instead of real method
     *
     * @param MethodInvocation $invocation Invocation
     * @Around("execution(public Stark\TDG\*->delete(*))")
     */
    public function aroundDelete(MethodInvocation $invocation)
    {

        try
        {
            $invocation->proceed();
            return true;
        }
        catch (\Exception $e)
        {

        }

        return false;

    }

    /**
     * Method that will be called instead of real method
     *
     * @param MethodInvocation $invocation Invocation
     * @Around("execution(public Stark\TDG\*->update(*))")
     */
    public function aroundUpdate(MethodInvocation $invocation)
    {

        try
        {
            $invocation->proceed();
            return true;
        }
        catch (\Exception $e)
        {

        }

        return false;

    }
}