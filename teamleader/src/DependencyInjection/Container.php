<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\DependencyInjection;

use Teamleader\Interfaces\DependencyInterface;

/**
 * Class Container
 * @package Openwork\DependencyInjection
 */
class Container
{
    /**
     * @var array
     *
     */
    protected $dependencies = array();

    /**
     * @var string
     */
    protected static $key;

    /**
     * @var string
     */
    protected static $version;

    /**
     * @var string
     */
    protected static $plugin_dir;

    /**
     * @var string
     */
    protected static $basename;

    /**
     * @var string
     */
    protected static $plugin_url;

    /**
     * Container constructor.
     *
     * @param $plugin
     * @param $key
     * @param $version
     */
    public function __construct($plugin, $key, $version)
    {
        static::$key = $key;
        static::$version = $version;
        static::$basename = basename($plugin);
        static::$plugin_dir = plugin_dir_path($plugin);
        static::$plugin_url = plugin_dir_url($plugin);
    }

    /**
     * @return string
     */
    public static function key()
    {
        return static::$key;
    }

    /**
     * @return string
     */
    public static function version()
    {
        return static::$version;
    }

    /**
     * @return string
     */
    public static function basename()
    {
        return static::$basename;
    }

    /**
     * @return string
     */
    public static function pluginUrl()
    {
        return static::$plugin_url;
    }

    /**
     * @return mixed
     */
    public static function pluginDir()
    {
        return static::$plugin_dir;
    }

    /**
     * @param $dependency
     * @return self
     */
    public function set($dependency)
    {
        $this->dependencies[get_class($dependency)] = $dependency;

        return $this;
    }

    /**
     * Return from memory or create and put to memory new object
     *
     * @param string $dependency
     * @param array $arguments
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($dependency, array $arguments = [])
    {
        if (!isset($this->dependencies[$dependency])) {
            if (!class_exists($dependency)) {
                throw new \LogicException('Dependency not found');
            }

            if (empty($arguments)) {
                $instance = new $dependency;
            } else {
                $reflect = new \ReflectionClass($dependency);
                $instance = $reflect->newInstanceArgs($arguments);
            }

            if ($instance instanceof DependencyInterface) {
                $instance->setContainer($this);
            }

            $this->dependencies[$dependency] = $instance;
        }

        return $this->dependencies[$dependency];
    }
}