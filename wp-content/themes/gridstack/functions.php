<?php 
/**
 * GridStack functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage gridstack
 * @since GridStack 1.0
 */

// Get theme version
$themewich_theme_info = wp_get_theme(); 

// Include Themewich framework
include("framework/framework.php"); 

// Add the Custom Fields for Sections and Pages
include("functions/customfields.php");

// WP customizer CSS and theme options CSS
include('functions/css/custom-styles.php');

// Remove admin bar
add_filter('show_admin_bar', '__return_false');

// Set content width
if ( ! isset( $content_width ) )
	$content_width = 1500;
	
$tw_column_width = 'thirteen';

if (class_exists('MultiPostThumbnails')) { 

   if ( $thumbnum = of_get_option('of_thumbnail_number') ) { $thumbnum = ($thumbnum + 1); } else { $thumbnum = 3;}
   $counter1 = 2;

    while ($counter1 < ($thumbnum)) {
    
    // Add Slides in Posts  
    new MultiPostThumbnails( 
        array( 
            'label' => 'Thumbnail Slide ' . $counter1, 
            'id' => $counter1 . '-slide', 
            'post_type' => 'post' 
        ));
    
    // Add Slides in Portfolio Items
    new MultiPostThumbnails( 
        array( 
            'label' => 'Thumbnail Slide ' . $counter1, 
            'id' => $counter1 . '-slide', 
            'post_type' => 'portfolio' 
        )); 
    
    $counter1++;
    
    }
}

/**
 * Sets up theme defaults and registers the various WordPress features that
 * GridStack supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail sizes.
 *
 * @since GridStack 1.0
 */
function themewich_setup() {

    // Adds RSS feed links to <head> for posts and comments.
    add_theme_support( 'automatic-feed-links' );

    // This theme supports a variety of post formats
    //add_theme_support( 'post-formats', array( 'image', 'gallery', 'video', 'audio' ) );
  
    // Adds menu support
    add_theme_support('menus');

    // This theme uses wp_nav_menu() in one location.
    if (function_exists('register_nav_menus')) {

        register_nav_menus(array('main_nav_menu' => 'Main Navigation Menu'));

        /**
         * Removes default menu container div
         * 
         * @param  array $args Arguments for the wp_nav_menu_args filter
         * 
         * @return array Returns altered array
         */
        function my_wp_nav_menu_args($args = '') {
            $args['container'] = false;
            return $args;
        } 
        add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );
    }

    /**
     * Add Theme Widgets
     */
    // Add the News Custom Widget
    include("functions/widgets/widget-news.php");
    // Add the Contact Custom Widget
    include("functions/widgets/widget-contact.php");
    // Add the Social Counter Tabs Widget
    include("functions/widgets/widget-tab.php");
    // Add the Recent Projects Widget
    include("functions/widgets/widget-recent-projects.php");

}
add_action( 'after_setup_theme', 'themewich_setup' );


/**
 * Remove Items from WYSIWYG for SEO Purposes
 * 
 * @param  array $init
 * 
 * @return array
 */
if (!function_exists('themewich_tinyMCE')) :
    function themewich_tinyMCE($init) {
        $init['theme_advanced_blockformats'] = 'h3,h4,h5,p,pre';
        return $init;
    }
    add_filter('tiny_mce_before_init', 'themewich_tinyMCE' );
endif;


/**
 * Modify WordPress Caption to add class and remove inline styles.
 * 
 * @param  array  $attr
 * @param  string $content
 * 
 * @return string
 */
