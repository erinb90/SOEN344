<?php
/**
 * Created by PhpStorm.
 * User: Erin
 * Date: 3/12/2017
 * Time: 4:12 PM
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
class ReservationLogger implements Aspect
{


    /**
     * Method that will be called before real method
     *
     * @param MethodInvocation $invocation Invocation
     * @Before("execution(public Stark\CreateReservationSession->reserve(*))")
     */
    public function beforeReserveExecution(MethodInvocation $invocation)
    {
        $obj = $invocation->getThis();
        echo 'Calling Before Interceptor for method: ',
            is_object($obj) ? get_class($obj) : $obj,
            $invocation->getMethod()->isStatic() ? '::' : '->',
            $invocation->getMethod()->getName(),
            '()',
            ' with arguments: ',
            json_encode($invocation->getArguments()),
            "<br>\n";

        echo 'User is about to create a reservation!';

    }

    /**
     * Method that will be called after real method
     *
     * @param MethodInvocation $invocation Invocation
     * @After("execution(public Stark\CreateReservationSession->reserve(*))")
     */
    public function afterReserveExecution(MethodInvocation $invocation)
    {
        $obj = $invocation->getThis();
        echo 'Calling After Interceptor for method: ',
        is_object($obj) ? get_class($obj) : $obj,
        $invocation->getMethod()->isStatic() ? '::' : '->',
        $invocation->getMethod()->getName(),
        '()',
        ' with arguments: ',
        json_encode($invocation->getArguments()),
        "<br>\n";

        if ($invocation == true)
        {
            ?>
            <div id="successReservation">
                <div class="alert alert-success">
                    You have successfully created your reservation! THIS IS THE ASPECT SPEAKING
                </div>
            </div>
            <?php
        }

        else
        {
            ?>
            <div id="successReservation">
                <div class="alert alert-success">
                    Your reservation has been wait listed due to conflicts! THIS IS THE ASPECT SPEAKING
                </div>
            </div>
            <?php
        }

    }

}