<?php

namespace Teamleader\Controllers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Helpers\Fields;
use Teamleader\Helpers\Options;
use Teamleader\Interfaces\DependencyInterface;
use Teamleader\Interfaces\HooksInterface;
use Curl\Curl;
use ReCaptcha\ReCaptcha;

/**
 * Class AjaxHandler
 * @package Teamleader\Controllers
 */
class AjaxHandler implements DependencyInterface, HooksInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $basename;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->key = $container::getKey();
        $this->basename = $container::getBasename();
    }

    /**
     * Set Wordpress hooks
     */
    public function initHooks()
    {
        add_action('wp_ajax_' . $this->key, [$this, 'ajaxHandler']);
        add_action('wp_ajax_nopriv_' . $this->key, [$this, 'ajaxHandler']);
    }

    public function ajaxHandler()
    {
        /**
         * @var $optionsHelper Options
         */
        $optionsHelper = $this->container->getContainer(Options::class);

        $data = $this->processFields();

        $formOptions = $optionsHelper->getForm();

        if (!empty($formOptions['recaptcha'] && !empty($formOptions['recaptcha_secret_key']))) {
            $recaptcha = new ReCaptcha($formOptions['recaptcha_secret_key']);
            $resp = $recaptcha->verify($_POST['g-recaptcha-response']);

            if (false === $resp->isSuccess()) {
                $this->setResponse(false, $resp->getErrorCodes());
                wp_die();
            }
        }

        try {
            $curl = new Curl();
            $curl->post($optionsHelper->getWebhook(), $data);

        } catch (\LogicException $exception) {
            $this->setResponse(false, $exception->getMessage());
            wp_die();
        }

        $this->setResponse(true);

        wp_die();

        return null;
    }

    /**
     * @return array
     * @throws \LogicException
     */
    protected function processFields()
    {
        /**
         * @var $optionsHelper Options
         */
        $optionsHelper = $this->container->getContainer(Options::class);

        /**
         * @var $fieldsHelper Fields
         */
        $fieldsHelper = $this->container->getContainer(Fields::class);
        $fields = $fieldsHelper->getFields();
        $fields_options = $optionsHelper->getFields();

        $data = [];

        foreach ($fields as $key => $field) {
            $value = isset($_POST['data'][$key]) ? $_POST['data'][$key] : null;
            $data[$key] = !empty($fields_options[$key]['default']) ? $fields_options[$key]['default'] : $value;
        }

        return $data;
    }

    /**
     * @param bool $success
     * @param string $message
     */
    protected function setResponse($success = false, $message = '')
    {
        echo json_encode(['success' => $success, 'message' => $message]);
    }
}