if (!function_exists('themewich_image_caption_class')) :
    function themewich_image_caption_class($attr, $content = null) {

        if (!isset($attr['caption'])) :

            // Set initial variables
            $captionclass='caption-normal';
            $widthstyle = true;

            // Get the caption
            if ( preg_match( '#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $content, $matches ) ) {
                $content = $matches[1];
                $attr['caption'] = trim( $matches[2] );
            } 

            // Find parallaximg class
            if ($c=preg_match_all ("/.*?(parallaximg)/is", $content, $matches)) {
                $captionclass= 'caption-'.$matches[1][0];
                $widthstyle = false;
            } 
            // Find fullimg class
            if ($c=preg_match_all ("/.*?(fullimg)/is", $content, $matches)) {
                $captionclass='caption-'.$matches[1][0];
                $widthstyle = false;
            } 
            // Find fixedimg class
            if ($c=preg_match_all ("/.*?(fixedimg)/is", $content, $matches)) {
                $captionclass='caption-'.$matches[1][0];
                $widthstyle = false;
            }    

        endif;
        
        $output = apply_filters('img_caption_shortcode', '', $attr, $content);
        if ( $output != '' ) return $output;

        extract(shortcode_atts(array(
            'id'    => '',
            'align' => 'alignnone',
            'width' => '',
            'caption' => ''
        ), $attr));

        $widthstyle = ($widthstyle == true) ? 'style="width:'.$width.'px;"' : '';

        if ( 1 > (int) $width || empty($caption) ) return $content;
        if ( $id ) $id = 'id="' . esc_attr($id) . '" ';
        
        return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" '.$widthstyle.'>'
        . do_shortcode( $content ) . '<p class="wp-caption-text '. $captionclass.'"><span>' . $caption . '</span></p></div>';
    }

    add_shortcode('wp_caption', 'themewich_image_caption_class');
    add_shortcode('caption', 'themewich_image_caption_class');
endif;


/**
 * Add class to images based on alignment.
 * 
 * @param  string      $html    string the html to insert into editor
 * @param  integer     $id      image attachment id
 * @param  string      $caption image caption
 * @param  string      $alt     image alt attribute
 * @param  string      $title   image title attribute
 * @param  string      $align   image css alignment property
 * @param  string      $url     image src url
 * @param  string|bool $rel     image rel attribute
 * @param  string      $size    image size (thumbnail, medium, large, full or added  with add_image_size() )
 * 
 * @return string      the html to insert into editor
 */
if (!function_exists('themewich_addclass_to_images')) :
    function themewich_addclass_to_images($html, $id, $caption, $title, $align, $url, $size, $alt = '' ) {

        switch ($align) {
            case 'parallax':
                $alignclass = 'parallaximg';
            break;
            case 'full':
                $alignclass = 'fullimg';
            break;
            case 'fixed':
                $alignclass = 'fixedimg';
            break;
            default:
                $alignclass = 'normal';
            break;
        }

       // Check for image tag in html content
        $found = preg_match("/<img[^>]*>/", $html, $a_elem);

        // If no image, do nothing
        if($found <= 0) return $html;

        // Convert to string
        $a_elem = $a_elem[0];

        // If image already has class defined inject it to attribute
        if(strstr($a_elem, "class=\"") !== FALSE) { 
            $a_elem_new = str_replace("class=\"", "class=\"$alignclass ", $a_elem);
            $html = str_replace($a_elem, $a_elem_new, $html);
        } else { // If no class defined, just add class attribute
            $html = str_replace("<img ", "<img class=\"$alignclass \" ", $html);
        }

        return $html;
    }
    add_filter('image_send_to_editor', 'themewich_addclass_to_images', 10, 8);
endif;


/**
 * Checks to see if the post custom field is set to full-width
 * @param  array $classes array that stores the classes
 * @return array          classes with new added class
 */
if (!function_exists('themewich_full_class')) :
    function themewich_full_class($classes) {
      global $post;

      $fullwidth            = '';
      $fixedwidth           = '';
      $columnswidth         = '';
      $no                   = 'no-';
      $full_class           = 'full-width';
      $fixed_class          = 'fixed-allowed';
      $columns_class        = 'full-columns';

      // if it's a post (not a page)
      if (is_single()) {

        $fullwidth      = $full_class;
        $fixedwidth     = (get_post_meta($post->ID, 'ag_fullwidth', true) == 'Full') ? $no.$fixed_class : $fixed_class;
        $columnwidth    = ($fixedwidth == $no.$fixed_class) ? $columns_class : $no.$columns_class;

      // Otherwise it's a page
      } else {

        $template   = ($post) ? get_page_template_slug( $post->ID ) : '';

        // Page templates where full-width is allowed
        $fullpages  = array(
            'template-medium.php',
            'template-home.php', 
            'template-home-fixed.php',
            'template-portfolio.php',
            'template-portfolio-fixed.php',
            'template-full.php'
            );

        // Page templates where fixed-width is allowed
        $fixedpages  = array(
            'template-medium.php'
            );

        // Page templates for full columns
        $columns  = array(
            'template-full.php'
        );
        
        $fullwidth      = (in_array($template, $fullpages)) ? $full_class : $no.$full_class;
        $fixedwidth     = (in_array($template, $fixedpages)) ? $fixed_class : $no.$fixed_class;
        $columnwidth    = (in_array($template, $columns)) ? $columns_class : $no.$columns_class;

       }

      // add class name to the $classes array
      $classes[] = $fullwidth;
      $classes[] = $fixedwidth;
      $classes[] = $columnwidth;
      // return the $classes array
      return $classes;
    }
    add_filter('body_class','themewich_full_class');
endif;

  
/**
 * Custom gallery html
 * 
 * @param  array  $attr Stores all shortcode properties
 * 
 * @return string Modified gallery html
 */
if (!function_exists('themewich_gallery_shortcode')) :
    function themewich_gallery_shortcode($attr) {

		global $tw_column_width;

        /**
         * Check body classes to find page size
         */
        $bodyclass  = get_body_class();
        $fullwidth  = (in_array('full-width', $bodyclass)) ? true : false;
        $columns    = (in_array('full-columns', $bodyclass)) ? 'sixteen' : $tw_column_width;


        /**
         * Initial default variables
         */
        $post       = get_post();
        $posttype   = get_post_type( $post );
        $format     = get_post_format( $post->ID );
        $slidesize  = 'gallery';
        $width      = (isset($attr['width'])) ? $attr['width'] : 'fixed';
        $linked     = (isset($attr['link']) && $attr['link'] == 'none') ? false : true;
        $closeout   = "</div><!--singlecontent--></div><!--thirteen columns--></div><!--Container-->";
        $openout    = "<div class='clear'></div><div class='container'><div class='$columns columns'><div class='singlecontent'>";

     
	 	if ($linked == true) {
			global $add_lightbox;
			$add_lightbox = true;
		}
        /**
         * Create output wrappings
         */
        if (isset($attr['type'])) :

            switch($attr['type']) :

                // Slideshow Gallery
                case 'slideshow':
                    global $add_slider;
                    $add_slider = true;
                    if ($width == 'full' && $fullwidth) : // If it's full width
                        $slidesize  = (isset($attr['crop']) && $attr['crop'] == 'nocrop') ? 'fullslideshownc' : 'fullslideshow';
                        if ($attr['autoplay'] == 'yes') {
                            $type       = "$closeout <div class='single-slideshow gallery-wrap info pager autoplay $width'>";
                        } else {
                            $type       = "$closeout <div class='single-slideshow gallery-wrap info pager $width'>";
                        }
                        $type_end   = $openout;
                    else : // If it's not 
                        if ($attr['autoplay'] == 'yes') {
                            $type       = "<div class='single-slideshow gallery-wrap info pager autoplay $width'>";
                        } else {
                            $type       = "<div class='single-slideshow gallery-wrap info pager $width'>";
                        }
                        $slidesize  = (isset($attr['crop']) && $attr['crop'] == 'nocrop') ? 'fixedslideshownc' : 'fixedslideshow';
                    endif;
                break;

                // Carousel Gallery
                case 'carousel':
					global $add_slider;
					$add_slider = true;
                    if ($width == 'full' && $fullwidth) : // If it's full width
                        $type       = "$closeout <div class='single-carousel carousel info gallery-wrap $width'>";
                        $type_end   = $openout;
                    else : // If it's not 
                        $type       = "<div class='single-carousel info carousel gallery-wrap $width'>";
                    endif;
                break;

            endswitch;

        else : // If no type is set, default to gallery
			
			$type       = "<div class='single-gallery gallery-wrap fixed'>";

        endif;

      // Set Slideshow Classes
      $slideshowclasses = 'pager nocaption nolink ';

      // Add type of slideshow class
      $slideshowclasses .= (isset($attr['type'])) ? $attr['type'] . ' ' : ''; 

      // Add size and width classes too
      $slideshowclasses .= $slidesize . ' ' . $width;


    /**
     * Start the creation of the gallery
     * This is a modified version of WordPress' 
     * gallery function
     */

    static $instance = 0;
    $instance++;

    if ( ! empty( $attr['ids'] ) ) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if ( empty( $attr['orderby'] ) )
            $attr['orderby'] = 'post__in';
        $attr['include'] = $attr['ids'];
    }

    // Allow plugins/themes to override the default gallery template.
    $output = apply_filters('post_gallery', '', $attr);
    if ( $output != '' )
        return $output;

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( !$attr['orderby'] )
            unset( $attr['orderby'] );
    }

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'itemtag'    => 'div',
        'icontag'    => 'div',
        'captiontag' => 'div',
        'columns'    => 3,
        'size'       => $slidesize,
        'include'    => '',
        'exclude'    => ''
    ), $attr));

    $id = intval($id);
    if ( 'RAND' == $order )
        $orderby = 'none';

    if ( !empty($include) ) {
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty($exclude) ) {
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
        return '';

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $icontag = tag_escape($icontag);
    $valid_tags = wp_kses_allowed_html( 'post' );
    if ( ! isset( $valid_tags[ $itemtag ] ) )
        $itemtag = 'div';
    if ( ! isset( $valid_tags[ $captiontag ] ) )
        $captiontag = 'div';
    if ( ! isset( $valid_tags[ $icontag ] ) )
        $icontag = 'div';

    $columns = intval($columns);
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $gallery_style = $gallery_div = '';
    if ( apply_filters( 'use_default_gallery_style', true ) )
        $gallery_style = "
        <style type='text/css' scoped='scoped'>
            #{$selector} .gallery-item {
                float: {$float};
                margin-top: 10px;
            }
            #{$selector} img {
                max-width:100% !important;
                height:auto !important;
            }
            #{$selector} .gallery-caption {
                margin-left: 0;
            }
        </style>";
    $size_class = sanitize_html_class( $size );
    if ($posttype == 'post' && $slidesize) {
    	$size_class = $slidesize;
    }

    if (isset($attr['type']) && ($attr['type'] == 'slideshow' || $attr['type'] == 'carousel')) {
        $gallery_div = "$type <ul id='$selector' class='bxslider gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class} $slideshowclasses'>";
    } else {
        $gallery_div = "$type <div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
    }

    $output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

    if (isset($attr['type']) && ($attr['type'] == 'slideshow' || $attr['type'] == 'carousel')) :
    $i = 0;
    foreach ( $attachments as $id => $attachment ) :

        $default_attr = array(
            'title' => apply_filters( 'the_title', $attachment->post_title ),
        );

        $image = wp_get_attachment_image($id, $size, false, $default_attr);
        $image = preg_replace( '/(width|height)=\"\d*\"\s/', "", $image );
        $link = wp_get_attachment_url($id); 

        $output .= "
            <li class='gallery-icon";

        $output .= ($linked) ? ' linked' : ' notlinked';

        $output .= "'>";

        $output .= ($link && $linked) ? "<a href='$link' class='lightbox-gallery no-ajaxy'>" : '';
        $output .= "$image";

        if ( $captiontag && trim($attachment->post_excerpt) ) {
            $output .= "
                <div class='wp-caption-text gallery-caption'>
                  <span>
                " . wptexturize($attachment->post_excerpt) . "
                  </span>
                </div>";
        } 

        $output .= ($link && $linked) ? "</a>" : "";
        $output .= "</li>";
    endforeach; 

    else :
        
        $i = 0;
        foreach ($attachments as $id => $attachment) :

            $default_attr = array(
                'title' => apply_filters( 'the_title', $attachment->post_title ),
            );

            $image = wp_get_attachment_image($id, $size, false, $default_attr);
            $image = preg_replace( '/(width|height)=\"\d*\"\s/', "", $image );
            $link = wp_get_attachment_url($id); 
           
            $output .= "<{$itemtag} class='gallery-item gallery-icon'>";
           // $output .= "<{$icontag} class='gallery-icon'>";
                    $output .= "
            <{$icontag}  class='gallery-icon";

        $output .= ($linked) ? ' linked' : ' notlinked';

        $output .= "'>";
            $output .= ($link && $linked) ? "<a href='$link' class='lightbox-gallery no-ajaxy'>" : '';
            $output .= $image;

            if ( $captiontag && trim($attachment->post_excerpt) ) {
                $output .= "
                    <{$captiontag} class='wp-caption-text gallery-caption'>
                      <span>
                    " . wptexturize($attachment->post_excerpt) . "
                      </span>
                    </{$captiontag}>";
            } $i++;
            
            $output .= ($link && $linked) ? "</a>" : "";
            $output .= "</{$icontag}>";
            $output .= "</{$itemtag}>";

        endforeach;
    endif;

    $output .= (isset($attr['type']) && ($attr['type'] == 'slideshow' || $attr['type'] == 'carousel')) ? '</ul><div class="clear"></div>' : '<div class="clear"></div></div>';
    $output .= "</div>\n<div class='clear'></div>";
    $output .= (isset($type_end)) ? $type_end : '';

    return $output;

    }
    add_shortcode('gallery', 'themewich_gallery_shortcode'); 
