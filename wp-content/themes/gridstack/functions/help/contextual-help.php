<?php

/*-----------------------------------------------------------------------------------*/
/* Add Contextual Help
/*-----------------------------------------------------------------------------------*/

function ag_contextual_help( $contextual_help, $screen_id, $screen ) {

	$help_screens = array(
		// Page Title Help
		'page-title' => array(
			'title' 	=> __( 'Page Title Options', 'framework'),
			'context' 	=> 'page'
		),
		// Page Title Help
		'page-intro' => array(
			'title' 	=> __( 'Page Intro Options', 'framework'),
			'context' 	=> 'page'
		)
	);

    $screen = get_current_screen();

    foreach($help_screens as $key => $help) {
    	// If it's the current page
        if ($screen_id == $help['context']) {
        	$readme = wp_remote_get( get_template_directory_uri() . '/functions/help/'.$key.'-readme.html' );

        	$screen->add_help_tab( array(
				'id'      => 'ag_'.$key.'_'.$screen_id,
				'title'   => $help['title'],
	   			'content' => $readme['body'],
			));
        }
    }
    
}
add_filter('contextual_help', 'ag_contextual_help', 10, 3);

?>