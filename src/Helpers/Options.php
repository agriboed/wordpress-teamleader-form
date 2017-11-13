<?php

namespace Teamleader\Helpers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Interfaces\HooksInterface;
use Teamleader\Interfaces\DependencyInterface;

class Options implements DependencyInterface, HooksInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * Options constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->key = $container::getKey();
    }

    /**
     * Set Wordpress hooks
     */
    public function initHooks()
    {
        register_setting($this->key, $this->key . '_webhook');
        register_setting($this->key, $this->key . '_form');
        register_setting($this->key, $this->key . '_fields');
    }

    /**
     * @return string|null
     */
    public function getWebhook()
    {
        return get_option($this->key . '_webhook', null);
    }

    public function getWebhookKey()
    {
        return $this->key . '_webhook';
    }

    /**
     * @return array
     */
    public function getForm()
    {
        return get_option($this->key . '_form', []);
    }

    public function getFormKey()
    {
        return $this->key . '_form';
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return get_option($this->key . '_fields', []);
    }

    public function getFieldsKey()
    {
        return $this->key . '_fields';
    }
}
