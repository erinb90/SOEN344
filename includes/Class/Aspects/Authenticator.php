<?php
/**
 * Created by PhpStorm.
 * User: Erin
 * Date: 3/15/2017
 * Time: 12:53 PM
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
 * Class Authenticator
 * Uses an Around advice to intercept the execution of Login->login() and perform validation
 * @package Stark\Aspects
 */
class Authenticator implements Aspect
{

    /**
     * Method that will be called instead of real method
     *
     * @param MethodInvocation $invocation Invocation
     * @Around("execution(public Stark\Login->login(*))")
     */
    public function validateLoginAttempt(MethodInvocation $invocation)
    {
        $login = $invocation->proceed(); //execute login() and get the return value

        //if true, log the user in
        if($login)
        {
            {

                ?>
                <script>window.location.replace("includes/Pages/Home.php");</script>

                <?php

            }
        }

        //if false, do not login and display error message
        else
        {
            $obj = $invocation->getThis();
            $errors = $obj->getErrors();
            $msg = '';

            ?>
            <script>
                $(function ()
                {
                    // $('#form')[0].reset();
                });
            </script>
            <br>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php
                $msg .= "<ul>";
                foreach ($errors as $error)
                {
                    $msg .= '<li>' . $error . '</li>';
                }
                $msg .= "</ul>";
                echo $msg;
                ?>
            </div>
            <?php
        }

    }

}