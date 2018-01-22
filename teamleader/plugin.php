<?php
/**
 * Plugin Name: Teamleader Form Integration
 * Description: Plugin provide integration between your website and Teamleader CRM
 * Author: AGriboed <alexv1rs@gmail.com>
 * Author URI: https://github.com/agriboed/wp-teamleader-integration
 * Version: 1.3.1
 */

require __DIR__ . '/vendor/autoload.php';

try {
	new \Teamleader\Bootstrap( __FILE__ );
} catch ( Exception $e ) {
}