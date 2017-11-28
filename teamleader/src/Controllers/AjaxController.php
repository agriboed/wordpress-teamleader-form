<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Controllers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Helpers\FieldsHelper;
use Teamleader\Helpers\OptionsHelper;
use Teamleader\Interfaces\HooksInterface;
use Curl\Curl;
use ReCaptcha\ReCaptcha;

/**
 * Class AjaxHandler
 * @package Teamleader\Controllers
 */
class AjaxController extends AbstractController implements HooksInterface
{
    /**
     * Set Wordpress hooks
     */
    public function initHooks()
    {
        add_action('wp_ajax_' . Container::key(), [$this, 'ajaxHandler']);
        add_action('wp_ajax_nopriv_' . Container::key(), [$this, 'ajaxHandler']);
    }

    /**
     * @throws \Exception
     */
    public function ajaxHandler()
    {
        /**
         * @var $optionsHelper OptionsHelper
         */
        $optionsHelper = $this->container->get(OptionsHelper::class);
        $data          = $this->processFields();
        $formOptions   = $optionsHelper->getForm();

        if ( ! empty($formOptions['recaptcha'] && ! empty($formOptions['recaptcha_secret_key']))) {
            $recaptcha = new ReCaptcha($formOptions['recaptcha_secret_key']);
            $resp      = $recaptcha->verify($_POST['g-recaptcha-response']);

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
    }

    /**
     * @return array
     * @throws \Exception
     * @throws \LogicException
     */
    protected function processFields()
    {
        /**
         * @var $optionsHelper OptionsHelper
         */
        $optionsHelper = $this->container->get(OptionsHelper::class);

        /**
         * @var $fieldsHelper FieldsHelper
         */
        $fieldsHelper   = $this->container->get(FieldsHelper::class);
        $fields         = $fieldsHelper->getFields();
        $fields_options = $optionsHelper->getFields();

        $data = [];

        foreach ($fields as $key => $field) {
            $value      = isset($_POST['data'][$key]) ? $_POST['data'][$key] : null;
            $data[$key] = ! empty($fields_options[$key]['default']) ? $fields_options[$key]['default'] : $value;
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