endif;


if ( !function_exists( 'themewich_close_unclosed_tags' ) ) :
    function themewich_close_unclosed_tags($unclosedString){ 
      preg_match_all("/<([^\/]\w*)>/", $closedString = $unclosedString, $tags); 
      for ($i=count($tags[1])-1;$i>=0;$i--){ 
        $tag = $tags[1][$i]; 
        if (substr_count($closedString, "</$tag>") < substr_count($closedString, "<$tag>")) $closedString .= "</$tag>"; 
      } 
      return $closedString; 
    } 
endif;

if ( !function_exists( 'themewich_wp_trim_excerpt' ) ) :
    function themewich_wp_trim_excerpt($text) {
        global $post;
        $raw_excerpt = $text;
        if ( '' == $text ) {
            //Retrieve the post content.
            $text = get_the_content('');
         
            //Delete all shortcode tags from the content.
            $text = strip_shortcodes( $text );
         
            $text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]&gt;', $text);
         
            $allowed_tags = '<p>,<em>,<strong>,<b>,<ul>,<li>,<ol>,<blockquote>'; /*** Add the allowed HTML tags separated by a comma.***/
            $text = strip_tags($text, $allowed_tags);
         
            $excerpt_word_count = 40; /*** change the excerpt word count to any integer you like.***/
            $excerpt_length = apply_filters('excerpt_length', $excerpt_word_count); 
         
            $excerpt_end = '... <span class="more-link"><a href="'. get_permalink($post->ID) . '" class="more-link">'.__('Read More', 'framework').'</a></span>'; /*** change the excerpt endind to something else.***/
            $excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_end);
         
            $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
            if ( count($words) > $excerpt_length ) {
                array_pop($words);
                $text = implode(' ', $words);
                $text = $text . $excerpt_more;
                $text = themewich_close_unclosed_tags($text); //close any unclosed tags
            } else {
                $text = implode(' ', $words);
            }
			
			// Remove empty paragraph tags
			$text = str_replace(array("<p></p>", "<p>&nbsp;</p>"), array("",""), $text);
        
        }
    return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
    }
    remove_filter('get_the_excerpt', 'wp_trim_excerpt');
    add_filter('get_the_excerpt', 'themewich_wp_trim_excerpt');
endif;

if ( !function_exists( 'all_excerpts_get_more_link' ) ) :
    function all_excerpts_get_more_link($post_excerpt) {
        global $post;
        if (preg_match('<span class="more-link">', $post_excerpt)) {
        return $post_excerpt;
        }
        else {
        return $post_excerpt . '<span class="more-link"><a href="'. get_permalink($post->ID) . '" class="more-link">'.__('Read More', 'framework').'</a></span>';
        }
    }
    add_filter('wp_trim_excerpt', 'all_excerpts_get_more_link');
endif;


/**
* Adds additional sizes to the gallery thumbnail dropdown
* 
* @param  array $sizes Stores all the available sizes
* 
* @return array Returns the new array with the additional sizes
*/
if (!function_exists('themewich_more_sizes')) :
    function themewich_more_sizes($sizes) {
        $sizes['fullslideshownc'] = __('Parallax Optimized', 'framework');
        $sizes['fixedslideshow'] = __('Full-Width Cropped', 'framework');
        $sizes['fixedslideshownc'] = __('Full-Width UnCropped', 'framework');

        return $sizes;
    }
    add_filter('image_size_names_choose', 'themewich_more_sizes', 4, 1);
