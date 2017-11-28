<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Controllers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Interfaces\DependencyInterface;

/**
 * Class ControllerAbstract
 * @package Teamleader\Controllers
 */
abstract class AbstractController implements DependencyInterface
{
    /**
     * @var $container Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
