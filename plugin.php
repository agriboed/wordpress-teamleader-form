<?php
/*
Plugin Name: TeamLeader Website Form
Description: Shows form on your website and send data into CRM
Author: AGriboed <alexv1rs@gmail.com>
Contributor: AGriboed <alexv1rs@gmail.com>
Author URI: http://v1rus.ru/
Version: 1.0
*/

if (!class_exists(\TeamLeader\TeamLeader::class)) {
    require __DIR__ . '/src/TeamLeader.php';
    new TeamLeader\TeamLeader;
}