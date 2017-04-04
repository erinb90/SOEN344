<?php
namespace Stark;

use Go\Core\AspectKernel;
use Go\Core\AspectContainer;
use Stark\Aspects\TestAspect;
use Stark\Aspects\TDGAspect;

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
        $container->registerAspect(new TDGAspect());
        //$container->registerAspect(new TestAspect());
    }
}