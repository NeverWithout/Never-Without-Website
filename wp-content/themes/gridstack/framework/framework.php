<?php

/*-----------------------------------------------------------------------------------*/
/* 
/*  Theme Framework Functions
/*
/*-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Options Framework For The Theme
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'optionsframework_init' ) ) {
	/* Set the file path based on whether the Options Framework Theme is a parent theme or child theme */
	if ( get_stylesheet_directory() == get_template_directory() ) {
		define('OPTIONS_FRAMEWORK_URL', get_template_directory() . '/framework/admin/');
		define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/framework/admin/');
	} else {
		define('OPTIONS_FRAMEWORK_URL', get_template_directory() . '/framework/admin/');
		define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/framework/admin/');
	}
	require_once (OPTIONS_FRAMEWORK_URL . 'options-framework.php');
}

/*-----------------------------------------------------------------------------------*/
/* Add Visual Editor Style
/*-----------------------------------------------------------------------------------*/
add_editor_style();

/*-----------------------------------------------------------------------------------*/
/*	Include Multi-Post Thumbnails
/*-----------------------------------------------------------------------------------*/
include( get_template_directory() . '/framework/framework-functions/multi-post-thumbnails.php');

/*-----------------------------------------------------------------------------------*/
/* Add Contextual Help
/*-----------------------------------------------------------------------------------*/
// include( get_template_directory() . '/functions/help/contextual-help.php'); // Coming soon

/*-----------------------------------------------------------------------------------*/
/*	Include Drag and Drop Slide Order Functionality
/*-----------------------------------------------------------------------------------*/
include( get_template_directory() . '/framework/framework-functions/drag-drop-order.php');

/*-----------------------------------------------------------------------------------*/
/*	Load Text Domain
/*-----------------------------------------------------------------------------------*/
function theme_init(){
    load_theme_textdomain('framework', get_template_directory() . '/lang');
}
add_action ('init', 'theme_init');


/*-----------------------------------------------------------------------------------*/
/* Add Embed Code to Footer
/*-----------------------------------------------------------------------------------*/
function ag_embed_code() { ?>
<!-- Google Analytics Code
  ================================================== -->
<?php echo of_get_option('of_google_analytics'); }

add_action( 'wp_footer', 'ag_embed_code', 1000 );


/*-----------------------------------------------------------------------------------*/
/*	Automatic Feed Links
/*-----------------------------------------------------------------------------------*/
if(function_exists('add_theme_support')) {
    add_theme_support('automatic-feed-links'); //WP Auto Feed Links
}

/*-----------------------------------------------------------------------------------*/
/*	Configure Excerpt String, Remove Automatic Periods
/*-----------------------------------------------------------------------------------*/
function ag_excerpt_more($excerpt) {
	return str_replace('[...]', '...', $excerpt); 
}
add_filter('wp_trim_excerpt', 'ag_excerpt_more');

/*-----------------------------------------------------------------------------------*/
/*	Add Browser Detection Body Class
/*-----------------------------------------------------------------------------------*/
add_filter('body_class','ag_browser_body_class');

function ag_browser_body_class($classes) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[] = 'ie';
	else $classes[] = 'unknown';

	if($is_iphone) $classes[] = 'iphone';
	return $classes;
}

/*-----------------------------------------------------------------------------------*/
/*	Comment Reply Javascript Action
/*-----------------------------------------------------------------------------------*/
function ag_enqueue_comment_reply() {
    // on single blog post pages with comments open and threaded comments
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) { 
        // enqueue the javascript that performs in-link comment reply fanciness
        wp_enqueue_script( 'comment-reply' ); 
    }
}
// Hook into wp_enqueue_scripts
add_action( 'wp_enqueue_scripts', 'ag_enqueue_comment_reply' );

/*-----------------------------------------------------------------------------------*/
/*	Add Widget Shortcode Support
/*-----------------------------------------------------------------------------------*/
add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

/*-----------------------------------------------------------------------------------*/
/*	Add deconstructed URI as <body> classes in Admin
/*-----------------------------------------------------------------------------------*/

