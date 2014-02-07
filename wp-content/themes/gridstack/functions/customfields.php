<?php

/*-----------------------------------------------------------------------------------*/
/* Add Actions for Custom Fields
/*-----------------------------------------------------------------------------------*/
function xxxx_add_edit_form_multipart_encoding() {

    echo ' enctype="multipart/form-data"';

}
add_action('post_edit_form_tag', 'xxxx_add_edit_form_multipart_encoding');

/*-----------------------------------------------------------------------------------*/
/* Default Variables
/*-----------------------------------------------------------------------------------*/
$prefix = 'ag_';
$url =  get_template_directory_uri() .'/admin/images/';

$tc_url = admin_url( 'customize.php' );

/*-----------------------------------------------------------------------------------*/
/* Add Colorpicker
/*-----------------------------------------------------------------------------------*/
function add_admin_scripts( $hook ) {

    global $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'section' === $post->post_type ) {     
            wp_enqueue_script('color-picker', OPTIONS_FRAMEWORK_DIRECTORY.'js/colorpicker.js', array('jquery'));
        }
    }
}
add_action( 'admin_enqueue_scripts', 'add_admin_scripts', 10, 1 );

// Admin Color Picker CSS
function add_admin_css( $hook ) {
   
            wp_enqueue_style('color-picker', OPTIONS_FRAMEWORK_DIRECTORY.'css/colorpicker.css');
 
}
add_action( 'admin_print_styles', 'add_admin_css' );

/*-----------------------------------------------------------------------------------*/
/* Create Metaboxes
/*-----------------------------------------------------------------------------------*/

