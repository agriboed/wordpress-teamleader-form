<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Controllers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Interfaces\HooksInterface;
use Teamleader\Helpers\OptionsHelper;
use Teamleader\Helpers\FieldsHelper;

/**
 * Class Frontend
 * @package Teamleader\Controllers
 */
class FrontendController extends AbstractController implements HooksInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var array
     */
    protected $forms;

    /**
     * @var array
     */
    protected $fields;

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
        $atts = shortcode_atts(['id' => null], $atts);

        if (null === $atts['id']) {
            return '';
        }
        $this->id = (int)$atts['id'];
        $this->forms = OptionsHelper::getForms();

        if (!isset($this->forms[$this->id])) {
            return '';
        }

        wp_enqueue_script('jquery');
        wp_enqueue_style(Container::key() . '-styles', Container::pluginUrl() . 'assets/css/front.css');

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
        $options = OptionsHelper::getOptions();
        $form = $this->forms[$this->id];

        if (!empty($options['recaptcha'])) {
            wp_enqueue_script('google-api', 'https://www.google.com/recaptcha/api.js', [], true);
        }

        $this->fields = $fieldsHelper->getFields();
        $logo = Container::pluginUrl() . 'assets/images/logo.png';
        $fields = [];
        $key = Container::key();

        foreach ($this->fields as $key => $field) {
            if (!isset($form[$key]) || $form[$key]['active'] !== true) {
                continue;
            }

            $field = [
                'label' => isset($form[$key]['label']) ? $form[$key]['label'] : $this->fields[$key]['title'],
                'value' => isset($form[$key]['default']) ? $form[$key]['default'] : '',
                'required' => true ===  $form[$key]['required'] || true === $field['required']
            ];

            if ($form[$key]['hidden']) {
                $field['type'] = 'hidden';
            }

            $fields[$key] = $field;
        }

        // allows to set template using own template
        if (file_exists(get_template_directory() . '/teamleader/frontend.php')) {
            $path = get_template_directory() . '/teamleader/frontend.php';
        } else {
            $path = Container::pluginDir() . '/templates/frontend.php';
        }

        if (!file_exists($path)) {
            throw new \LogicException('Frontend template not found');
        }

        ob_start();
        include $path;

        return ob_get_clean();
    }
}