endif;

/**
 * Class to add additional optinos to image gallery popup
 */
class Custom_Gallery_Setting {
    /**
     * Stores the class instance.
     *
     * @var Custom_Gallery_Setting
     */
    private static $instance = null;

    /**
     * Returns the instance of this class.
     *
     * It's a singleton class.
     *
     * @return Custom_Gallery_Setting The instance
     */
    public static function get_instance() {

        if ( ! self::$instance )
            self::$instance = new self;

        return self::$instance;
    }

    

    /**
     * Initialises the plugin.
     */
    public function init_plugin() {

        $this->init_hooks();
    }

    /**
     * Initialises the WP actions.
     *  - admin_print_scripts
     */
    private function init_hooks() {

        add_action( 'wp_enqueue_media', array( $this, 'wp_enqueue_media' ) );
        add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
    }


    /**
     * Enqueues the script.
     */
    public function wp_enqueue_media() {

        if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
            return;

        wp_enqueue_script(
            'custom-gallery-settings',
            get_template_directory_uri() . '/functions/js/custom-gallery-setting.js',
            array( 'media-views' )
        );

    }

    /**
     * Outputs the view template with the custom setting.
     */
    public function print_media_templates() {

        if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
            return;
        
        ?>

        <script type="text/html" id="tmpl-custom-gallery-type">
            <label class="setting gallery-type">
                <span><?php _e('Gallery Type', 'framework'); ?></span>

                <select class="type gallery-type" name="type" data-setting="type">
                    <?php

                    $types = array(
					    'gallery' => __( 'Gallery', 'framework' ),
                        'slideshow'    => __( 'Slideshow', 'framework' ),
                        'carousel' => __( 'Carousel', 'framework' )
                     );

                    foreach ( $types as $value => $name ) { ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'gallery' ); ?> >
                            <?php echo esc_html( $name ); ?>
                        </option>
                    <?php } ?>
                </select>
            </label>
        </script>

        <script type="text/html" id="tmpl-custom-gallery-cropping">
            <label class="setting gallery-crop">
                <span><?php _e('Cropping', 'framework'); ?></span>

                <select class="type" name="crop" data-setting="crop">
                    <?php

                    $types = array(
                        'crop'    => __( 'Crop Images', 'framework' ),
                        'nocrop' => __( 'Do Not Crop Images', 'framework')
                     );

                    foreach ( $types as $value => $name ) { ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'slideshow' ); ?>>
                            <?php echo esc_html( $name ); ?>
                        </option>
                    <?php } ?>
                </select>
            </label>
        </script>

        <script type="text/html" id="tmpl-custom-gallery-width">
            <label class="setting gallery-width">
                <span><?php _e('Width', 'framework'); ?></span>

                <select class="type" name="width" data-setting="width">
                    <?php

                    $types = array(
                        'fixed'   	=> __( 'Extended Width', 'framework' ),
                        'full' 		=> __( 'Full Width', 'framework'),
						'normal' 	=> __( 'Normal Width', 'framework' )
                    );

                    foreach ( $types as $value => $name ) { ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'fixed' ); ?>>
                            <?php echo esc_html( $name ); ?>
                        </option>
                    <?php } ?>
                </select>
            </label>
        </script>

        <script type="text/html" id="tmpl-custom-gallery-link">
            <label class="setting gallery-link">
                <span><?php _e('Link', 'framework'); ?></span>

                <select class="type" name="link" data-setting="link">
                    <?php

                    $types = array(
                        'full'    => __( 'Full Image', 'framework' ),
                        'none' => __( 'None', 'framework')
                    );

                    foreach ( $types as $value => $name ) { ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'link' ); ?>>
                            <?php echo esc_html( $name ); ?>
                        </option>
                    <?php } ?>
                </select>
            </label>
        </script>

        <?php
    }

}
add_action( 'admin_init', array( Custom_Gallery_Setting::get_instance(), 'init_plugin' ), 20 );


/**
 * Loads admin javascript
 * 
 * @since GridStack 1.0
 */
if (!function_exists('themewich_load_post_scripts')) :
    function themewich_load_post_scripts() {
    	wp_register_script('post-js', get_template_directory_uri() . '/functions/js/post-javascript.js', 'jquery');
    	wp_enqueue_script( 'post-js' );
    }
    add_action('admin_init', 'themewich_load_post_scripts');
endif;

if (!function_exists('themewich_remove_comment_hash')) :
	function themewich_remove_comment_hash($link) {
		// Manipulate comment link
		return $link;
	}
	add_filter( 'comment_link', 'themewich_remove_comment_hash', 10, 3 );
endif;

/**
 * Comments template
 * @since  v1.0
 */
