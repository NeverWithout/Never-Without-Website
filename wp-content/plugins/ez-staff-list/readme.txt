=== Plugin Name ===

Contributors: codifyllc
Plugin Name: EZ Staff List
Plugin URI: http://thecodify.com/wordpress-plugins/ez-staff-list/
Tags: staff, staff list, employee list, employee
Author URI: http://thecodify.com
Author: Lucas Hoezee - Codify, LLC
Requires at least: 2.3
Tested up to: 3.4.2
Stable tag: 0.7
Version: 0.7

This plugin gives you the ability to easily list your staff members

== Description ==

This plugin gives you the ability to easily list your staff members. For most people, trying to format a list of employees via a WYSIWYG is a hard task. Now, using a simple shortcode *[staff_list]* will solve your problem!

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place [staff_list] in any of your posts or pages

== Changelog ==

= 0.7 =
* UPDATED - Changed a few function names in the frameworks plugin directory. Some of the function names were too generic and other theme developers were not checking if those generic functions were already created. One of them being: button()

= 0.6 =
* UPDATED - Updated main file to have session_start() which fixes bug for some environments.
* UPDATED - Last updated did not include the framework default XSL templates for some reason.

= 0.5 =
* UPDATED - Updated Framework Version
* NEW - Choose number of columns to display staff under the "Layout Options" menu
* NEW - Added color pickers for background color, border color, and font color under the "Layout Options" menu.
* NEW - Added "Update Order" message

= 0.4 =
* NEW - Added functionality to not display the the hours for a staff member. Just leave the field blank to not show on the site
* FIXED - Fixed bug when trying to remove staff members

= 0.3 =
* NEW - Added in an ordering by option to order your list
* FIXED - Fixed issue with removing slashes when using quotation marks in Title and Name of person

= 0.2 =
* Fixed the delete user option.  This was an "oops" :)

= 0.1 =
* First launch

== Screenshots ==
1. The basic form for adding a new staff member
2. A list of staff members which can be deleted or selected to edit
3. Layout options to control number of columns and color
4. Example of the default layout settings when shown on your site
5. The color picker used when changing your colors in the Layout options page