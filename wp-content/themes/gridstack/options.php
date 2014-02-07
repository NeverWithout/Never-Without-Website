<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 * 
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet (lowercase and without spaces)
	$themename = 'GridStack';
	$themename = preg_replace("/\W/", "", strtolower($themename) );
	
	$optionsframework_settings = get_option('optionsframework');
	$optionsframework_settings['id'] = $themename;
	update_option('optionsframework', $optionsframework_settings);
	
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */

function optionsframework_options() {
	
	$shortname = 'of';
	
	// Pull all the categories into an array
	$options_categories = array();  
	$options_categories_obj = get_categories();
	$options_categories[''] = 'Latest Posts';
	foreach ($options_categories_obj as $category) {
    	$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	// Pull all the pages into an array
	$options_pages = array();  
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Navigation';
	foreach ($options_pages_obj as $page) {
    	$options_pages[$page->ID] = $page->post_title;
	}
		
	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/admin/images/';
		
	// Set the Options Array
$options = array();
$options[] = array( "name" => __("General", "framework"),				 
					"type" => "heading");
		
$options[] = array( "name" => __("Custom Logo", "framework"),
					"desc" => "Upload a logo for your theme, or specify the image address of your online logo. (http://yoursite.com/logo.png).<br /><br /> Image-size should be 300px x 65px.",
					"id" => $shortname."_logo",
					"std" => "",
					"type" => "upload");
					
$options[] = array( "name" => __("Custom Favicon", "framework"),
					"desc" => "Upload a 16px x 16px Png/Gif image that will represent your website's favicon.",
					"id" => $shortname."_custom_favicon",
					"std" => "",
					"type" => "upload"); 

                                               
$options[] = array( "name" => __("Tracking Code", "framework"),
					"desc" => "Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.",
					"id" => $shortname."_google_analytics",
					"std" => "",
					"type" => "textarea");    					
					
$options[] = array( "name" => __("Customize", "framework"),				 
					"type" => "heading"); 			

$options[] = array( "name" => __("Maximum Number of Image Slides per Thumbnail Slideshow", "framework"),
					"desc" => "Keep this as low as you can for memory reasons to keep your load time fast.",
					"id" => $shortname."_thumbnail_number",
					"std" => "3",
					"type" => "text"); 

$options[] = array( "name" => __("Dropdown Menu Text", "framework"),
					"desc" => "Default Text Displayed in the Mobile Dropdown Menu",
					"id" => $shortname."_menu_text",
					"std" => "Navigation",
					"type" => "text");
					
$options[] = array( "name" => __("Custom CSS", "framework"),
                    "desc" => "Quickly add some CSS to your theme by adding it to this block.",
                    "id" => $shortname."_custom_css",
                    "std" => "",
                    "type" => "textarea");

$options[] = array( "name" => __("Homepage", "framework"),				 
					"type" => "heading"); 

$options[] = array( "name" => __("Homepage Thumbnails Slider Autoplay", "framework"),
                    "desc" => "Choose your slideshow autoplay choice.",
                    "id" => $shortname."_home_autoplay",
                    "std" => "true",
                    "type" => "select",
                    "options" =>  array(
                        'true' => 'Autoplay',
                        'false' => "No Autoplay"
                    ));
					
$options[] = array( "name" => __("Number of Homepage Featured Items per Page.", "framework"),
					"desc" => "Enter a number of posts you want to display on the homepage.",
					"id" => $shortname."_home_posts",
					"std" => "10",
					"type" => "text");

$options[] = array( "name" => __("Portfolio", "framework"),				 
					"type" => "heading");

$options[] = array( "name" => __("Portfolio Thumbnails Slider Autoplay", "framework"),
                    "desc" => "Choose your slideshow autoplay choice.",
                    "id" => $shortname."_portfolio_autoplay",
                    "std" => "true",
                    "type" => "select",
                    "options" =>  array(
                        'true' => 'Autoplay',
                        'false' => "No Autoplay"
                    ));
					
$options[] = array( "name" => __("Posts Per Page", "framework"),
					"desc" => "Enter the number of portfolio posts you want to display per page.",
					"id" => $shortname."_portfolio_posts",
					"std" => "6",
					"type" => "text"); 

$options[] = array( "name" => __("Blog", "framework"),				 
					"type" => "heading");


$options[] = array( "name" => __("Blog Style", "framework"),
					"desc" => "Choose your blog style.",
					"id" => $shortname."_blog_style",
					"std" => "threecol",
					"type" => "radio",
					"options" =>  array(
						'onecol' => 'One Column With Sidebar',
						'twocol' => 'Two Columns With Sidebar',
						'threecol' => 'Three Columns'	
					)); 

$options[] = array( "name" => __("Forms", "framework"),			 
					"type" => "heading");

$options[] = array( "name" => __("Contact Email Address", "framework"),
					"desc" => "Type in the email address you want the contact and quote request forms to mail to.",
					"id" => $shortname."_mail_address",
					"std" => "",
					"type" => "text"); 

$options[] = array( "name" => __("Successfully Sent Heading", "framework"),
					"desc" => "Heading for a successfully sent contact or quote form.",
					"id" => $shortname."_sent_heading",
					"std" => "Thank you for your email.",
					"type" => "text"); 

$options[] = array( "name" => __("Successfully Sent Description", "framework"),
					"desc" => "Heading for a successfully sent contact or quote form.",
					"id" => $shortname."_sent_description",
					"std" => "It will be answered as soon as possible.",
					"type" => "text"); 
	
$options[] = array( "name" => __("Basic Spam Question", "framework"),
					"desc" => "Do you want to add a basic spam question to your form?",
					"id" => $shortname."_spam_question",
					"std" => "off",
					"type" => "radio",
					"options" => array(
						'on' => 'On',	
						'off' => 'Off'	
					));	

$options[] = array( "name" => __("Fonts", "framework"),				 
					"type" => "heading");

$options[] = array( "name" => __("Navigation Font", "framework"),
					"desc" => "Font Settings for sitewide fonts excluding the Top Featured Area. For previews, visit <a href='http://www.google.com/webfonts' target='_blank'>The Google Fonts Homepage</a>",
					"id" => $shortname."_nav_font",
					"std" => array('face' => 'Raleway','style' => 'uppercase', 'weight' => '400'),
					"type" => "typography_nosize");
					
$options[] = array( "name" => __("Page Title Font", "framework"),
					"desc" => "Font setting for page titles For previews, visit <a href='http://www.google.com/webfonts' target='_blank'>The Google Fonts Homepage</a>",
					"id" => $shortname."_heading_font",
					"std" => array('face' => 'Raleway','style' => 'normal', 'weight' => '200'),
					"type" => "typography_nosize");
					
$options[] = array( "name" => __("Page Subtitle Font", "framework"),
					"desc" => "Font setting for page subtitles For previews, visit <a href='http://www.google.com/webfonts' target='_blank'>The Google Fonts Homepage</a>",
					"id" => $shortname."_page_subtitle_font",
					"std" => array('face' => 'Raleway','style' => 'normal', 'weight' => '300'),
					"type" => "typography_nosize");
					
$options[] = array( "name" => __("Content Area Heading Font", "framework"),
					"desc" => "Font Settings for sitewide portfolios and blogs. Blog titles and other secondary heading fonts. For previews, visit <a href='http://www.google.com/webfonts' target='_blank'>The Google Fonts Homepage</a>",
					"id" => $shortname."_content_heading_font",
					"std" => array('face' => 'Raleway','style' => 'normal', 'weight' => '300'),
					"type" => "typography_nosize");
					
$options[] = array( "name" => __("Content Area Subheading Font", "framework"),
					"desc" => "Font Settings for sitewide headings in WYSIWYG areas. For previews, visit <a href='http://www.google.com/webfonts' target='_blank'>The Google Fonts Homepage</a>",
					"id" => $shortname."_content_subheading_font",
					"std" => array('face' => 'Raleway','style' => 'uppercase', 'weight' => '700'),
					"type" => "typography_nosize");

$options[] = array( "name" => __("Caption Font", "framework"),
					"desc" => "Font Settings for sitewide fonts excluding the Top Featured Area. For previews, visit <a href='http://www.google.com/webfonts' target='_blank'>The Google Fonts Homepage</a>",
					"id" => $shortname."_captions",
					"std" => array('face' => 'Raleway','style' => 'uppercase', 'weight' => '700'),
					"type" => "typography_nosize");
					
$options[] = array( "name" => __("Button Font", "framework"),
					"desc" => "Font Settings for sitewide buttons. Smaller portfolio titles, comment section titles and other tertiary headings. For previews, visit <a href='http://www.google.com/webfonts' target='_blank'>The Google Fonts Homepage</a>",
					"id" => $shortname."_button_font",
					"std" => array('face' => 'Raleway','style' => 'uppercase', 'weight' => '300'),
					"type" => "typography_nosize");

$options[] = array( "name" => __("Tiny Details Font", "framework"),
					"desc" => "Font Settings for sitewide fonts excluding the Top Featured Area. For previews, visit <a href='http://www.google.com/webfonts' target='_blank'>The Google Fonts Homepage</a>",
					"id" => $shortname."_tiny_font",
					"std" => array('face' => 'Raleway','style' => 'uppercase', 'weight' => '700'),
					"type" => "typography_nosize");

$options[] = array( "name" => __("Paragraphs and Body Font", "framework"),
					"desc" => "Font Settings for sitewide fonts excluding the Top Featured Area. For previews, visit <a href='http://www.google.com/webfonts' target='_blank'>The Google Fonts Homepage</a>",
					"id" => $shortname."_p_font",
					"std" => array('face' => 'Raleway','style' => 'normal', 'weight' => '400'),
					"type" => "typography_nosize");	

$options[] = array( "name" => __("Latin/Cyrillic Character Support", "framework"),
					"desc" => "Select whether you want Latin/Cyrillic characters in your fonts. Note that some Google fonts don't have these characters, so you'll need to choose ones that do.",
					"id" => $shortname."_cyrillic_chars",
					"std" => "No",
					"type" => "radio",
					"options" =>  array(
						'No' => 'No',
						'Yes' => 'Yes'
						));		

$options[] = array( "name" => __("Ajax", "framework"),				 
					"type" => "heading");

$options[] = array( "name" => __("Ajax Page Loading", "framework"),
					"desc" => "Turn on/off ajax page loading.",
					"id" => $shortname."_ajaxify",
					"std" => "on",
					"type" => "radio",
					"options" => array(
						'on' => 'On',	
						'off' => 'Off'	
					));

$options[] = array( "name" => __("Updates", "framework"),				 
					"type" => "heading");

$options[] = array( "name" => __("Themeforest Username", "framework"),
					"desc" => "Enter your Themeforest Username that you used to purchase the this theme.",
					"id" => $shortname."_tf_username",
					"std" => "",
					"type" => "text"); 

$options[] = array( "name" => __("Themeforest API Key", "framework"),
					"desc" => "You can find your API key by Logging into Themeforest, visiting your Dashboard page then clicking the My Settings tab. At the bottom of the page you will find your account API key and a button to regenerate it as needed.",
					"id" => $shortname."_tf_api",
					"std" => "",
					"type" => "text"); 
/* Coming Soon
$options[] = array( "name" => __("Browser Specific Ajax", "framework"),
					"desc" => "Turn on/off ajax page loading for specific browsers. Uncheck to turn off.",
					"id" => $shortname."_ajaxify_browsers",
					"std" => array(
						'chrome' => '1',	
						'ie' => '1',
						'firefox' => '1',
						'safari' => '1',
						'opera' => '1',
						'android' => '1',
						'iphone' => '1',

					),
					"type" => "multicheck",
					"options" => array(
						'chrome' => 'Chrome',	
						'ie' => 'Internet Explorer',
						'firefox' => 'Firefox',
						'safari' => 'Safari',
						'opera' => 'Opera',
						'android' => 'Android',
						'iphone' => 'iPhone/iPad',

					)); */

	return $options;
}