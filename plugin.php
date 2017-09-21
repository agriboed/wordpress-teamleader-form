<?php
/**
 * Plugin Name: TeamLeader Website Form
 * Description: Plugin shows form on your website and allows to send data into CRM
 * Author: AGriboed <alexv1rs@gmail.com>
 * Author URI: http://v1rus.ru/
 * Version: 1.1.0
 */

if (!class_exists(\TeamLeader\TeamLeader::class)) {
    require __DIR__ . '/src/TeamLeader.php';
    new TeamLeader\TeamLeader(__FILE__);
}