<?php
namespace Stark;

use Go\Core\AspectKernel;
use Go\Core\AspectContainer;

use Stark\Aspects\LogAspect;
use Stark\Aspects\PreventReservation;
use Stark\Aspects\ReservationLogger;
use Stark\Aspects\TestAspect;
use Stark\Aspects\Authenticator;

/**
 * Application Aspect Kernel
 */
class ApplicationAspectKernel extends AspectKernel
{

    /**
     * Configure an AspectContainer with advisors, aspects and pointcuts
     *
     * @param AspectContainer $container
     *
     * @return void
     */
    protected function configureAop(AspectContainer $container)
    {
        // REGISTER OUR ASPECTS
        $container->registerAspect(new Authenticator());
        //$container->registerAspect(new LogAspect());
        //$container->registerAspect(new TestAspect());
        //$container->registerAspect(new ReservationLogger());
        //$container->registerAspect(new PreventReservation());
    }
}