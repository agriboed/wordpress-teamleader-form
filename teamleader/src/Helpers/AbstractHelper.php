<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Helpers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Interfaces\DependencyInterface;

/**
 * Class AbstractHelper
 * @package teamleader\src\Helpers
 */
abstract class AbstractHelper implements DependencyInterface
{
    /**
     * @var $container Container
     */
    protected $container;

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}