$metaboxes = array(
	// Page Title Box
    
    'page_title' => array(
        'title' => __('Page Title Options', 'framework'),
        'applicableto' => 'page',
        'location' => 'normal',
        'priority' => 'high',
        'fields' => array(
            $prefix . 'page_title_color' => array(
                'title' => __('Title Text Color', 'framework'),
                'type' => 'radio',
                'description' => 'Do you want dark or light text?',
                'std' => 'Dark',
                'options' => array('Dark','Light'),
            ),
            $prefix . 'page_title_bg_color' => array(
                'title' => __('Title Background Overlay Color', 'framework'),
                'type' => 'color',
                'description' => 'Optional. Choose a background color overlay for your title block.',
                'size' => 20,
                'std' => ''
            ),
            $prefix . 'page_title_bg_opacity' => array(
                'title' => __('Background Color Opacity', 'framework'),
                'type' => 'text',
                'description' => 'Enter a number 1 - 100 for your background color opacity.',
                'size' => 40,
                'std' => '80'
            ),

        )
    ),

    // Page Intro Box 
    'page_intro_box' => array(
        'title' => __('Page Intro', 'framework'),
        'applicableto' => 'page',
        'location' => 'normal',
        'priority' => 'high',
        'fields' => array(
			$prefix . 'intro' => array(
                'title' => __('Page Intro', 'framework'),
                'type' => 'repeatable',
                'description' => __('This replaces the page title. Add more than one for a text slideshow.', 'framework'),
                'size' => 40,
            ),
            $prefix . 'auto_play' => array(
                'title' => __('Intro Autoplay', 'framework'),
                'type' => 'radio',
                'description' => __('Choose your autoplay option for your page intro carousel.', 'framework'),
                'std' => 'No-Autoplay',
                'options' => array('Autoplay','No-Autoplay'),
            ),
            $prefix . 'delay' => array(
                'title' => __('Autoplay Delay', 'framework'),
                'type' => 'text',
                'size' => 40,
                'description' => __('Enter the number of seconds for your autoplay delay.', 'framework'),
                'std' => '5',
            ),
           /* $prefix . 'adaptive' => array(
                'title' => __('Adaptive Height', 'framework'),
                'type' => 'radio',
                'description' => __('Do you want the carousel to adjust the height for each slide? Otherwise, titles will vertically center.', 'framework'),
                'std' => __('Yes','framework'),
                'options' => array( 
                    __('Yes','framework'), 
                    __('No', 'framework')
                ),
            ),
            $prefix . 'intro_button_help' => array(
                'title' => __('Help', 'framework'),
                'type' => 'help',
                'description' => __('Need Additional Assitance?', 'framework'),
                'size' => 40,
                'std' => '',
                'help' => '#tab-link-ag_page_intro_help_page'
            ),*/
        )
    ),



    'portfolio_featured_box' => array(
        'title' => __('Feature on Homepage', 'framework'),
        'applicableto' => 'portfolio',
        'location' => 'side',
        'priority' => 'high',
        'fields' => array(
            $prefix . 'featured' => array(
                'title' => '',
                'type' => 'radio',
                'description' => '',
                'std' => 'No',
                'options' => array('Yes','No'),
            ),            
        )
    ),

    'post_featured_box' => array(
        'title' => __('Feature on Homepage', 'framework'),
        'applicableto' => 'post',
        'location' => 'side',
        'priority' => 'high',
        'fields' => array(
            $prefix . 'featured' => array(
                'title' => '',
                'type' => 'radio',
                'description' => '',
                'std' => 'No',
                'options' => array('Yes','No'),
            ),            
        )
    ),

	// Post Box
    'post_box' => array(
        'title' => __('Post Options', 'framework'),
        'applicableto' => 'post',
        'location' => 'normal',
        'priority' => 'high',
        'fields' => array(
            $prefix . 'subheadline' => array(
                'title' => __('Post Subheadline', 'framework'),
                'type' => 'textarea',
                'description' => 'Enter a subheadline for your post.',
                'std' => '',
                'size' => 2,
            ),
            $prefix . 'featured_size' => array(
                'title' => __('Featured Thumbnail Size', 'framework'),
                'type' => 'radio',
                'description' => 'Choose a size for the post in the grid.',
                'std' => 'Square',
                'options' => array('Big','Square','Slim'),
            ),
            $prefix . 'fullwidth' => array(
                'title' => __('Single Content Width', 'framework'),
                'type' => 'radio',
                'description' => 'Choose a size for the post in the grid.',
                'std' => '3/4',
                'options' => array('3/4','Full'),
            ),
           /* $prefix . 'post_size_button_help' => array(
                'title' => __('Help', 'framework'),
                'type' => 'help',
                'description' => 'Need Additional Assitance?',
                'size' => 40,
                'std' => '',
                'help' => '#tab-link-ag_post_size_help_page'
            ), */
        )
    ),

    // Post Box
    'portfolio_box' => array(
        'title' => __('Portfolio Options', 'framework'),
        'applicableto' => 'portfolio',
        'location' => 'normal',
        'priority' => 'high',
        'fields' => array(
            $prefix . 'subheadline' => array(
                'title' => __('Portfolio Subheadline', 'framework'),
                'type' => 'textarea',
                'description' => 'Enter a subheadline for your post.',
                'std' => '',
                'size' => 2,
            ),
            $prefix . 'featured_size' => array(
                'title' => __('Featured Thumbnail Size', 'framework'),
                'type' => 'radio',
                'description' => 'Choose a size for the post in the grid.',
                'std' => 'Square',
                'options' => array('Big','Square','Slim'),
            ),
            $prefix . 'fullwidth' => array(
                'title' => __('Single Content Width', 'framework'),
                'type' => 'radio',
                'description' => 'Choose a size for the post in the grid.',
                'std' => '3/4',
                'options' => array('3/4','Full'),
            ),
            /*$prefix . 'portfolio_size_button_help' => array(
                'title' => __('Help', 'framework'),
                'type' => 'help',
                'description' => 'Need Additional Assitance?',
                'size' => 40,
                'std' => '',
                'help' => '#tab-link-ag_portfolio_size_help_page'
            ), */
        )
    )
);

add_action( 'admin_init', 'add_post_format_metabox' );
function add_post_format_metabox() {
    global $metaboxes;
    if ( ! empty( $metaboxes ) ) {
        foreach ( $metaboxes as $id => $metabox ) {
            add_meta_box( $id, $metabox['title'], 'show_metaboxes', $metabox['applicableto'], $metabox['location'], $metabox['priority'], $id );
        }
    }
}

