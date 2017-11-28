<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Interfaces;

/**
 * Interface HooksInterface
 * @package Teamleader\Interfaces
 */
interface HooksInterface
{
    /**
     * Set Wordpress hooks
     * @return mixed
     */
    public function initHooks();
}
