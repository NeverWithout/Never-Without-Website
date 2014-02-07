<?php
/*
Plugin Name: EZ Staff List
Plugin URI: http://www.thecodify.com
Description: This plugin gives you the ability to easily list your staff members. For most people, trying to format a list of employees via a WYSIWYG is a hard task. Now, using a simple shortcode [staff_list] will solve your problem!
Version: 0.7
Author: Lucas Hoezee - TheCodify, LLC


This plugin gives you the ability to easily list your staff members in wordpress
Copyright (C) 2011 Codify, LLC (Lucas Hoezee)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

/**
* Start the framework object
*/
// Start the session.. Fixes bugs for some enviornments...
session_start();
if (!class_exists('phplitefw_controller'))
{
    include(dirname(__FILE__) . '/framework/phplitefw.inc.php');
}
$pco = new phplitefw_controller();
$pco->load_form_engine();
$pco->load_db_config(dirname(__FILE__) . "/db_config.inc.php", true);
$pco->load_db_engine();
$pco->set_plugin_folder(dirname(__FILE__) . "/plugins");
$pco->load_plugin('content_gen');
$pco->load_plugin('xhtml_gen');
$pco->load_plugin("ssv");
$pco->load_plugin('pagination');
$pco->load_plugin('staff_management');

/**
* Register the stylesheet
*/
wp_register_style( 'staff_management_styles', WP_PLUGIN_URL . '/ez-staff-list/ez-staff-list.css');
wp_enqueue_style( 'staff_management_styles', WP_PLUGIN_URL . '/ez-staff-list/ez-staff-list.css');

wp_register_style( 'ezstaff_jpicker_min', WP_PLUGIN_URL . '/ez-staff-list/js/jpicker-1.1.6/css/jPicker-1.1.6.css');
wp_enqueue_style( 'ezstaff_jpicker_min', WP_PLUGIN_URL . '/ez-staff-list/js/jpicker-1.1.6/css/jPicker-1.1.6.css');

wp_register_style( 'ezstaff_jpicker', WP_PLUGIN_URL . '/ez-staff-list/js/jpicker-1.1.6/jPicker.css');
wp_enqueue_style( 'ezstaff_jpicker', WP_PLUGIN_URL . '/ez-staff-list/js/jpicker-1.1.6/jPicker.css');

/**
* Register and load the javascript
*/
wp_enqueue_script('jquery');
wp_enqueue_script('ezstaff_jpicker_js', WP_PLUGIN_URL . '/ez-staff-list/js/jpicker-1.1.6/jpicker-1.1.6.js');
wp_enqueue_script('ezstaff_main_js', WP_PLUGIN_URL . '/ez-staff-list/js/main.js');

/**
* Start the Staff Management Class
*/
if (class_exists("staff_mgt"))
{
    $staff_mgt = new staff_mgt();
    add_action('admin_menu', array($staff_mgt,'ConfigureMenu'));

    add_action('admin_head', array($staff_mgt,'ajax_it'));
    add_action('wp_ajax_staff_mgt', array($staff_mgt,'staff_mgt_ajax_callback'));

    
    add_shortcode( 'staff_list', array($staff_mgt,'staff_list') );
}

?>