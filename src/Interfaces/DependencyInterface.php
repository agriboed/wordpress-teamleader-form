<?php

namespace Teamleader\Interfaces;

use Teamleader\DependencyInjection\Container;

/**
 * Interface DependencyInterface
 * @package Teamleader\Interfaces
 */
interface DependencyInterface
{
    public function __construct(Container $container);
}