function add_to_admin_body_class($classes) {
	// get the global post variable
	global $post;
	$post_type = '';
	
	// instantiate, should be overwritten
	$mode = '';
	// get the current page's URI (the part /after/ your domain name)
	$uri = $_SERVER["REQUEST_URI"];
	// get the post type from WP
	if($post) {
		$post_type = get_post_type($post->ID);
	}
	// set the $mode variable to reflect the editorial /list/ page...
	if (strstr($uri,'edit.php')) {
		$mode = 'edit-list-';
	}
	// or the actual editor page
	if (strstr($uri,'post.php')) {
		$mode = 'edit-page-';
	}
	// append our new mode/post_type class to any existing classes
	if ($post_type) {
		$classes .= $mode . $post_type;
	} else {
		$classes .= '';
	}
	// and send them back to WP
	return $classes;
}
	
// add this filter to the admin_body_class hook
add_filter('admin_body_class', 'add_to_admin_body_class');

/*-----------------------------------------------------------------------------------*/
/* Add HTTP to links function
/*-----------------------------------------------------------------------------------*/
function ag_addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

/*-----------------------------------------------------------------------------------*/
/* Remove Dimensions from Thumbnails (for responsivity) and Gallery
/*-----------------------------------------------------------------------------------*/

function remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}

add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10, 3 );


/*-----------------------------------------------------------------------------------*/
/* Remove More Link Jump
/*-----------------------------------------------------------------------------------*/

function ag_remove_more_jump_link($link) { 
	$offset = strpos($link, '#more-');
	if ($offset) { $end = strpos($link, '"',$offset); }
	if ($end) { $link = substr_replace($link, '', $offset, $end-$offset); }
	return $link;
}
add_filter('the_content_more_link', 'ag_remove_more_jump_link');

/*-----------------------------------------------------------------------------------*/
/* Get Attachment ID from the source
/*-----------------------------------------------------------------------------------*/

function get_attachment_id_from_src ($image_src) {
	global $wpdb;
	$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
	$id = $wpdb->get_var($query);
	return $id;
}
	
/*-----------------------------------------------------------------------------------*/
/* Wrap All Read More Tags In A Span
/*-----------------------------------------------------------------------------------*/	
function wrap_readmore($more_link)
{
	return '<span class="more-link">'.$more_link.'</span>';
}
add_filter('the_content_more_link', 'wrap_readmore', 10, 1);

/*-----------------------------------------------------------------------------------*/
/* Check for a Default Font
/*-----------------------------------------------------------------------------------*/
function ag_is_default($font) {
  if ($font == 'Arial' || $font == 'Georgia' || $font == 'Tahoma' || $font == 'Verdana' || $font == 'Helvetica') {
    $font = true;
  } else {
	$font = false;  
  }
  return $font;
}

/*-----------------------------------------------------------------------------------*/
/* Get a Specific Amount of Categories
/*-----------------------------------------------------------------------------------*/

function ag_get_cats($num, $list=NULL){
	
    $t=get_the_category();
    $count=count($t); 

    if ($list) {
    	$list = ', ';
    } else {
    	$list = '';
    }
	
	if ($count < $num) $num = $count;
	
	$cat_string = '';
    for($i=0; $i<$num; $i++){
        $cat_string.= '<a href="'.get_category_link( $t[$i]->cat_ID  ).'">'.$t[$i]->cat_name.'</a>'.$list;
    }

    if ($list == ', ') $cat_string = rtrim($cat_string, ', ');
	
	if ($cat_string) return $cat_string;
}

/*-----------------------------------------------------------------------------------*/
/* Load Google Fonts
/*-----------------------------------------------------------------------------------*/

