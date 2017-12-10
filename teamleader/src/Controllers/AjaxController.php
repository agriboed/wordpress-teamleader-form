<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Controllers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Helpers\FormsHelper;
use Teamleader\Interfaces\AjaxInterface;
use Teamleader\Interfaces\HooksInterface;
use Teamleader\Helpers\OptionsHelper;
use Teamleader\Helpers\FieldsHelper;
use ReCaptcha\ReCaptcha;
use Curl\Curl;

/**
 * Class AjaxHandler
 * @package Teamleader\Controllers
 */
class AjaxController extends AbstractController implements HooksInterface, AjaxInterface
{
    protected $data;

    /**
     * Set Wordpress hooks
     */
    public function initHooks()
    {
        add_action('wp_ajax_' . Container::key(), [$this, 'ajaxHandler']);
        add_action('wp_ajax_nopriv_' . Container::key(), [$this, 'ajaxHandler']);

        add_action('wp_ajax_teamleader_options', [$this, 'saveOptions']);
        add_action('wp_ajax_teamleader_create', [$this, 'createForm']);
    }

    /**
     * @param $nonce
     * @return bool
     */
    protected function checkNonce($nonce)
    {
        return wp_create_nonce('teamleader') === $nonce;
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
    }

    /**
     * @return array
     * @throws \ReflectionException
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
        $fieldsHelper = $this->container->get(FieldsHelper::class);
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
     * Store plugin options in database
     *
     */
    public function saveOptions()
    {
        $this->data['success'] = false;

        if (!isset($_POST['nonce']) || false === $this->checkNonce($_POST['nonce'])) {
            $this->data['message'] = __('Security Error', Container::key());
            return $this->renderJson();
        }

        if (empty($_POST['webhook'])) {
            $this->data['message'] = __('Webhook is empty', Container::key());
            return $this->renderJson();
        }

        $options = [
            'webhook' => sanitize_text_field($_POST['webhook']),
            'logo' => isset($_POST['logo']) ? true : false,
            'referral' => !empty($_POST['referral']) ? sanitize_text_field($_POST['referral']) : null,
            'recaptcha' => [
                'enable' => isset($_POST['recaptcha']['enable']) ? true : false,
                'key' => !empty($_POST['recaptcha']['key']) ? sanitize_text_field($_POST['recaptcha']['key']) : null,
                'secret' => !empty($_POST['recaptcha']['secret']) ? sanitize_text_field($_POST['recaptcha']['secret']) : null,
            ]
        ];

        if (empty($options['recaptcha']['key']) || empty($options['recaptcha']['secret'])) {
            $options['recaptcha']['enabled'] = false;
        }

        OptionsHelper::setOptions($options);

        $this->data['success'] = true;
        $this->data['message'] = __('Settings saved', Container::key());
        return $this->renderJson();
    }

    /**
     *
     * @throws \Exception
     */
    public function createForm()
    {
        $this->data['success'] = false;

        if (!isset($_POST['nonce']) || false === $this->checkNonce($_POST['nonce'])) {
            $this->data['message'] = __('Security Error', Container::key());
            return $this->renderJson();
        }

        if (empty($_POST['form'])) {
            $this->data['message'] = __('Form is empty', Container::key());
            return $this->renderJson();
        }

        /**
         * @var $formsHelper FormsHelper
         */
        $formsHelper = $this->container->get(FormsHelper::class);
        try {
            $form_id = $formsHelper->createForm($_POST);
            $this->data['form_id'] = $form_id;
            $this->data['success'] = true;
            $this->data['message'] = __('Form created', Container::key());
        } catch (\LogicException $exception) {
            $this->data['message'] = __('System error', Container::key());
        }

        return $this->renderJson();
    }

    /**
     *
     */
    public function renderJson()
    {
        echo json_encode($this->data);
        wp_die();
    }
}