function show_metaboxes( $post, $args ) {
    global $metaboxes;
	
    $fields = $tabs = $metaboxes[$args['id']]['fields'];
    /** Nonce **/
    $output = '<style>.custom_preview_image { max-width:300px; } </style><input type="hidden" name="post_format_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';
 
	$output .= '<table class="form-table">';
 
	foreach ($fields as $id => $field) {

		// get current post meta data
		$meta = get_post_meta($post->ID, $id , true);
		if (!$meta) $meta = '';
		
		switch ($field['type']) {
 
			
			//If Text		
			case 'text':
			
    			if (isset($field['hide']) && isset($field['show'])) {
    				$output .= '<tr class="' . $id . ' '. $field['hide'] .' show-' . $field['show'] . '">';
    			} else {
    				$output .= '<tr class="' . $id . '">';
    			}
    			$output .= '<th style="width:200px"><label for="'. $id . '"><strong>'. $field['title']. '</strong><span style="line-height:20px; display:block; color:#999; margin:5px 0 0 0;">'. $field['description'].'</span></label></th>'.
    					   '<td>';
    			$output .= '<input id="ag_' . $id . '" type="text" name="' . $id . '" value="' ;
                $output .= ($meta) ? $meta : $field['std'];
                $output .= '" size="' . $field['size'] . '" style="width:100%; margin-right: 20px; float:left; padding:10px;" />';
			
			break;
			
			//If Text		
			case 'color':
			
			if (isset($field['hide']) && isset($field['show'])) {
				$output .= '<tr class="' . $id . ' '. $field['hide'] .' show-' . $field['show'] . '">';
			} else {
				$output .= '<tr class="' . $id . '">';
			}
			if (!$meta){
				if ( isset($field['std']) ) $meta = $field['std']; 
			}
			$output .= '<th style="width:200px"><label for="'. $id . '"><strong>'. $field['title']. '</strong><span style="line-height:20px; display:block; color:#999; margin:5px 0 0 0;">'. $field['description'].'</span></label></th>'.
					   '<td>';
			$output .= '<div id="' .  $id  . '_color_picker" class="colorSelector" style="margin-right: 6px; margin-top: 6px;"><div style="background-color:' . $meta . '"></div></div>';
			$output .= '<input class="colorbox_' . $id . '" id="ag_' . $id . '" type="text" name="' . $id . '" value="' . $meta . '" size="' . $field['size'] . '" style=" margin-right: 20px; float:left; padding:10px;" />';
			break;
			
						// image
			case 'image':
			
			if (isset($field['hide']) && isset($field['show'])) {
				$output .= '<tr class="' . $id . ' '. $field['hide'] .' show-' . $field['show'] . '">';
			} else {
				$output .= '<tr class="' . $id . '">';
			}
		
			$output .= '<th style="width:200px"><label for="'. $id . '"><strong>'. $field['title']. '</strong><span style="line-height:20px; display:block; color:#999; margin:5px 0 0 0;">'. $field['description'].'</span></label></th>'.
					   '<td>';
				$image = get_template_directory_uri().'/images/image-upload.png';
				$output .= '<span class="custom_default_image" style="display:none">'.$image.'</span>';
				if ($meta) { $image = wp_get_attachment_image_src($meta, 'medium');	$image = $image[0]; }
				$output .=	'<input name="'. $id .'" type="hidden" class="custom_upload_image" value="'.$meta.'" />
							<img src="'.$image.'" class="custom_preview_image" alt="" /><br />
								<input class="custom_upload_image_button button" type="button" value="Choose Image" />
								<small><a href="#" class="custom_clear_image_button">Remove Image</a></small>
								<br clear="all" />';
			break;

			case 'help':
			$output .= '<tr class="' . $id . '">';
			$output .= '<th style="width:200px"></th>'.
					   '<td>';	
			
			if (isset($field['help'])) {
					$output .= '<a href="#" target="_blank" class="helpbutton" rel="'.$field['help'].'" onclick="return false;">Need Help?</a>';
			}
			
			break;
			
			//If textarea		
			case 'textarea':
			
			if (isset($field['hide']) && isset($field['show'])) {
				$output .= '<tr class="' . $id . ' '. $field['hide'] .' show-' . $field['show'] . '">';
			} else {
				$output .= '<tr class="' . $id . '">';
			}
			
			$output .= '<th style="width:200px"><label for="'. $id . '"><strong>'. $field['title']. '</strong><span style="line-height:18px; display:block; color:#999; margin:5px 0 0 0;">'. $field['description'].'</span></label></th>'.
					   '<td>';
			$output .= '<textarea id="' . $id . '" name="' . $id . '" rows="'. $field['size'] .'" cols="5" style="width:100%; margin-right: 20px; float:left;">' . $meta . '</textarea>';
			
			break;
 
			//If Button	
			case 'button':
				$output .= '<input style="float: left;" type="button" class="button" name="'. $id . '" id="'. $id . '"value="'. $meta . '" />';
				$output .= 	'</td>'.
			'</tr>';
			
			break;
			
			
			//If Select	
			case 'select':
			
			if (isset($field['hide']) && isset($field['show'])) {
				$output .= '<tr class="' . $id . ' '. $field['hide'] .' show-' . $field['show'] . '">';
			} else {
				$output .= '<tr class="' . $id . '">';
			}
			
				$output .= '<th style="width:200px"><label for="'. $id . '"><strong>'. $field['title']. '</strong><span style=" display:block; color:#999; margin:5px 0 0 0;">'. $field['description'].'</span></label></th>'.
						   '<td>';
			
				$output .='<select name="'.$id .'" style="width:300px; height:auto; padding:10px;">';
			
				foreach ($field['options'] as $option) {
					
					$output .='<option';
					if ($meta == $option ) { 
						$output .= ' selected="selected"'; 
					}
					$output .='>'. $option .'</option>';
				
				} 
				
				$output .='</select>';
			
			break;
	
			//If Radio Button
			case 'radioshow':
			
			
				$output .= '<tr class="' . $id . ' radioshow" data-url=".show-'.$id.'">';
			
			
				$output .= '<th style="width:200px"><label for="'. $id . '"><strong>'. $field['title']. '</strong><span style=" display:block; color:#999; margin:5px 0 0 0;">'. $field['description'].'</span></label></th>'.
						   '<td>';
			
				foreach ($field['options'] as $option) {
					$output .='<input style= "margin-right: 10px; margin-bottom: 5px;" type="radio"';
						if ($meta == $option ) { 
							$output .= 'checked ';
						} else if (!$meta && $option == $field['std']  ) {
							$output .= 'checked ';
						}
					
					$output .= ' name="'.$id .'" value="'.$option .'">' . $option .' <br />';						
					
				} 
			
			break;
			
				
			//If Radio Button
			case 'radio':
			
			if (isset($field['hide']) && isset($field['show'])) {
				$output .= '<tr class="' . $id . ' '. $field['hide'] .' show-' . $field['show'] . '">';
			} else {
				$output .= '<tr class="' . $id . '">';
			}
			

            if ($field['title'] && $field['title'] !='') {
				$output .= '<th style="width:200px"><label for="'. $id . '"><strong>'. $field['title']. '</strong><span style=" display:block; color:#999; margin:5px 0 0 0;">'. $field['description'].'</span></label></th>';
            }

			$output .= '<td>';

			
				foreach ($field['options'] as $option) {
					$output .='<input style= "margin-right: 10px; margin-bottom: 5px;" type="radio"';
						if ($meta == $option ) { 
							$output .= 'checked ';
						} else if (!$meta && $option == $field['std']  ) {
							$output .= 'checked ';
						}
					
					$output .= ' name="'.$id .'" value="'.$option .'">' . $option .' <br />';						
					
				} 
			
			break;
			
			// repeatable
			case 'repeatable':

            /*if (isset($field['hide']) && isset($field['show'])) {
                    $output .= '<tr class="' . $id . ' '. $field['hide'] .' show-' . $field['show'] . '">';
                } else {
                    $output .= '<tr class="' . $id . '">';
                }

                var_dump($meta);
                $i = 0;

                $output .= '<th style="width:200px"><label for="'. $id . '"><strong>'. $field['title']. '</strong><span style="line-height:20px; display:block; color:#999; margin:5px 0 0 0;">'. $field['description'].'</span></label></th>'.
                           '<td>';
                $output .= '<ul id="' . $id . '-repeatable" class="custom_repeatable">';          
                $output .= '<li><input id="'.$id.'['.$i.']" type="text" name="'.$id.'['.$i.']" value="' . $meta[$i]. '" size="' . $field['size'] . '" style="width:100%; margin-right: 20px; float:left; padding:10px;" /></li>';
                $i++;
                $output .= '<li><input id="'.$id.'['.$i.']" type="text" name="'.$id.'['.$i.']" value="' . $meta[$i] . '" size="' . $field['size'] . '" style="width:100%; margin-right: 20px; float:left; padding:10px;" /></li>';

                $output .= '</ul>'; 


            break; */
			
			if (isset($field['hide']) && isset($field['show'])) {
				$output .= '<tr class="' . $id . ' '. $field['hide'] .' show-' . $field['show'] . '">';
			} else {
				$output .= '<tr class="' . $id . '">';
			}
			
				$output .= '<th style="width:200px"><label for="'. $id . '"><strong>'. $field['title']. '</strong><span style=" display:block; color:#999; margin:5px 0 0 0;">'. $field['description'].'</span></label></th>'.
						   '<td>';
				
				$output .= '<ul id="' . $id . '-repeatable" class="custom_repeatable">';
										
				// uncomment to debug
				// var_dump($meta);
				
				// If there's meta information and it has one or more values
				if ($meta && (count($meta)>=1)) {
					
					// For piece of meta information
					foreach($meta as $index => $row) :

                    $output .= '<li><span class="sort hndle" style="margin-right:15px; float:left;color: #999; font-size: 16px;">| | |</span>
                                    <div style="border:1px solid #dcdcdc; background: #f3f3f3; border-radius:2px; padding: 20px 20px 5px 20px; float:left; margin-bottom:30px; margin-right:20px; width:80%;">

                                        <label for="'.$id.'['.$index.'][0]" style="margin-bottom:5px;">
                                            <strong>Intro Headline</strong>
                                        </label><br />
                                        <input id="'.$id.'['.$index.'][0]" type="text" name="'.$id.'['.$index.'][0]" value="' . htmlspecialchars($meta[$index][0]) . '" size="' . $field['size'] . '" placeholder="(' . __('Optional', 'framework') .')" style="width:100%; margin-right: 20px; float:left; padding:10px; margin-bottom:15px;" /><br />                           
                                        
                                        <label for="'.$id.'['.$index.'][1]" style="margin-bottom:5px;">
                                            <strong>Intro Subheadline</strong>
                                        </label><br />
                                        <textarea id="'.$id.'['.$index.'][1]" name="'.$id.'['.$index.'][1]" rows="3" cols="5" placeholder="(' . __('Optional', 'framework') .')" style="width:100%; margin-right: 20px; float:left; padding:10px; margin-bottom:15px;">' . htmlspecialchars($meta[$index][1]) . '</textarea><br />
                                        
                                        <label for="'.$id.'['.$index.'][2]" style="margin-bottom:5px;">
                                            <strong>Button Text</strong>
                                        </label><br />
                                        <input type="text" name="'.$id.'['.$index.'][2]" id="'.$id.'['.$index.'][2]" value="' . htmlspecialchars($meta[$index][2]) . '" placeholder="(' . __('Optional', 'framework') .')" style="width:100%; margin-bottom:15px; height:auto; padding:10px; margin-bottom:15px;"><br />
                                        
                                        <label for="'.$id.'['.$index.'][3]" style="margin-bottom:5px;">
                                            <strong>Button Link</strong>
                                        </label><br />
                                        <input type="text" name="'.$id.'['.$index.'][3]" id="'.$id.'['.$index.'][3]" value="' . htmlspecialchars($meta[$index][3]) . '" placeholder="http://" style="width:100%; margin-bottom:15px; height:auto; padding:10px; margin-bottom:15px;"><br />
                                        
                                    </div><a class="repeatable-remove button" href="#">-</a><div style="clear:both"></div></li>'; 							
        			endforeach;
					
					
				// Else if the $meta information is blank
				} else { 

                $output .= '<li><span class="sort hndle" style="margin-right:15px; float:left;color: #999; font-size: 16px;">| | |</span>
                            <div style="border:1px solid #dcdcdc; background: #f3f3f3; border-radius:2px; padding: 20px 20px 5px 20px; float:left; margin-bottom:30px; margin-right:20px; width:80%;">

                                <label for="'.$id.'[0][0]" style="margin-bottom:5px;">
                                    <strong>Intro Headline</strong>
                                </label><br />
                                <input id="'.$id.'[0][0]" type="text" name="'.$id.'[0][0]" size="' . $field['size'] . '" placeholder="(' . __('Optional', 'framework') .')" style="width:100%; margin-right: 20px; float:left; padding:10px; margin-bottom:15px;" /><br />                           
                                
                                <label for="'.$id.'[0][1]" style="margin-bottom:5px;">
                                    <strong>Intro Subheadline</strong>
                                </label><br />
                                <textarea id="'.$id.'[0][1]" name="'.$id.'[0][1]" rows="3" cols="5" placeholder="(' . __('Optional', 'framework') .')" style="width:100%; margin-right: 20px; float:left; padding:10px; margin-bottom:15px;"></textarea><br /> 
                                
                                <label for="'.$id.'[0][2]" style="margin-bottom:5px;">
                                    <strong>Button Text</strong>
                                </label><br />
                                <input type="text" name="'.$id.'[0][2]" id="'.$id.'[0][2]"  placeholder="(' . __('Optional', 'framework') .')" style="width:100%; margin-bottom:15px; height:auto; padding:10px; margin-bottom:15px;"><br />
                                
                                <label for="'.$id.'[0][3]" style="margin-bottom:5px;">
                                    <strong>Button Link</strong>
                                </label><br />
                                <input type="text" name="'.$id.'[0][3]" id="'.$id.'[0][3]" placeholder="http://" style="width:100%; margin-bottom:15px; height:auto; padding:10px; margin-bottom:15px;"><br />
                                
                            </div><a class="repeatable-remove button" href="#">-</a><div style="clear:both"></div></li>';                                   
            

				}
				$output .= '</ul><a class="repeatable-add button" href="#">+ Add More</a>';
					
			break;
			
		
		}

	}
 
	$output .= '</table>';
	
   echo $output;
}