if (!function_exists('themewich_comment')) :
    function themewich_comment($comment, $args, $depth) {

        $isByAuthor = false;
        if($comment->comment_author_email == get_the_author_meta('email')) {
            $isByAuthor = true;
        }

        $GLOBALS['comment'] = $comment; ?>
       <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
        <div class="avatar" id="avatar-user-<?php echo $comment->user_id; ?>">
            <?php echo get_avatar( $comment->comment_author_email, 65 ); ?>
        </div>
       <div id="comment-<?php comment_ID(); ?>" class="singlecomment">
            <p class="commentsmetadata">
                <cite><?php comment_date('F j, Y'); ?></cite>
            </p>
            <div class="author">
                <div class="reply"><?php echo comment_reply_link(array('depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => '<i class="icon-reply"></i> ' . __('Reply', 'framework') )); ?></div>
                <div class="name"><?php comment_author_link() ?></div>
            </div>
          <?php if ($comment->comment_approved == '0') : ?>
             <p class="moderation"><?php _e('Your comment is awaiting moderation.', 'framework') ?></p>
          <?php endif; ?>
            <div class="commenttext">
                <?php comment_text() ?>
            </div>
        </div>
    <?php
    } 
endif;

/**
 * Registers and loads front-end javascript
 * 
 * @since GridStack 1.0
 */
if (!function_exists('themewich_register_js')) :
    function themewich_register_js() {
    	if (!is_admin()) {
    		
    		// Get theme version info
    		global $themewich_theme_info;

        $ajax = (of_get_option('of_ajaxify')) ? of_get_option('of_ajaxify') : 'on';
    		
    		// Register theme javascript
				if (!wp_script_is('jquery')) wp_enqueue_script('jquery'); // Load jquery if not already loaded
        wp_register_script('modernizr', get_template_directory_uri() . '/js/modernizr.custom.min.js', 'jquery', '2.6.2', false);
        wp_register_script('history', get_template_directory_uri() . '/js/jquery.history.js', 'jquery', '1.8b2', false);
        wp_register_script('ajaxify', get_template_directory_uri() . '/js/jquery.ajaxify.js', 'jquery', '1.0.1', false);            
				wp_register_script('fitvids', get_template_directory_uri() . '/js/jquery.fitvids.js', 'jquery', '1.0', true);
				wp_register_script('ba-bbq', get_template_directory_uri() . '/js/bs-history.js', 'jquery', '1.2.1 ', true);
				wp_register_script('isotope', get_template_directory_uri() . '/js/jquery.isotope.min.js', 'jquery', '1.5.25', true);
				wp_register_script('bxslider', get_template_directory_uri() . '/js/jquery.bxslider.min.js', 'jquery', '4.1.1', true);
				wp_register_script('infinite', get_template_directory_uri() . '/js/jquery.infinitescroll.min.js', 'jquery', '1.5.100504', true);
				wp_register_script('magnificpopup', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', 'jquery', '0.9.4', true);
				wp_register_script('validate', get_template_directory_uri() . '/js/jquery.validate.min.js', 'jquery', '1.11.1', true);
				wp_register_script('superfish', get_template_directory_uri() . '/js/superfish.min.js', 'jquery', '1.7.4', true);
				wp_register_script('easing', get_template_directory_uri() . '/js/jquery.easing.1.3.js', 'jquery', '1.3', true);
				wp_register_script('themewich-tabs', get_template_directory_uri() . '/js/jquery.themewichtabs.min.js', 'jquery', '1.0', true);	      								
				wp_register_script('custom', get_template_directory_uri() . '/js/custom.js', 'jquery', $themewich_theme_info->Version, true); 

        // Localize the ajax script
        $variables_array = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'get_template_directory_uri' => get_template_directory_uri() );
        wp_localize_script('custom', 'agAjax', $variables_array);
          
    		// Enqueue javascript used on every page
    		wp_enqueue_script('jquery');
        wp_enqueue_script('modernizr');
				wp_enqueue_script('superfish');
				wp_enqueue_script('fitvids');
				wp_enqueue_script('easing');

        if ($ajax && $ajax == 'on' ) { 
            wp_enqueue_script('history');
            wp_enqueue_script('ajaxify');
        }
				
				
			
				wp_enqueue_script('custom');
    		
    	}
	  }
	  add_action('init', 'themewich_register_js');
endif;

/** What grid?
**/
function what_js(){
	if(is_page('people')){
		wp_register_script('grid', get_template_directory_uri() . '/js/grid.js', 'jquery', '1.0', true);
		wp_enqueue_script('grid');
	}else if(is_page('disciplines')){
		wp_register_script('grid-disc', get_template_directory_uri() . '/js/grid-disc.js', 'jquery', '1.0', true);
		wp_enqueue_script('grid-disc');
	}else if(is_page('philosophy')){
		wp_register_script('rslides', get_template_directory_uri() . '/js/rslides.min.js', 'jquery', true);
		wp_enqueue_script('rslides');
	}else if(is_page('work')){
		wp_register_script('dropdown', get_template_directory_uri() . '/js/dropdown.js', 'jquery', true);
		wp_enqueue_script('dropdown');
	}
}

/**
 * Loads slider javascript
 * 
 * @since GridStack 1.0
 */
if (!function_exists('themewich_print_slider_script')) :
	function themewich_print_slider_script() {
		global $add_slider;
		if ( ! $add_slider ) { return; }
	
		wp_print_scripts('bxslider');
	}
	add_action('wp_footer', 'themewich_print_slider_script');
endif;


/**
 * Loads lightbox javascript
 * 
 * @since GridStack 1.0
 */
if (!function_exists('themewich_print_lightbox_script')) :
	function themewich_print_lightbox_script() {
		global $add_lightbox;
		if ( ! $add_lightbox ) { return; }
	
		wp_print_scripts('magnificpopup');
	}
	add_action('wp_footer', 'themewich_print_lightbox_script');
endif;


/**
 * Loads isotope javascript
 * 
 * @since GridStack 1.0
 */
if (!function_exists('themewich_print_isotope_script')) :
	function themewich_print_isotope_script() {
		global $add_isotope;
		if ( ! $add_isotope ) { return; }
		
	  //wp_print_scripts('ba-bbq');
	  //wp_print_scripts('history');
		wp_print_scripts('isotope');
		wp_print_scripts('infinite');
		
	}
	add_action('wp_footer', 'themewich_print_isotope_script');
endif;


/**
 * Loads validate javascript
 * 
 * @since GridStack 1.0
 */
if (!function_exists('themewich_print_validate_script')) :
	function themewich_print_validate_script() {
		global $add_validate;
		if ( ! $add_validate ) { return; }
	
		wp_print_scripts('validate');
	}
	add_action('wp_footer', 'themewich_print_validate_script');
endif;


/**
 * Loads tabs javascript
 * 
 * @since GridStack 1.0
 */
if (!function_exists('themewich_print_tabs_script')) :
	function themewich_print_tabs_script() {
		global $add_tabs;
		if ( ! $add_tabs ) { return; }
	
		wp_print_scripts('themewich-tabs');
	}
	add_action('wp_footer', 'themewich_print_tabs_script');
endif;


/**
 * Loads iconfont stylesheet
 * 
 * @since GridStack 1.0
 */
if (!function_exists('themewich_register_iconfonts')) :
    function themewich_register_iconfonts () {
        if (!is_admin()) {
            // Edit IE CSS within their files
            wp_register_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css' );
            wp_enqueue_style( 'font-awesome' );
        }
    }
    add_action('wp_enqueue_scripts', 'themewich_register_iconfonts');
endif;


/**
 * Registers IE stylesheets
 * 
 * @since GridStack 1.0
 */
if (!function_exists('themewich_register_iecss')) :
    function themewich_register_iecss () {
    	if (!is_admin()) {
    		global $wp_styles;
    		// Edit IE CSS within their files
    		wp_enqueue_style(  "ie8",  get_template_directory_uri() . "/css/ie8.css", false, 'ie8', "all");
    		$wp_styles->add_data( "ie8", 'conditional', 'IE 8' );
    	}
    }
    add_action('init', 'themewich_register_iecss');
endif;

/**
 * Configure WP2.9+ Thumbnails
 */
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );

	set_post_thumbnail_size( 56, 56, true ); // Normal post thumbnails tinyfeatured
	add_image_size( 'medium', 600, 400, true );
	add_image_size( 'large', 1024, 700, true );

    // Recent Projects Widget
	add_image_size( 'tinyfeatured', 75, 75, true ); // Tiny Square thumbnail

    // Gallery & Slideshow Sizes
	add_image_size( 'gallery', 426, 351, true ); // For regular galleries
	add_image_size( 'fullslideshow', 1500, 600, true); // Full Slideshow Cropped 
    add_image_size( 'fullslideshownc', 1500, '', false); // Full Slideshow UnCropped 
    add_image_size( 'fixedslideshow', 960, 540, true); // Extended Slideshow Cropped
    add_image_size( 'fixedslideshownc', 960, '', false); // Extended Slideshow UnCropped

    // Home and Portfolio Index Page Sizes
	add_image_size( 'slim', 420, 210, true ); // Small Featured thumbnail
	add_image_size( 'square', 420, 425, true );
    add_image_size( 'big', 790, 598, true );

    // Blog Index Sizes
	add_image_size( 'blogonecol', 575, 325, true); // One Column Posts
	add_image_size( 'blog', 420, 215, true);  // Two-Three Column Posts
}

