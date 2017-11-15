<?php
/**
 * Plugin Name: Teamleader Form Integration
 * Description: Plugin shows form on your website and allows to send data into CRM
 * Author: AGriboed <alexv1rs@gmail.com>
 * Author URI: http://v1rus.ru/
 * Version: 1.2.1
 */

require __DIR__ . '/vendor/autoload.php';

$dependencies = array(
    \Teamleader\Controllers\Admin::class,
    \Teamleader\Controllers\AjaxHandler::class,
    \Teamleader\Controllers\Frontend::class,
    \Teamleader\Helpers\Options::class,
    \Teamleader\Helpers\Fields::class
);

new \Teamleader\DependencyInjection\Container('teamleader', __FILE__, $dependencies);