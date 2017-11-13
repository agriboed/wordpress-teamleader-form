<?php

namespace Teamleader\Helpers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Interfaces\DependencyInterface;

/**
 * Class Fields
 * @package Teamleader\Helpers
 */
class Fields implements DependencyInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $plugin_dir;

    /**
     * @var array
     */
    protected $fields;

    /**
     * Fields constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->plugin_dir = $container::getPluginDir();
    }

    /**
     *
     * @return array|mixed
     * @throws \LogicException
     */
    public function getFields()
    {
        if (null === $this->fields) {

            if (!file_exists($this->plugin_dir . '/fields/fields.php')) {
                throw new \LogicException('Fields file not found');
            }

            $this->fields = require $this->plugin_dir . '/fields/fields.php';
        }

        return $this->fields;
    }
}