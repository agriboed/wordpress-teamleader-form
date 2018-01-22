<?php

namespace Teamleader;

use Teamleader\Interfaces\DependencyInterface;
use Teamleader\DependencyInjection\Container;
use Teamleader\Interfaces\HooksInterface;

/**
 * Class Bootstrap
 * @package Teamleader
 */
class Bootstrap {
	/**
	 * @var string
	 */
	protected $key = 'teamleader';

	/**
	 * @var string
	 */
	protected $version = '1.3.1';

	/**
	 * @var $container Container
	 */
	protected $container;

	/**
	 * @var array
	 */
	protected $autostart = array(
		\Teamleader\Controllers\AdminController::class,
		\Teamleader\Controllers\AjaxController::class,
		\Teamleader\Controllers\FrontendController::class,
		\Teamleader\Helpers\OptionsHelper::class,
		\Teamleader\Helpers\FieldsHelper::class,
	);

	/**
	 * Bootstrap constructor.
	 *
	 * @param $plugin
	 */
	public function __construct( $plugin ) {
		$this->container = new Container( $plugin, $this->key, $this->version );

		foreach ( $this->autostart as $class ) {
			$object = new $class;

			if ( $object instanceof DependencyInterface ) {
				$object->setContainer( $this->container );
			}

			if ( $object instanceof HooksInterface ) {
				$object->initHooks();
			}
		}
	}
}