function ag_load_fonts($default='Source Sans Pro') {
	
$cyrillic = of_get_option('of_cyrillic_chars');

	// Initialize Variables
	$fonts = '';
	$font_list = array();
	$cyrillic_chars = '';
	
	// Get All Font Options
	$option_fonts = array(
		of_get_option('of_nav_font'),
		of_get_option('of_heading_font'),
		of_get_option('of_page_subtitle_font'),
		of_get_option('of_content_heading_font'),
		of_get_option('of_content_subheading_font'),
		of_get_option('of_button_font'),
		of_get_option('of_tiny_font'),
		of_get_option('of_p_font')
		);
	

  // Check for cyrillic character option
  if ($cyrillic == 'Yes') $cyrillic_chars = ':cyrillic,latin'; 
  
  $fonts .= "
    <!-- Embed Google Web Fonts Via API -->
    <script type='text/javascript'>
          WebFontConfig = {
            google: { families: [ ";
				// Store the font list.
				$fontlist = '';
				foreach ($option_fonts as $font) {
					$italic = $font['style'];
					$italic = ($italic == 'italic') ? 'italic' : '';
					$fontlist .= '"' . $font['face'] . ':'. $font['weight'] . $italic . $cyrillic_chars .'", ';
				}
				// Trim the last comma and space for IE and store in fonts
				$fonts .= rtrim($fontlist, ', ');
    $fonts .=  " ] }   };
          (function() {
            var wf = document.createElement('script');
            wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
                '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
          })();
    </script>";
	
	return $fonts;
}

/*-----------------------------------------------------------------------------------*/
/* Add Theme Customizer Under Appearance
/*-----------------------------------------------------------------------------------*/

add_action('admin_menu', 'add_customizer_to_appearance');
function add_customizer_to_appearance() 
{
  add_submenu_page('themes.php', 'Customizer', 'Theme Customizer', 'edit_theme_options', 'customize.php', '', '', 6);
}

/*-----------------------------------------------------------------------------------*/
/* Get Popular Posts
/*-----------------------------------------------------------------------------------*/

function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "<span>0</span> Views";
    }
    return '<span>'. $count.'</span> '. __('Views', 'framework');
}
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}


/*------------------------------------------------------------------------------*/
/*	Get Caption from Image ID
/*------------------------------------------------------------------------------*/

function the_post_thumbnail_info($thumbnail_id = NULL, $size = 'medium') {
  global $post;
  $info = (object)array();

  if ($thumbnail_id == NULL) $thumbnail_id    = get_post_thumbnail_id($post->ID);
  $thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));

  if ($thumbnail_image && isset($thumbnail_image[0])) {

  	$src = wp_get_attachment_image_src( $thumbnail_id, $size);
  	$info->src = $src[0];

  	//Show the thumbnail caption
    $info->caption = $thumbnail_image[0]->post_excerpt;
    $info->caption = str_replace('"', "", $info->caption); 

    //Show the thumbnail description
    $info->description = $thumbnail_image[0]->post_content; 
    
    //Show the thumbnail alt field
    $info->alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    $info->alt = str_replace('"', "", $info->alt); 
  }

  return $info;
}

function get_the_post_thumbnail_fadein($size = 'medium') {
	global $post;

	$thumbnail = (object)array();
	$thumb = the_post_thumbnail_info(get_post_thumbnail_id($post->ID), $size);

	$thumbnail = '<div class="featured-image thumbnail-' . get_post_thumbnail_id($post->ID). '"><img src="' .$thumb->src .'" ';
	$thumbnail .= ($thumb->alt) ? 'alt="'. $thumb->alt . '" ' : 'alt="'.get_the_title().'" '; 
	$thumbnail .= ($thumb->caption) ? 'title="'. $thumb->caption .'" ' : '';
	$thumbnail .= 'class="scale-with-grid" onload="jQuery(this).closest(&#39;.featured-image&#39;).animate({opacity : 1}, 500, function(){ jQuery(this).css(&#39;background&#39;, &#39;none&#39;)});" /></div>';

	return $thumbnail;

}


/*-----------------------------------------------------------------------------------*/
/* Add Lightbox to Gallery Insert
/*-----------------------------------------------------------------------------------*/

add_filter( 'wp_get_attachment_link', 'themewich_lightbox');
 
function themewich_lightbox($content) {
	$content = preg_replace("/<a/","<a class=\"themewich-lightbox no-ajaxy\"",$content,1);
	return $content;
}


/**
 * Automatically add lightbox to images linking to images.
 * 
 * @param  string $content
 * 
 * @return string Content html to filter
 */
if (!function_exists('themewich_add_lightbox')) :
    function themewich_add_lightbox($content) {

        //Check the page for link images direct to image (no trailing attributes)
        $string = '/<a href="(.*?).(jpg|jpeg|png|gif|bmp|ico)"><img(.*?)class="(.*?)wp-image-(.*?)" \/><\/a>/i';
        preg_match_all( $string, $content, $matches, PREG_SET_ORDER);

        //Check which attachment is referenced
        foreach ($matches as $val) :
            $post = get_post($val[5]);
            
            //Replace the instance with the lightbox and title(caption) references. Won't fail if caption is empty.
            $string = '<a href="' . $val[1] . '.' . $val[2] . '"><img' . $val[3] . 'class="' . $val[4] . 'wp-image-' . $val[5] . '" /></a>';
            $replace = '<a href="' . $val[1] . '.' . $val[2] . '" class="themewich-lightbox"><img' . $val[3] . 'class="' . $val[4] . 'wp-image-' . $val[5] . '" /></a>';
            $content = str_replace( $string, $replace, $content);
        endforeach;

		// Add lightbox script
		global $add_lightbox;
		$add_lightbox = true;
		
        return $content;
    }
    add_filter('the_content', 'themewich_add_lightbox', 2);