/**
 * Register Widget Sidebars
 */
if ( function_exists('register_sidebar') ) {
 register_sidebar(array(
  'name' => 'Blog Sidebar',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div><div class="clear"></div>',
  'before_title' => '<h4 class="widget-title">',
  'after_title' => '</h4>',
 ));
 register_sidebar(array(
  'name' => 'Page Sidebar',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div><div class="clear"></div>',
  'before_title' => '<h4 class="widget-title">',
  'after_title' => '</h4>',
 ));
 register_sidebar(array(
  'name' => 'Contact Sidebar',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div><div class="clear"></div>',
  'before_title' => '<h4 class="widget-title">',
  'after_title' => '</h4>',
 ));
 register_sidebar(array( 
  'name' => 'Footer Column 1',
  'before_widget' => '<div id="%1$s" class="widget %2$s tw-column one-fourth">',
  'after_widget' => '</div>',
  'before_title' => '<h3 class="widget-title">',
  'after_title' => '</h3>',
 ));
 register_sidebar(array( 
  'name' => 'Footer Column 2',
  'before_widget' => '<div id="%1$s" class="widget %2$s tw-column one-third">',
  'after_widget' => '</div>',
  'before_title' => '<h3 class="widget-title">',
  'after_title' => '</h3>',
 ));
 register_sidebar(array( 
  'name' => 'Footer Column 3',
  'before_widget' => '<div id="%1$s" class="widget %2$s tw-column two-fourth">',
  'after_widget' => '</div>',
  'before_title' => '<h3 class="widget-title">',
  'after_title' => '</h3>',
 ));
}

/*-----------------------------------------------------------------------------------*/
/*  Add Custom Portfolio Post Type
/*-----------------------------------------------------------------------------------*/
if (!function_exists('create_portfolio_post_types')) :
    function create_portfolio_post_types() {
        register_post_type( 'portfolio',
            array(
                  'labels' => array(
                  'name' => __( 'Portfolio', 'framework'),
                  'singular_name' => __( 'Portfolio Item', 'framework'),
                  'add_new' => __( 'Add New', 'framework' ),
                  'add_new_item' => __( 'Add New Portfolio Item', 'framework'),
                  'edit' => __( 'Edit', 'framework' ),
                  'edit_item' => __( 'Edit Portfolio Item', 'framework'),
                  'new_item' => __( 'New Portfolio Item', 'framework'),
                  'view' => __( 'View Portfolio', 'framework'),
                  'view_item' => __( 'View Portfolio Item', 'framework'),
                  'search_items' => __( 'Search Portfolio Items', 'framework'),
                  'not_found' => __( 'No Portfolios found', 'framework'),
                  'not_found_in_trash' => __( 'No Portfolio Items found in Trash', 'framework'),
                  'parent' => __( 'Parent Portfolio', 'framework'),
                ),
                'menu_icon' => get_template_directory_uri() . '/framework/admin/images/media.png',
                'public' => true,
                'rewrite' => array( 'slug' => 'portfolio'), //  Change this to change the url of your "portfolio".
                'supports' => array( 
                    'title', 
                    'editor',  
                    'thumbnail',
                    'revisions',
                    'post-formats'),
            )
        );
    }
    add_action( 'init', 'create_portfolio_post_types' );
endif;

/*-----------------------------------------------------------------------------------*/
/*  Add Custom Icons for Post Types
/*-----------------------------------------------------------------------------------*/
if (!function_exists('custom_icon')) :
    function custom_icon() {
           echo '<style type="text/css">
              #icon-edit.icon32.icon32-posts-portfolio {
                background: url('. get_stylesheet_directory_uri() . '/framework/admin/images/media_large.png) no-repeat; 
              }
             </style>';
    }
    add_action('admin_enqueue_scripts', 'custom_icon', 1);
endif;


 /**
  * Add Column to Posts and Portfolio Admin Edit Screen
  * @param  array $columns Array that stores the column number
  * @return array          New array with added column
  */
if (!function_exists('themewich_add_post_columns')) :
    function themewich_add_post_columns($columns) {
        $columns['portfolio_subheadline'] = 'Portfolio Subheadline';
        return $columns;
    }
    add_filter('manage_post_posts_columns', 'themewich_add_post_columns');
    add_filter('manage_portfolio_posts_columns', 'themewich_add_post_columns');
endif;


/**
 * Render Custom Column
 * @param  array    $column_name Name of column
 * @param  integer  $id          Id of the post
 * @return echo                  Custom meta selection
 */
if (!function_exists('themewich_render_post_columns')) :
    function themewich_render_post_columns($column_name, $id) {
        switch ($column_name) {
        case 'portfolio_subheadline':
            // show widget set
            $featured = get_post_meta( $id, 'ag_subheadline', TRUE);
            if ($featured) : 
                echo '<div id="featured-' . $id . '">' . $featured . '</div>';
           else : 
                echo '<div id="featured-' . $id . '">' . __(' ', 'framework') . '</div>';              
            endif;
        }
    }
    add_action('manage_posts_custom_column', 'themewich_render_post_columns', 10, 2);
    add_action('manage_portfolio_custom_column', 'themewich_render_post_columns', 10, 2);
endif;

/**
 * Adds featured option to quick edit menu
 * @param  string $column_name Name of the column
 * @param  string $post_type   Name of post type
 * @return html                Dropdown html
 */
if (!function_exists('themewich_add_quick_edit')) :
    function themewich_add_quick_edit($column_name, $post_type) {
        if ($column_name != 'featured') { 
            return; 
        } ?>
        <fieldset class="inline-edit-col-left">
        <div class="inline-edit-col">
            <span class="title"><?php _e('Featured', 'framework'); ?></span>
            <input type="hidden" name="themewich_featured_noncename" id="themewich_featured_noncename" value="" />
            <select name='post_featured' id='post_featured'>
                <option class='featured-option' value='No'><?php _e(' ', 'framework'); ?></option>
                <option class='featured-option' value='Yes'><?php _e('Yes', 'framework'); ?></option>
            </select>
        </div>
        </fieldset>
        <?php
    }
    add_action('quick_edit_custom_box', 'themewich_add_quick_edit', 10, 2);
    add_action('bulk_edit_custom_box', 'themewich_add_quick_edit', 10, 2 ); 
endif;


/**
 * Enqueue quickedit scripts
 * @return void 
 */
if (!function_exists('themewich_enqueue_edit_scripts')) :
    function themewich_enqueue_edit_scripts() { 
        wp_enqueue_script( 'themewich-admin-edit', get_template_directory_uri() . '/functions/js/quick_edit.js', array( 'jquery', 'inline-edit-post' ), '', true ); 
    } 
    add_action( 'admin_print_scripts-edit.php', 'themewich_enqueue_edit_scripts' ); 
