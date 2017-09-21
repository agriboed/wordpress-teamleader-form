<?php
/**
 * Plugin Name: Teamleader Form Integration
 * Description: Plugin shows form on your website and allows to send data into CRM
 * Author: AGriboed <alexv1rs@gmail.com>
 * Author URI: http://v1rus.ru/
 * Version: 1.1.1
 */

if (!class_exists(\Teamleader\Teamleader::class)) {
    require __DIR__ . '/src/Teamleader.php';

    new Teamleader\Teamleader(__FILE__);
}