endif;


/*-----------------------------------------------------------------------------------*/
/*	Get Post Slides
/*-----------------------------------------------------------------------------------*/
if (!function_exists('themewich_thumbnail_post_slideshow')) :
	function themewich_thumbnail_post_slideshow($image_size, $id, $thumbnum) {
		
		// Add slideshow javascript
		global $add_slider;
		$add_slider = true;
		
		// Add one to the thumbnail number for the loop
		$thumbnum++; 
		// Set the slideshow variable	
		$slideshow = '';
		
		// Get The Post Type
		$posttype = get_post_type( $id );
		
		// Check whether the slide should link
		$permalink = get_permalink($id);
		$title = get_the_title($id);
		$permalink = '<a href="'.$permalink.'" title="'.$title.'" class="postlink thumbnail-'.$image_size.'">';
		$permalinkend = '</a>';
		
		$counter = 2; //start counter at 2			  
		
		$full = get_post_meta($id,'_thumbnail_id',false); // Get Image ID 
		
		
		/* If there's a featured image
		================================================== */
		if($full) {

			$alt = get_post_meta($full, '_wp_attachment_image_alt', true); // Alt text of image
			$full = wp_get_attachment_image_src($full[0], 'full', false);  // URL of Featured Full Image
					  
			$thumb = get_post_meta($id,'_thumbnail_id',false); 
			$thumb = wp_get_attachment_image_src($thumb[0], $image_size, false);  // URL of Featured first slide
			
			
			// Get all slides
			while ($counter < ($thumbnum)) {
				
				${"full" . $counter} = MultiPostThumbnails::get_post_thumbnail_id($posttype, $counter . '-slide', $id); // Get Image ID

				${"alt" . $counter} = get_post_meta(${"full" . $counter} , '_wp_attachment_image_alt', true); // Alt text of image			 
				${"full" . $counter} = wp_get_attachment_image_src(${"full" . $counter}, false); // URL of Second Slide Full Image
				
				${"thumb" . $counter} = MultiPostThumbnails::get_post_thumbnail_id($posttype, $counter . '-slide', $id); 
				${"thumb" . $counter} = wp_get_attachment_image_src(${"thumb" . $counter}, $image_size, false); // URL of next Slide 
			 
			$counter++;
			
			}
			
			// If there's a slide 2
			$slideshow .= (isset($thumb2[0]) && $thumb2[0] != '') ? '<ul class="bxslider"><li>' : '';
			
			// If there's a slide 2 and outside arrows are set to true
			$slideshow .= $permalink . '<img src="' . $thumb[0] .'" onLoad="jQuery(this).closest(&#39;.postphoto&#39;).css({&#39;min-height&#39; : &#39;0&#39;});" alt="';
			// If there's an image alt info, set it
			$slideshow .= ($alt) ? str_replace('"', "", $alt) : get_the_title();
			$slideshow .= '"';
			
			$slideshow .= ' class="scale-with-grid"/><div class="overlay"></div>' .
                        '<div class="title">'.
                          '<h2>'.get_the_title($id).'</h2>'.
                        '</div>' .$permalinkend;
			
			$slideshow .= (isset($thumb2[0]) && $thumb2[0] != '') ? '</li>' : '';
			
			// Loop through thumbnails and set them
			if (isset($thumb2[0]) && $thumb2[0] != '') {	
				$tcounter = 2;
				while ($tcounter < ($thumbnum)) :
					if ( ${'thumb' . $tcounter}) : 
					   $slideshow .= '<li>' . $permalink . '<img src="' . ${'thumb' . $tcounter}[0] .'" alt="';
					   $slideshow .= (${'alt' . $tcounter}) ? str_replace('"', "", ${'alt' . $tcounter}) : get_the_title();
					   $slideshow .= '" ';
					   $slideshow .= ' class="scale-with-grid"/><div class="overlay"></div>' .
                        '<div class="title">'.
                          '<h2>'.get_the_title().'</h2>'.
                        '</div>'. $permalinkend . '</li>';
					endif; $tcounter++;
				endwhile; 
			}
			
			$slideshow .= (isset($thumb2[0]) && $thumb2[0] != '') ? '</ul>' : '';

			
		} else {
			$slideshow .= $permalink .'<div class="title">'.
                          '<h2>'.get_the_title($id).'</h2>'.
                        '</div>' .$permalinkend;
		} // End if $full
		  
		return $slideshow;

	} 