endif;


/**
 * Saves quickedit info
 * @param  integer $post_id ID of the post being saved
 * @return integer          ID of post with featured data
 */
if (!function_exists('themewich_save_quick_edit_data')) :
    function themewich_save_quick_edit_data($post_id) {
        // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
        // to do anything
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
            return $post_id;    
        // Check permissions
        if (isset($_POST['post_type'])) {
            if ( 'page' == $_POST['post_type'] ) {
                if ( !current_user_can( 'edit_page', $post_id ) )
                    return $post_id;
            } else {
                if ( !current_user_can( 'edit_post', $post_id ) )
                return $post_id;
            } 
        }  

        // OK, we're authenticated: we need to find and save the data
        $post = get_post($post_id);
        if (isset($_POST['post_featured']) && ($post->post_type != 'revision')) {
            $featured_id = esc_attr($_POST['post_featured']);
            if ($featured_id)
                update_post_meta( $post_id, 'ag_featured', $featured_id);     
            else
                delete_post_meta( $post_id, 'ag_featured');     

            return $featured_id; 
        }       
         
    }
    add_action('save_post', 'themewich_save_quick_edit_data');
endif;


/**
 * Saves bulk edit data
 * @return void 
 */
if (!function_exists('themewich_save_bulk_edit')) :
    function themewich_save_bulk_edit() { 
        // get our variables 
        $post_ids = ( isset( $_POST[ 'post_ids' ] ) && !empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : array(); 
        $featured = ( isset( $_POST[ 'post_featured' ] ) && !empty( $_POST[ 'post_featured' ] ) ) ? $_POST[ 'post_featured' ] : NULL; 
        // if everything is in order 
        if ( !empty( $post_ids ) && is_array( $post_ids ) && !empty( $featured ) ) { 
            foreach( $post_ids as $post_id ) { 
                update_post_meta( $post_id, 'ag_featured', $featured ); 
            } 
        } 
    } 
    add_action( 'wp_ajax_themewich_save_bulk_edit', 'themewich_save_bulk_edit' ); 
endif;


/**
 * Creates the "Filter" taxonomy.
 * 
 * @since GridStack 1.0
 */
if (!function_exists('themewich_create_taxonomies')) :
    function themewich_create_taxonomies() {
      // Add new taxonomy, make it hierarchical (like categories)
      $labels = array(
        'name' => _x( 'Filter', 'taxonomy general name', 'framework'),
        'singular_name' => _x( 'Filter', 'taxonomy singular name', 'framework'),
        'search_items' =>  __( 'Search Filters', 'framework'),
        'all_items' => __( 'All Filters', 'framework'),
        'parent_item' => __( 'Parent Filter', 'framework'),
        'parent_item_colon' => __( 'Parent Filter:', 'framework'),
        'edit_item' => __( 'Edit Filter', 'framework'), 
        'update_item' => __( 'Update Filter', 'framework'),
        'add_new_item' => __( 'Add New Filter', 'framework'),
        'new_item_name' => __( 'New Filter Name', 'framework'),
        'menu_name' => __( 'Filters', 'framework'),
      );    
      register_taxonomy('filter',array('portfolio', 'post'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'filter' ), // This is the url slug
      ));

    }
    //hook into the init action and call the taxonomy when it fires
    add_action( 'init', 'themewich_create_taxonomies', 0 );
endif;

/**
 * Add additional functionality for Qtranslate
 */
if (function_exists('qtrans_modifyTermFormFor')) {
	add_action('filter_add_form', 'qtrans_modifyTermFormFor');
	add_action('filter_edit_form', 'qtrans_modifyTermFormFor');
}


/**
 * Function that checks if multi-dimensional array is empty
 * @param  array    $mixed  Multi-dimesional array
 * @return boolean          Returns true if the multi-dimensional 
 *                          array is empty
 */
if (!function_exists('themewich_array_empty')) :
    function themewich_array_empty($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $value) {
                if (!themewich_array_empty($value)) {
                    return false;
                }
            }
        }
        elseif (!empty($mixed)) {
            return false;
        }
        return true;
    }
endif;


/**
 * Gets the page title options from the custom fields
 * @param  integer $pageID  Id of the page
 * @return array            Array that stores the variables
 */
if (!function_exists('themewich_page_title_options')) :
    function themewich_page_title_options($pageID) {
        global $post;

        if (is_page() || is_home() || is_front_page()) :
            // Check for intro
            $options['intro']        = get_post_meta($pageID, 'ag_intro', true); // Get intro text
            $options['plural']       = ($options['intro'] && (count($options['intro'])>1)) ? true : false; // Find out if there's more than one set
            
            // Options
            $options['pause']        = ($pausenumber = get_post_meta($pageID, 'ag_delay', true)) ? $pausenumber : '6';
            $options['autoplay']     = (get_post_meta($pageID, 'ag_auto_play', true) == 'Autoplay') ? 'autoplay' : 'noautoplay'; // Get autoplay setting
            $options['adapt']        = ($options['autoplay'] == 'autoplay') ? 'noadapt' : 'adapt';  // If autoplay setting is on, turn height adapt off            
                    
        endif;

        // Title styles
        $options['titlecolor']   = (get_post_meta($pageID, 'ag_page_title_color', true) == 'Light') ? 'light' : '';
        $options['titlebg']      = ($options['titlebgcolor'] = get_post_meta($pageID, 'ag_page_title_bg_color', true)) ? $options['titlebgcolor'] : '';
        $options['opacity']      = ($options['titleopacity'] = get_post_meta($pageID, 'ag_page_title_bg_opacity', true))  ? $options['titleopacity']/100 : '0.8';
        $options['o_helper']     = ($options['opacity']) ? '<script type="text/javascript">jQuery(".titleoverlay").css("opacity", ' . $options['opacity'] . ');</script>' : '';
        $options['frontpage_id'] = get_option('page_on_front');
        $options['heading']      = $options['frontpage_id'] == $pageID ? '2' : '1';
        $options['subheading']   = $options['frontpage_id'] == $pageID ? '3' : '2';

        // Title image
        $options['titleimage']   = wp_get_attachment_image_src( get_post_thumbnail_id($pageID), 'fullslideshownc' );
        $options['titleimage']   = $options['titleimage']['0'];

        // Assemble the background style
        $options['pagebgstyle']  = "style='";
        $options['pagebgstyle']  .= ($options['titleimage'] != "") ? ' background-image:url('.$options['titleimage'].');' : "";
        $options['pagebgstyle']  .= "'";

        return $options;
    }
endif;


/**
 *  New category walker for isotope filtering
 */
