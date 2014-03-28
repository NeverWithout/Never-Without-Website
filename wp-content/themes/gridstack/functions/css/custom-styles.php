<?php

/*-----------------------------------------------------------------------------------*/
/* WP Customizer
/*-----------------------------------------------------------------------------------*/

add_action('customize_register', 'ag_color_customizer');
function ag_color_customizer($wp_customize)
{
  $colors = array();
  
  $colors[] = array( 
  	'slug'=>'highlight_color', 
	'default' => '#00a498',
  	'label' => __( 'Theme Highlight Color', 'framework' ),
	'priority' => 20 
	);
  
  foreach($colors as $color)
  {

    // SETTINGS
    $wp_customize->add_setting( $color['slug'], array( 'default' => $color['default'],
    'type' => 'option', 'capability' => 'edit_theme_options' ));

    // CONTROLS
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
     $color['slug'], array( 'label' => $color['label'], 'section' => 'colors',
     'settings' => $color['slug'] )));
  }
  

}

$args = array(
	'default-color' => 'ffffff',
	'default-image' => '',
);
add_theme_support( 'custom-background', $args );


/*-----------------------------------------------------------------------------------*/
/* Print Custom Styles
/*-----------------------------------------------------------------------------------*/

function ag_customize_css() { ?>

<style type="text/css">


/* Print Highlight Background Color
/*-----------------------------------------------------------------------------------*/
.avatar-info .comment-counter,
.categories a:hover, .tagcloud a, .widget .tagcloud a, .single .categories a, .single .sidebar .categories a:hover, 
.tabswrap ul.tabs li a.active, .tabswrap ul.tabs li a:hover, .dark .tabswrap ul.tabs li a:hover, .dark .tabswrap ul.tabs li a.active, 
.pagination a.button.share:hover, #commentsubmit #submit, #cancel-comment-reply-link, .widget .categories a, .button, a.button, .widget a.button, a.more-link, .widget a.more-link, .cancel-reply p a,
.dark .button:hover, .dark a.button:hover, .dark a.more-link:hover, .tw-postshortcode a.more-link, .tw-button, a.tw-button, .tw-pricing-table .featured .tw-pricing-header, 
.gridstack .mejs-controls .mejs-time-rail .mejs-time-buffering, .gridstack .mejs-controls .mejs-time-rail .mejs-time-current, .gridstack .mejs-controls .mejs-volume-button .mejs-volume-slider,
.gridstack .mejs-controls .mejs-volume-button .mejs-volume-slider .mejs-volume-current, .gridstack .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current,
.gridstack .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current  { 
	background: <?php echo ($highlight_color = get_option('highlight_color')) ? $highlight_color : '#00a498';?>;
	border-color: <?php echo ($highlight_color = get_option('highlight_color')) ? $highlight_color : '#00a498';?>;  
	color:#fff; 
}

/* Print Highlight Color
/*-----------------------------------------------------------------------------------*/
p a, a, .subtitle a, .title a, blockquote, blockquote p, .tabswrap .tabpost a:hover, .articleinner h2 a:hover, span.date a:hover, .highlight, h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, .post h2.title a:hover, #wp-calendar tbody td a,
.author p a:hover, .date p a:hover, .widget a:hover, .widget.ag_twitter_widget span a, .dark h1 a:hover, .dark h2 a:hover, .dark h3 a:hover, .dark h3 a:hover, .dark h4 a:hover, .dark h5 a:hover, a:hover, .dark a:hover, .blogpost h2 a:hover, .blogpost .smalldetails a:hover,
a.comment-reply-link:hover .icon-reply {
	 color: <?php echo ($highlight_color = get_option('highlight_color')) ? $highlight_color : '#00a498';?>;
}

/* Print Highlight Border Color
/*-----------------------------------------------------------------------------------*/
.recent-project:hover,
.dark .recent-project:hover {
	border-color: <?php echo ($highlight_color = get_option('highlight_color')) ? $highlight_color : '#00a498';?>;
}

/* Print Heading Color
/*-----------------------------------------------------------------------------------*/
h1, h1 a,
h2, h2 a,
h3, h3 a,
h4, h4 a,
h5, h5 a,
h6, h6 a,
.widget h1 a,
.widget h2 a,
.widget h3 a,
.widget h4 a,
.widget h5 a,
.widget h6 a,
.tabswrap .tabpost a,
.more-posts a,
ul li a.rsswidget { 
	color: <?php echo ($heading_color = get_option('heading_color')) ? $heading_color : '#222222';?>; 
} 

/***************Typographic User Values *********************************/

/* Navigation Font */

.sf-menu a {
<?php 
// Get nav option
if ($sffont = of_get_option('of_nav_font')) { 
	
	echo 'font-family:"'.$sffont['face'].'", arial, sans-serif;'; 
	echo 'font-weight:'.$sffont['weight'].';';
	if (isset($sffont['style'])) {
		switch($sffont['style']) {
			case 'uppercase' :
				echo 'text-transform:uppercase; letter-spacing: 2px;';
			break;
			case 'italic':
				echo 'font-style:italic;';
			break;
		}
	}

} ?>
font-size:14px;
}

/* Slider Caption, Page Title, and Section Title Font */

.pagetitle .title {
<?php 
// Get heading font choices
if ( $headingfont = of_get_option('of_heading_font') ) { 

	echo 'font-family:"'.$headingfont['face'].'", arial, sans-serif;'; 
	echo 'font-weight:'.$headingfont['weight'].';';
	if (isset($headingfont['style'])) {
		switch($headingfont['style']) {
			case 'uppercase' :
				echo 'text-transform:uppercase; letter-spacing: 2px;';
			break;
			case 'italic':
				echo 'font-style:italic;';
			break;
		}
	}

}?>
}

/* Subtitle Font */
.pagetitle .subtitle {
<?php 
// Get heading font choices
if ( $subtitlefont = of_get_option('of_page_subtitle_font') ) { 

	echo 'font-family:"'.$subtitlefont['face'].'", arial, sans-serif;'; 
	echo 'font-weight:'.$subtitlefont['weight'].';';
	if (isset($subtitlefont['style'])) {
		switch($subtitlefont['style']) {
			case 'uppercase' :
				echo 'text-transform:uppercase; letter-spacing: 1px;';
			break;
			case 'italic':
				echo 'font-style:italic;';
			break;
		}
	}

}?>
}

h1,
h2,
h3,
h4,
.content h4,
.content h3,
.content h2,
.content h1 {
<?php 
// Get subfont option
if ($contentheadfont = of_get_option('of_content_heading_font') ) { 
	
	echo 'font-family:"'.$contentheadfont['face'].'", arial, sans-serif;'; 
	echo 'font-weight:'.$contentheadfont['weight'].';';
	if (isset($contentheadfont['style'])) {
		switch($contentheadfont['style']) {
			case 'uppercase' :
				echo 'text-transform:uppercase; letter-spacing: 2px;';
			break;
			case 'italic':
				echo 'font-style:italic;';
			break;
		}
	}

}?>
}

.content h5,
.content h6 {
<?php 
// Get subfont option
if ($contentsubfont = of_get_option('of_content_subheading_font') ) { 
	
	echo 'font-family:"'.$contentsubfont['face'].'", arial, sans-serif;'; 
	echo 'font-weight:'.$contentsubfont['weight'].';';
	if (isset($contentsubfont['style'])) {
		switch($contentsubfont['style']) {
			case 'uppercase' :
				echo 'text-transform:uppercase; letter-spacing: 2px;';
			break;
			case 'italic':
				echo 'font-style:italic;';
			break;
		}
	}

}?>

}

/* Captions*/

.wp-caption-text, .gallery-caption, .mfp-title {
<?php 
// Get subfont option
if ($captions = of_get_option('of_captions') ) { 

	echo 'font-family:"'.$captions['face'].'", arial, sans-serif;'; 
	echo 'font-weight:'.$captions['weight'].';';
	if (isset($captions['style'])) {
		switch($captions['style']) {
			case 'uppercase' :
				echo 'text-transform:uppercase; letter-spacing: 2px;';
			break;
			case 'italic':
				echo 'font-style:italic;';
			break;
		}
	}
}
?>
}

/* Button Fonts */

.button, 
a.button, 
a.more-link, 
.tw-postshortcode a.more-link,
a.tw-button, 
.tw-button, 
#submit, 
input[type='submit'], 
label, 
.detailtitle {
<?php 
// Get subfont option
if ($buttonfont = of_get_option('of_button_font') ) { 
	echo 'font-family:"'.$buttonfont['face'].'", arial, sans-serif;'; 
	echo 'font-weight:'.$buttonfont['weight'].';';
	if (isset($buttonfont['style'])) {
		switch($buttonfont['style']) {
			case 'uppercase' :
				echo 'text-transform:uppercase; letter-spacing: 2px;';
			break;
			case 'italic':
				echo 'font-style:italic;';
			break;
		}
	}
}
?>
}

/* Tiny Details Font */

h5, h5 a, h4.widget-title {  
<?php 
// Get tiny font option
if ( $tinyfont = of_get_option('of_tiny_font') ) { 
	echo 'font-family:"'.$tinyfont['face'].'", arial, sans-serif;'; 
	echo 'font-weight:'.$tinyfont['weight'].';';
	if (isset($tinyfont['style'])) {
		switch($tinyfont['style']) {
			case 'uppercase' :
				echo 'text-transform:uppercase; letter-spacing: 2px;';
			break;
			case 'italic':
				echo 'font-style:italic;';
			break;
		}
	}
}?>
}


/* Paragraph Font */

html, body, input, textarea, p, ul, ol, .button, .ui-tabs-vertical .ui-tabs-nav li a span.text,
.footer p, .footer ul, .footer ol, .footer.button, .credits p,
.credits ul, .credits ol, .credits.button, .footer textarea, .footer input, .testimonial p, 
.contactsubmit label, .contactsubmit input[type=text], .contactsubmit textarea, h2 span.date, .articleinner h1,
.articleinner h2, .articleinner h3, .articleinner h4, .articleinner h5, .articleinner h6, .nivo-caption h1,
.nivo-caption h2, .nivo-caption h3, .nivo-caption h4, .nivo-caption h5, .nivo-caption h6, .nivo-caption h1 a,
.nivo-caption h2 a, .nivo-caption h3 a, .nivo-caption h4 a, .nivo-caption h5 a, .nivo-caption h6 a,
#cancel-comment-reply-link {
<?php 
// Get paragraph option
if ($pfont = of_get_option('of_p_font')) { 
	echo 'font-family:"'.$pfont['face'].'", arial, sans-serif;'; 
	echo 'font-weight:'.$pfont['weight'].';';
	if (isset($pfont['style'])) {
		switch($pfont['style']) {
			case 'uppercase' :
				echo 'text-transform:uppercase; letter-spacing: 2px;';
			break;
			case 'italic':
				echo 'font-style:italic;';
			break;
		}
	}
} ?>
}

<?php
/* Custom CSS Box
/*-----------------------------------------------------------------------------------*/
echo ($customcss = of_get_option('of_custom_css')) ? "/* Custom CSS */ \n" . $customcss : ''; ?>

</style>
    <?php
}
add_action( 'wp_head', 'ag_customize_css');

?>