endif;

/**
 * Envato Protected API
 *
 * Wrapper class for the Envato marketplace protected API methods specific
 * to the Envato WordPress Toolkit plugin.
 *
 * @package     WordPress
 * @subpackage  Envato WordPress Toolkit
 * @author      Derek Herman <derek@envato.com>
 * @since       1.0
 */ 
if (!class_exists("Envato_Protected_API")) {
    class Envato_Protected_API {
      /**
       * The buyer's Username
       *
       * @var       string
       *
       * @access    public
       * @since     1.0
       */
      public $user_name;
      
      /**
       * The buyer's API Key
       *
       * @var       string
       *
       * @access    public
       * @since     1.0
       */
      public $api_key;
      
      /**
       * The default API URL
       *
       * @var       string
       *
       * @access    private
       * @since     1.0
       */
      protected $public_url = 'http://marketplace.envato.com/api/edge/set.json';
      
      /**
       * Error messages
       *
       * @var       array
       *
       * @access    public
       * @since     1.0
       */
      public $errors = array( 'errors' => '' );
      
      /**
       * Class contructor method
       *
       * @param     string      The buyer's Username
       * @param     string      The buyer's API Key can be accessed on the marketplaces via My Account -> My Settings -> API Key
       * @return    void        Sets error messages if any.
       *
       * @access    public
       * @since     1.0
       */
      public function __construct( $user_name = '', $api_key = '' ) {
      
        if ( $user_name == '' ) {
          $this->set_error( 'user_name', __( 'Please enter your Envato Marketplace Username.', 'envato' ) );
        }
          
        if ( $api_key == '' ) {
          $this->set_error( 'api_key', __( 'Please enter your Envato Marketplace API Key.', 'envato' ) );
        }
          
        $this->user_name  = $user_name;
        $this->api_key    = $api_key;
        
      }
      
      /**
       * Get private user data.
       *
       * @param     string      Available sets: 'vitals', 'earnings-and-sales-by-month', 'statement', 'recent-sales', 'account', 'verify-purchase', 'download-purchase', 'wp-list-themes', 'wp-download'
       * @param     string      The buyer/author username to test against.
       * @param     string      Additional set data such as purchase code or item id.
       * @param     bool        Allow API calls to be cached. Default false.
       * @param     int         Set transient timeout. Default 300 seconds (5 minutes).
       * @return    array       An array of values (possibly cached) from the requested set, or an error message.
       *
       * @access    public
       * @since     1.0
       * @updated   1.3
       */ 
      public function private_user_data( $set = '', $user_name = '', $set_data = '', $allow_cache = false, $timeout = 300 ) { 
        
        if ( $set == '' ) {
          $this->set_error( 'set', __( 'The API "set" is a required parameter.', 'envato' ) );
        }
          
        if ( $user_name == '' ) {
          $user_name = $this->user_name;
        }
          
        if ( $set_data !== '' ) {
          $set_data = ":$set_data";
        }
          
        $url = "http://marketplace.envato.com/api/edge/$user_name/$this->api_key/$set$set_data.json";
        
        /* set transient ID for later */
        $transient = $user_name . '_' . $set . $set_data;
        
        if ( $allow_cache ) {
          $cache_results = $this->set_cache( $transient, $url, $timeout );
          $results = $cache_results;
        } else {
          $results = $this->remote_request( $url );
        }
        
        if ( isset( $results->error ) ) {
          $this->set_error( 'error_' . $set, $results->error );
        }
        
        if ( $errors = $this->api_errors() ) {
          $this->clear_cache( $transient );
          return $errors;
        }
        
        if ( isset( $results->$set ) ) {
          return $results->$set;
        }
        
        return false;
        
      }
      
      /**
       * Used to list purchased themes.
       *
       * @param     bool        Allow API calls to be cached. Default true.
       * @param     int         Set transient timeout. Default 300 seconds (5 minutes).
       * @return    object      If user has purchased themes, returns an object containing those details.
       *
       * @access    public
       * @since     1.0
       * @updated   1.3
       */
      public function wp_list_themes( $allow_cache = true, $timeout = 300 ) {
      
        return $this->private_user_data( 'wp-list-themes', $this->user_name, '', $allow_cache, $timeout );
        
      }
      
      /**
       * Used to download a purchased item.
       *
       * This method does not allow caching.
       *
       * @param     string      The purchased items id
       * @return    string|bool If item purchased, returns the download URL.
       *
       * @access    public
       * @since     1.0
       */ 
      public function wp_download( $item_id ) {
        
        if ( ! isset( $item_id ) ) {
          $this->set_error( 'item_id', __( 'The Envato Marketplace "item ID" is a required parameter.', 'envato' ) );
        }
          
        $download = $this->private_user_data( 'wp-download', $this->user_name, $item_id );
        
        if ( $errors = $this->api_errors() ) {
          return $errors;
        } else if ( isset( $download->url ) ) {
          return $download->url;
        }
        
        return false;
      }
      
      /**
       * Retrieve the details for a specific marketplace item.
       *
       * @param     string      $item_id The id of the item you need information for. 
       * @return    object      Details for the given item.
       *
       * @access    public
       * @since     1.0
       * @updated   1.3
       */
      public function item_details( $item_id, $allow_cache = true, $timeout = 300 ) {
        
        $url = preg_replace( '/set/i', 'item:' . $item_id, $this->public_url );
        
        /* set transient ID for later */
        $transient = 'item_' . $item_id;
          
        if ( $allow_cache ) {
          $cache_results = $this->set_cache( $transient, $url, $timeout );
          $results = $cache_results;
        } else {
          $results = $this->remote_request( $url );
        }
        
        if ( isset( $results->error ) ) {
          $this->set_error( 'error_item_' . $item_id, $results->error );
        }
        
        if ( $errors = $this->api_errors() ) {
          $this->clear_cache( $transient );
          return $errors;
        }
          
        if ( isset( $results->item ) ) {
          return $results->item;
        }
        
        return false;
        
      }
      
      /**
       * Set cache with the Transients API.
       *
       * @link      http://codex.wordpress.org/Transients_API
       *
       * @param     string      Transient ID.
       * @param     string      The URL of the API request.
       * @param     int         Set transient timeout. Default 300 seconds (5 minutes).
       * @return    mixed
       *
       * @access    public
       * @since     1.3
       */ 
      public function set_cache( $transient = '', $url = '', $timeout = 300 ) {
      
        if ( $transient == '' || $url == '' ) {
          return false;
        }
        
        /* keep the code below cleaner */
        $transient = $this->validate_transient( $transient );
        $transient_timeout = '_transient_timeout_' . $transient;
        
        /* set original cache before we destroy it */
        $old_cache = get_option( $transient_timeout ) < time() ? get_option( $transient ) : '';
        
        /* look for a cached result and return if exists */
        if ( false !== $results = get_transient( $transient ) ) {
          return $results;
        }
        
        /* create the cache and allow filtering before it's saved */
        if ( $results = apply_filters( 'envato_api_set_cache', $this->remote_request( $url ), $transient ) ) {
          set_transient( $transient, $results, $timeout );
          return $results;
        }
        
        return false;
        
      }
      
      /**
       * Clear cache with the Transients API.
       *
       * @link      http://codex.wordpress.org/Transients_API
       *
       * @param     string      Transient ID.
       * @return    void
       *
       * @access    public
       * @since     1.3
       */ 
      public function clear_cache( $transient = '' ) {
      
        delete_transient( $transient );
        
      }
      
      /**
       * Helper function to validate transient ID's.
       *
       * @param     string      The transient ID.
       * @return    string      Returns a DB safe transient ID.
       *
       * @access    public
       * @since     1.3
       */
      public function validate_transient( $id = '' ) {

        return preg_replace( '/[^A-Za-z0-9\_\-]/i', '', str_replace( ':', '_', $id ) );
        
      }
      
      /**
       * Helper function to set error messages.
       *
       * @param     string      The error array id.
       * @param     string      The error message.
       * @return    void
       *
       * @access    public
       * @since     1.0
       */
      public function set_error( $id, $error ) {
      
        $this->errors['errors'][$id] = $error;
        
      }
      
      /**
       * Helper function to return the set errors.
       *
       * @return    array       The errors array.
       *
       * @access    public
       * @since     1.0
       */
      public function api_errors() {
      
        if ( ! empty( $this->errors['errors'] ) ) {
          return $this->errors['errors'];
        }
        
      }
      
      /**
       * Helper function to query the marketplace API via wp_remote_request.
       *
       * @param     string      The url to access.
       * @return    object      The results of the wp_remote_request request.
       *
       * @access    private
       * @since     1.0
       */
      protected function remote_request( $url ) {
      
        if ( empty( $url ) ) {
          return false;
        }

        $request = wp_remote_request( $url );

        if ( is_wp_error( $request ) ) {
            echo $request->get_error_message();
            return false;
        }

        $data = json_decode( $request['body'] );
        
        if ( $request['response']['code'] == 200 ) {
          return $data;
        } else {
          $this->set_error( 'http_code', $request['response']['code'] );
        }
          
        if ( isset( $data->error ) ) {
          $this->set_error( 'api_error', $data->error ); 
        }
        
        return false;
      }
      
      /**
       * Helper function to print arrays to the screen ofr testing.
       *
       * @param     array       The array to print out
       * @return    string
       *
       * @access    public
       * @since     1.0
       */
      public function pretty_print( $array ) {
      
        echo '<pre>';
        print_r( $array );
        echo '</pre>';
        
      }
    }
}


