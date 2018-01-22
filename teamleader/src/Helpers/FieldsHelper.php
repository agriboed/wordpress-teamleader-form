<?php

namespace Teamleader\Helpers;

use Teamleader\DependencyInjection\Container;

/**
 * Class Fields
 * @package Teamleader\Helpers
 */
class FieldsHelper extends AbstractHelper {
	/**
	 * @var array
	 */
	protected $fields;

	/**
	 *
	 * @return array
	 * @throws \LogicException
	 */
	public function getFields() {
		if ( null === $this->fields ) {

			if ( ! file_exists( Container::pluginDir() . '/fields/fields.php' ) ) {
				throw new \LogicException( 'Fields file not found' );
			}

			$this->fields = require Container::pluginDir() . '/fields/fields.php';
		}

		return $this->fields;
	}
}