add_action( 'save_post', 'save_metaboxes' );



function save_metaboxes( $post_id ) {
    global $metaboxes;
    // verify nonce
    if ( isset( $_POST['post_format_meta_box_nonce']) && ! wp_verify_nonce( $_POST['post_format_meta_box_nonce'], basename( __FILE__ ) ) )
        return $post_id;
    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;
		
	$post_type = get_post_type();	
    // check permissions
    if ( 'page' == $post_type ) {
        if ( ! current_user_can( 'edit_page', $post_id ) )
            return $post_id;
    } elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }
    
    // loop through fields and save the data
    foreach ( $metaboxes as $id => $metabox ) {
        // check if metabox is applicable for current post type
        if ( $metabox['applicableto'] == $post_type ) {

            $fields = $metaboxes[$id]['fields'];
            foreach ( $fields as $id => $field ) {
                $old = get_post_meta( $post_id, $id, true );
				if (isset($_POST[$id])) {
    				$new = $_POST[$id];
				} else {
    				$new = null;	
				}
                if (($new && $new != $old) || ($new && $new == 0)) {
					update_post_meta( $post_id, $id, $new );
				}
				elseif ( '' == $new && $old || ! isset( $_POST[$id] ) ) {
					delete_post_meta( $post_id, $id, $old );
				}
            }
        }
    }
}
?>