if (!class_exists('Themewich_Walker_Portfolio_Filter')) :
    class Themewich_Walker_Portfolio_Filter extends Walker_Category {

        /**
         * Outputs a customized list of category links
         * 
         * @param  string  $output   String to output at the end
         * @param  array   $category Stores all category information (slug, id, name, etc.)
         * @param  integer $depth    Depth of categories
         * @param  array   $args     Extracts arguments for function
         * 
         * @return string Outputs customized string
         */
       function start_el(&$output, $category, $depth = 0, $args = array(), $current_object_id = 0) {

          extract($args);
          $cat_name = esc_attr( $category->name);
          $cat_slug = $category->slug;
          $cat_name = apply_filters( 'list_cats', $cat_name, $category );
          $link = '<a href='.$cat_slug.' class="no-ajaxy" data-filter=".'.strtolower(preg_replace('/\s+/', '-', $cat_slug)).'" ';
          if ( $use_desc_for_title == 0 || empty($category->description) )
             $link .= 'title="' . sprintf(__( 'View all projects filed under %s', 'framework'), $cat_name) . '"';
          else
             $link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
          $link .= '>';
          $link .= strip_tags (apply_filters('the_content', $cat_name));
          $link .= '</a>';
          if ( (! empty($feed_image)) || (! empty($feed)) ) {
             $link .= ' ';
             if ( empty($feed_image) )
                $link .= '(';
             $link .= '<a href="' . get_category_feed_link($category->term_id, $feed_type) . '"';
             if ( empty($feed) )
                $alt = ' alt="' . sprintf(__( 'Feed for all posts filed under %s', 'framework'), $cat_name ) . '"';
             else {
                $title = ' title="' . $feed . '"';
                $alt = ' alt="' . $feed . '"';
                $name = $feed;
                $link .= $title;
             }
             $link .= '>';
             if ( empty($feed_image) )
                $link .= $name;
             else
                $link .= "<img src='$feed_image'$alt$title" . ' />';
             $link .= '</a>';
             if ( empty($feed_image) )
                $link .= ')';
          }
          if ( isset($show_count) && $show_count )
             $link .= ' (' . intval($category->count) . ')';
          if ( isset($show_date) && $show_date ) {
             $link .= ' ' . gmdate('Y-m-d', $category->last_update_timestamp);
          }
          if ( isset($current_category) && $current_category )
             $_current_category = get_category( $current_category );
          if ( 'list' == $args['style'] ) {
              $output .= '<option class="segment-2" value=".'.$cat_slug.'" data-filter=".'.$cat_slug.'"';
              $class = 'cat-item cat-item-'.$category->term_id;
              if ( isset($current_category) && $current_category && ($category->term_id == $current_category) )
                 $class .=  ' current-cat';
              elseif ( isset($_current_category) && $_current_category && ($category->term_id == $_current_category->parent) )
                 $class .=  ' current-cat-parent';
              $output .=  '';
              $output .= ">$link\n";
           } else {
              $output .= "\t$link<br />\n";
           }
       }
    }
endif;

/**
 * This allows for the notification and installation of required 
 * plugins for the theme.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage gridstack
 * @version    2.3.6
 * @author     Thomas Griffin <thomas@thomasgriffinmedia.com>
 * @author     Gary Jones <gamajo@gamajo.com>
 * @copyright  Copyright (c) 2012, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/thomasgriffin/TGM-Plugin-Activation
 */


/**
 * Include the TGM_Plugin_Activation class.
 */
require_once dirname( __FILE__ ) . '/framework/framework-functions/class-tgm-plugin-activation.php';


/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
if (!function_exists('themewich_register_required_plugins')) :
    function themewich_register_required_plugins() {

        /**
         * Array of plugin arrays. Required keys are name and slug.
         * If the source is NOT from the .org repo, then source is also required.
         */
        $plugins = array(

            // Include Shortcodes Plugin
            array(
                'name'                  => 'Themewich Shortcodes', // The plugin name
                'slug'                  => 'themewich-shortcodes', // The plugin slug (typically the folder name)
                'source'                => get_stylesheet_directory() . '/functions/plugins/themewich-shortcodes.zip', // The plugin source
                'required'              => true, // If false, the plugin is only 'recommended' instead of required
                'version'               => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
                'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                'external_url'          => 'http://google.com', // If set, overrides default API URL and points to an external URL
            ),

        );

        // Change this to your theme text domain, used for internationalising strings
        $theme_text_domain = 'framework';

        /**
         * Array of configuration settings. Amend each line as needed.
         * If you want the default strings to be available under your own theme domain,
         * leave the strings uncommented.
         * Some of the strings are added into a sprintf, so see the comments at the
         * end of each line for what each argument will be.
         */
        $config = array(
            'domain'            => $theme_text_domain,          // Text domain - likely want to be the same as your theme.
            'default_path'      => '',                          // Default absolute path to pre-packaged plugins
            'parent_menu_slug'  => 'themes.php',                // Default parent menu slug
            'parent_url_slug'   => 'themes.php',                // Default parent URL slug
            'menu'              => 'install-required-plugins',  // Menu slug
            'has_notices'       => true,                        // Show admin notices or not
            'is_automatic'      => true,                       // Automatically activate plugins after installation or not
            'message'           => '',                          // Message to output right before the plugins table
            'strings'           => array(
                'page_title'                                => __( 'Install Required Plugins', $theme_text_domain ),
                'menu_title'                                => __( 'Install Plugins', $theme_text_domain ),
                'installing'                                => __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
                'oops'                                      => __( 'Something went wrong with the plugin API.', $theme_text_domain ),
                'notice_can_install_required'               => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
                'notice_can_install_recommended'            => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
                'notice_cannot_install'                     => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
                'notice_can_activate_required'              => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
                'notice_can_activate_recommended'           => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
                'notice_cannot_activate'                    => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
                'notice_ask_to_update'                      => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
                'notice_cannot_update'                      => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
                'install_link'                              => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
                'activate_link'                             => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
                'return'                                    => __( 'Return to Required Plugins Installer', $theme_text_domain ),
                'plugin_activated'                          => __( 'Plugin activated successfully.', $theme_text_domain ),
                'complete'                                  => __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
                'nag_type'                                  => 'updated' // Determines admin notice type - can only be 'updated' or 'error'
            )
        );

        tgmpa( $plugins, $config );

    }
    add_action( 'tgmpa_register', 'themewich_register_required_plugins' );
endif;


/**
 * Get Username and API Key from Theme Options
 */
$username = of_get_option('of_tf_username');
$api = of_get_option('of_tf_api');

if ($username && $username != '') {
    define('THEMEFOREST_USERNAME',$username);
}
if ($api && $api != '') {
    define('THEMEFOREST_APIKEY', $api);
}

/**
 * Adds custom homepage order field on publish
 */
if (!function_exists('themewich_add_field_automatically')) :
    function themewich_add_field_automatically($post_ID) {
        global $wpdb;
        if(!wp_is_post_revision($post_ID)) {
            add_post_meta($post_ID, 'homepage_order', '0', true);
        }
    }
    add_action('publish_portfolio', 'themewich_add_field_automatically');
    add_action('publish_post', 'themewich_add_field_automatically');
endif;
?>