<?php

namespace Teamleader\DependencyInjection;

use Teamleader\Interfaces\DependencyInterface;
use Teamleader\Interfaces\HooksInterface;

/**
 * Class Container
 * @package Teamleader\DependencyInjection
 */
class Container
{
    /**
     * @var string
     */
    protected static $key;

    /**
     * @var string
     */
    protected static $basename;

    /**
     * @var string
     */
    protected static $plugin_dir;

    /**
     * @var string
     */
    protected static $plugin_dir_url;

    /**
     * @var array
     */
    protected $containers = [];

    /**
     * Container constructor.
     *
     * @param string $key
     * @param string $basename
     * @param array $dependencies
     */
    public function __construct($key = '', $basename = '', array $dependencies = [])
    {
        static::$key = $key;
        static::$basename = plugin_basename($basename);
        static::$plugin_dir = plugin_dir_path($basename);
        static::$plugin_dir_url = plugin_dir_url($basename);

        /**
         * @var $dependencies DependencyInterface[]
         */
        foreach ($dependencies as $d) {
            if (!in_array(DependencyInterface::class, class_implements($d), true)) {
                continue;
            }

            $dependency = new $d($this);
            $this->containers[$d] = $dependency;

            if ($dependency instanceof HooksInterface) {
                $dependency->initHooks();
            }
        }
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getContainer($key = '')
    {
        return $this->containers[$key];
    }

    /**
     * @return string
     */
    public static function getKey()
    {
        return static::$key;
    }

    /**
     * @return string
     */
    public static function getBasename()
    {
        return static::$basename;
    }

    /**
     * @return string
     */
    public static function getPluginDir()
    {
        return static::$plugin_dir;
    }

    /**
     * @return string
     */
    public static function getPluginDirUrl()
    {
        return static::$plugin_dir_url;
    }
}
