<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

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
