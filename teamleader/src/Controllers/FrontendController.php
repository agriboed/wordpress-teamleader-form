<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Controllers;

use Teamleader\Helpers\FieldsHelper;
use Teamleader\Interfaces\HooksInterface;
use Teamleader\DependencyInjection\Container;
use Teamleader\Helpers\OptionsHelper;

/**
 * Class Frontend
 * @package Teamleader\Controllers
 */
class FrontendController extends AbstractController implements HooksInterface
{
    /**
     * Set Wordpress hooks
     */
    public function initHooks()
    {
        add_shortcode(Container::key(), [$this, 'processShortcode']);
    }

    /**
     * @param array $atts
     *
     * @return string
     * @throws \Exception
     */
    public function processShortcode($atts = array())
    {
        $atts = shortcode_atts([], $atts);

        /**
         * @var $optionsHelper OptionsHelper
         */
        $optionsHelper = $this->container->get(OptionsHelper::class);

        if (null === $optionsHelper->getWebhook()) {
            return '';
        }

        wp_enqueue_script('jquery');
        wp_enqueue_style(Container::key() . '-styles', Container::pluginUrl() . 'assets/css/styles.css');

        return $this->renderForm();
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function renderForm()
    {
        /**
         * @var $fieldsHelper FieldsHelper
         */
        $fieldsHelper = $this->container->get(FieldsHelper::class);

        /**
         * @var $optionsHelper OptionsHelper
         */
        $optionsHelper = $this->container->get(OptionsHelper::class);

        $form = $optionsHelper->getForm();

        if ( ! empty($form['recaptcha'])) {
            wp_enqueue_script('google-api', 'https://www.google.com/recaptcha/api.js', [], true);
        }

        $fields         = $fieldsHelper->getFields();
        $fields_options = $optionsHelper->getFields();

        $form['submit']  = ! empty($form['submit']) ? $form['submit'] : __('Submit', Container::key());
        $form['success'] = ! empty($form['success']) ? $form['success'] : __('Thank you!', Container::key());

        $logo = Container::pluginUrl() . 'assets/images/logo.png';

        // allows to set template using own template
        if (file_exists(get_template_directory() . '/teamleader/frontend.php')) {
            $path = get_template_directory() . '/teamleader/frontend.php';
        } else {
            $path = Container::pluginDir() . '/templates/frontend.php';
        }

        if ( ! file_exists($path)) {
            throw new \LogicException('Frontend template not found');
        }

        ob_start();
        include $path;

        return ob_get_clean();
    }
}