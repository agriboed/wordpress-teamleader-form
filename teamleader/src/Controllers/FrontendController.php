<?php

namespace Teamleader\Controllers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Interfaces\HooksInterface;
use Teamleader\Helpers\OptionsHelper;
use Teamleader\Helpers\FieldsHelper;

/**
 * Class Frontend
 * @package Teamleader\Controllers
 */
class FrontendController extends AbstractController implements HooksInterface {
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var array
	 */
	protected $forms;

	/**
	 * @var
	 */
	protected $form;

	/**
	 * @var array
	 */
	protected $fields;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * Set Wordpress hooks
	 */
	public function initHooks() {
		add_shortcode( Container::key(), [ $this, 'processShortcode' ] );
	}

	/**
	 * @param array $atts
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function processShortcode( $atts = array() ) {
		$atts = shortcode_atts( [ 'id' => null ], $atts );

		if ( null === $atts['id'] ) {
			return '';
		}

		$this->id      = (int) $atts['id'];
		$this->forms   = OptionsHelper::getForms();
		$this->options = OptionsHelper::getOptions();

		if ( ! isset( $this->forms[ $this->id ] ) ) {
			return '';
		}

		$this->form = $this->forms[ $this->id ];

		/**
		 * @var $fieldsHelper FieldsHelper
		 */
		$fieldsHelper = $this->container->get( FieldsHelper::class );

		foreach ( $fieldsHelper->getFields() as $key => $field ) {
			if ( ! isset( $this->form[ $key ] ) || $this->form[ $key ]['active'] !== true ) {
				continue;
			}

			$field = [
				'type'     => $field['type'],
				'label'    => isset( $this->form[ $key ]['label'] ) ? $this->form[ $key ]['label'] : $this->fields[ $key ]['title'],
				'value'    => isset( $this->form[ $key ]['default'] ) ? $this->form[ $key ]['default'] : '',
				'required' => true === $this->form[ $key ]['required'] || true === $field['required']
			];

			if ( $this->form[ $key ]['hidden'] ) {
				$field['type'] = 'hidden';
			}

			$this->fields[ $key ] = $field;
		}

		$this->loadAssets();

		return $this->renderForm();
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	protected function renderForm() {
		$fields  = $this->fields;
		$logo    = Container::pluginUrl() . 'assets/images/logo.png';
		$key     = Container::key();
		$id      = $this->id;
		$form    = $this->form;
		$options = $this->options;

		// allows to set template using own template
		if ( file_exists( get_template_directory() . '/teamleader/frontend.php' ) ) {
			$path = get_template_directory() . '/teamleader/frontend.php';
		} else {
			$path = Container::pluginDir() . '/templates/frontend.php';
		}

		if ( ! file_exists( $path ) ) {
			throw new \LogicException( 'Frontend template not found' );
		}

		ob_start();
		include $path;

		return ob_get_clean();
	}

	/**
	 * @return void
	 */
	protected function loadAssets() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( Container::key(), Container::pluginUrl() . 'assets/js/teamleader.js', [ 'jquery' ], Container::version() );

		if ( ! empty( $this->options['recaptcha']['enable'] ) ) {
			wp_enqueue_script( 'google-api', 'https://www.google.com/recaptcha/api.js', null, null, true );
		}

		wp_enqueue_style( Container::key() . '-styles', Container::pluginUrl() . 'assets/css/front.css', null, Container::version() );
	}
}