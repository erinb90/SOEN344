<?php
namespace Stark\RequestModels;
/**
 * Interface Builder
 * @package Stark\RequestModels
 */
interface Builder
{
    /**
     * @return object of the model.
     */
    public function build();
}