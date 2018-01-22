<?php

namespace Teamleader\Controllers;

use Teamleader\Interfaces\DependencyInterface;
use Teamleader\DependencyInjection\Container;

/**
 * Class ControllerAbstract
 * @package Teamleader\Controllers
 */
abstract class AbstractController implements DependencyInterface {
	/**
	 * @var $container Container
	 */
	protected $container;

	/**
	 * @param Container $container
	 */
	public function setContainer( Container $container ) {
		$this->container = $container;
	}
}