/**
 * Theme Updater
 */
if (!class_exists("themewich_Updater")) {
    class themewich_Updater {

        protected $username;
        protected $apikey;

        public function __construct($username = null,$apikey = null,$authors = null) {

            $this->username = $username;
            $this->apikey = $apikey;
            $this->authors = $authors;

        }

        public function check($updates) {

            $this->username = apply_filters("themewich_updater_username",$this->username);
            $this->apikey = apply_filters("themewich_updater_apikey",$this->apikey);
            $this->authors = apply_filters("themewich_updater_authors",$this->authors);

            if (isset($this->authors) && !is_array($this->authors)) {
                $this->authors = array($this->authors);
            }

            if (!isset($this->username) || !isset($this->apikey) || !isset($updates->checked)) return $updates;

            if (!class_exists("Envato_Protected_API")) {
                require_once("class-envato-protected-api.php");
            }


            $api = new Envato_Protected_API($this->username,$this->apikey);
            add_filter("http_request_args",array(&$this,"http_timeout"),10,1);
            $purchased = $api->wp_list_themes(true);

            $installed = wp_get_themes();
            $filtered = array();

            foreach ($installed as $theme) {
                if ($this->authors && !in_array($theme->{'Author Name'},$this->authors)) continue;
                $filtered[$theme->Name] = $theme;
            }

            foreach ($purchased as $theme) {
                if (isset($filtered[$theme->theme_name])) {
                    // gotcha, compare version now
                    $current = $filtered[$theme->theme_name];
                    if (version_compare($current->Version, $theme->version, '<')) {
                        // bingo, inject the update
                        if ($url = $api->wp_download($theme->item_id)) {
                            $update = array(
                                            "url" => "http://themeforest.net/item/theme/{$theme->item_id}",
                                            "new_version" => $theme->version,
                                            "package" => $url
                                            );

                            $updates->response[$current->Stylesheet] = $update;

                        }
                    }
                }
            }

            remove_filter("http_request_args",array(&$this,"http_timeout"));

            return $updates;
        }

        public function http_timeout($req) {
            // increase timeout for api request
            $req["timeout"] = 300;
            return $req;
        }

    }
}

/* 
    Plugin Name: Themeforest Themes Update
    Plugin URI: https://github.com/bitfade/themeforest-themes-update
    Description: Updates all themes purchased from themeforest.net 
    Author: pixelentity
    Version: 1.0
    Author URI: http://pixelentity.com
*/


// to debug
// set_site_transient('update_themes',null);

if(!function_exists('themeforest_themes_update')) {
	function themeforest_themes_update($updates) {
	    if (isset($updates->checked)) {    
	        $username = defined('THEMEFOREST_USERNAME') ? THEMEFOREST_USERNAME : null;
	        $apikey = defined('THEMEFOREST_APIKEY') ? THEMEFOREST_APIKEY : null;

	        $updater = new themewich_Updater($username,$apikey);
	        $updates = $updater->check($updates);
	    }
	    return $updates;
	}
	add_filter("pre_set_site_transient_update_themes", "themeforest_themes_update");
}
?>