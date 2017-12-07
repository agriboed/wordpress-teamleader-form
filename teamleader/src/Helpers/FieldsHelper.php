<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Helpers;

use Teamleader\DependencyInjection\Container;

/**
 * Class Fields
 * @package Teamleader\Helpers
 */
class FieldsHelper extends AbstractHelper
{
    /**
     * @var array
     */
    protected $fields;

    /**
     *
     * @return array|mixed
     * @throws \LogicException
     */
    public function getFields()
    {
        if (null === $this->fields) {

            if (!file_exists(Container::pluginDir() . '/fields/fields.php')) {
                throw new \LogicException('Fields file not found');
            }

            $this->fields = require Container::pluginDir() . '/fields/fields.php';
        }

        return $this->fields;
    }
}