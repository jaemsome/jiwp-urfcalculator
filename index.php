<?php

/*
Plugin Name: KMI urf calculator
Plugin URI: 
Description: Plugin to manage the calculation of ultrasonic range.
Author: KMI
Version: 1.0
Author URI: 
*/

if(!defined('ABSPATH')) exit; // Exit if accessed directly

require_once 'urf-calculator.php';

register_activation_hook(__FILE__, 'kmi_activate_urf_calculator');

function kmi_activate_urf_calculator()
{
    error_log('KMI urf calculator activated.');
}

register_deactivation_hook(__FILE__, 'kmi_deactivate_urf_calculator');

function kmi_deactivate_urf_calculator()
{
    error_log('KMI urf calculator deactivated.');
}