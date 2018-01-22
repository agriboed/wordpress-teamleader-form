<?php

namespace Teamleader\Helpers;

use Teamleader\Interfaces\DependencyInterface;
use Teamleader\DependencyInjection\Container;

/**
 * Class AbstractHelper
 * @package teamleader\src\Helpers
 */
abstract class AbstractHelper implements DependencyInterface {
	/**
	 * @var $container Container
	 */
	protected $container;

	public function setContainer( Container $container ) {
		$this->container = $container;
	}
}