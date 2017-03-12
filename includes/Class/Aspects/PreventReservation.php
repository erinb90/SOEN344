<?php
/**
 * Created by PhpStorm.
 * User: Erin
 * Date: 3/12/2017
 * Time: 5:29 PM
 */

namespace Stark\Aspects;

use Go\Aop\Aspect;
use Go\Aop\Intercept\FieldAccess;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\After;
use Go\Lang\Annotation\Before;
use Go\Lang\Annotation\Around;
use Go\Lang\Annotation\Pointcut;

/**
 * Monitor aspect
 */
class PreventReservation implements Aspect
{

    /**
     * Method that will be called instead of real method
     *
     * @param MethodInvocation $invocation Invocation
     * @Around("execution(public Stark\CreateReservationSession->reserve(*))")
     */
    public function aroundReserve(MethodInvocation $invocation)
    {

        echo 'SORRY, SYSTEM CLOSED FOR MAINTENANCE';

        exit;
    }
}