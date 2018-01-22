<?php

namespace Teamleader\Controllers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Interfaces\HooksInterface;
use Teamleader\Interfaces\AjaxInterface;
use Teamleader\Helpers\OptionsHelper;
use Teamleader\Helpers\FormsHelper;
use ReCaptcha\ReCaptcha;
use Curl\Curl;

/**
 * Class AjaxHandler
 * @package Teamleader\Controllers
 */
class AjaxController extends AbstractController implements HooksInterface, AjaxInterface {

	/**
	 * @var
	 */
	protected $data;

	/**
	 * Set Wordpress hooks
	 */
	public function initHooks() {
		add_action( 'wp_ajax_' . Container::key(), [ $this, 'frontHandler' ] );
		add_action( 'wp_ajax_nopriv_' . Container::key(), [ $this, 'frontHandler' ] );

		add_action( 'wp_ajax_teamleader_get', [ $this, 'getForm' ] );
		add_action( 'wp_ajax_teamleader_save', [ $this, 'saveForm' ] );
		add_action( 'wp_ajax_teamleader_options', [ $this, 'saveOptions' ] );
		add_action( 'wp_ajax_teamleader_create', [ $this, 'createForm' ] );
		add_action( 'wp_ajax_teamleader_delete', [ $this, 'deleteForm' ] );
	}

	/**
	 * @return bool
	 */
	protected function checkNonce() {
		if ( ! isset( $_POST['nonce'] ) ) {
			$this->data['message'] = __( 'Security Error', Container::key() );

			return false;
		}

		if ( wp_create_nonce( 'teamleader' ) !== $_POST['nonce'] ) {
			$this->data['message'] = __( 'Security Error', Container::key() );

			return false;
		}

		return true;
	}

	/**
	 * @throws \Exception
	 */
	public function frontHandler() {
		$this->data['success'] = false;
		$options               = OptionsHelper::getOptions();

		if ( ! empty( $options['recaptcha']['enable'] ) && ! empty( $options['recaptcha']['secret'] ) ) {
			$recaptcha = new ReCaptcha( $options['recaptcha']['secret'] );
			$resp      = $recaptcha->verify( $_POST['g-recaptcha-response'] );

			if ( false === $resp->isSuccess() ) {
				$this->data['message'] = __( 'Recaptcha invalid', Container::key() );
				return $this->renderJson();
			}
		}

		if ( null === $_POST['nonce'] || false === wp_verify_nonce( $_POST['nonce'], Container::key() ) ) {
			$this->data['message'] = __( 'Security error. Please, reload the page', Container::key() );

			return $this->renderJson();
		}

		try {
			$curl = new Curl();
			$curl->post( $options['webhook'], $_POST );
			$this->data['success'] = true;
			$this->data['message'] = __( 'Data sent', Container::key() );

		} catch ( \LogicException $exception ) {
			$this->data['message'] = $exception->getMessage();
		}

		return $this->renderJson();
	}

	/**
	 * Store plugin options in database
	 *
	 */
	public function saveOptions() {
		$this->data['success'] = false;

		if ( false === $this->checkNonce() ) {
			return $this->renderJson();
		}

		if ( empty( $_POST['webhook'] ) ) {
			$this->data['message'] = __( 'Webhook is empty', Container::key() );

			return $this->renderJson();
		}

		OptionsHelper::setOptions( $_POST );
		$this->data['success'] = true;
		$this->data['message'] = __( 'Settings saved', Container::key() );

		return $this->renderJson();
	}

	/**
	 *
	 * @throws \ReflectionException
	 * @throws \LogicException
	 */
	public function getForm() {
		$this->data['success'] = false;

		if ( ! isset( $_POST['id'] ) || false === $this->checkNonce() ) {
			return $this->renderJson();
		}

		/**
		 * @var $formsHelper FormsHelper
		 */
		$formsHelper = $this->container->get( FormsHelper::class );
		$form        = $formsHelper->getForm( (int) $_POST['id'] );

		if ( null === $form ) {
			$this->data['message'] = __( 'Form not found' );

			return $this->renderJson();
		}

		$this->data['success'] = true;
		$this->data['form']    = $form;

		return $this->renderJson();
	}

	/**
	 *
	 * @throws \ReflectionException
	 * @throws \Exception
	 * @throws \LogicException
	 */
	public function saveForm() {
		$this->data['success'] = false;

		if ( false === $this->checkNonce() ) {
			return $this->renderJson();
		}

		if ( empty( $_POST['form'] ) && ! isset( $_POST['id'] ) ) {
			$this->data['message'] = __( 'Form is empty', Container::key() );

			return $this->renderJson();
		}

		/**
		 * @var $formsHelper FormsHelper
		 */
		$formsHelper = $this->container->get( FormsHelper::class );

		try {
			$this->data['id']      = $formsHelper->updateForm( (int) $_POST['id'], $_POST );
			$this->data['success'] = true;
			$this->data['message'] = __( 'Form saved', Container::key() );
		} catch ( \LogicException $exception ) {
			$this->data['message'] = __( 'System error', Container::key() );
		}

		return $this->renderJson();
	}

	/**
	 * @throws \Exception
	 */
	public function createForm() {
		$this->data['success'] = false;

		if ( false === $this->checkNonce() ) {
			return $this->renderJson();
		}

		if ( empty( $_POST['form'] ) ) {
			$this->data['message'] = __( 'Form is empty', Container::key() );

			return $this->renderJson();
		}

		/**
		 * @var $formsHelper FormsHelper
		 */
		$formsHelper = $this->container->get( FormsHelper::class );

		try {
			$this->data['id']      = $formsHelper->createForm( $_POST );
			$this->data['success'] = true;
			$this->data['message'] = __( 'Form created', Container::key() );
		} catch ( \LogicException $exception ) {
			$this->data['message'] = __( 'System error', Container::key() );
		}

		return $this->renderJson();
	}

	/**
	 *
	 * @throws \ReflectionException
	 * @throws \LogicException
	 */
	public function deleteForm() {
		$this->data['success'] = false;

		if ( false === $this->checkNonce() ) {
			return $this->renderJson();
		}

		if ( ! isset( $_POST['id'] ) ) {
			$this->data['message'] = __( 'Nothing to delete', Container::key() );

			return $this->renderJson();
		}

		/**
		 * @var $formsHelper FormsHelper
		 */
		$formsHelper = $this->container->get( FormsHelper::class );
		$formsHelper->deleteForm( (int) $_POST['id'] );

		$this->data['success'] = true;
		$this->data['message'] = __( 'Form was deleted', Container::key() );

		return $this->renderJson();
	}

	/**
	 *
	 */
	public function renderJson() {
		echo json_encode( $this->data );
		wp_die();
	}
}