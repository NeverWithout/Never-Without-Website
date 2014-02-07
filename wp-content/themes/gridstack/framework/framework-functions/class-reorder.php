<?php
/**
 * Reorder posts
 * 
 * @package    WordPress
 * @subpackage Metronet Reorder Posts plugin
 */


/**
 * Reorder posts
 * Adds drag and drop editor for reordering WordPress posts
 * 
 * Based on work by Scott Basgaard and Ronald Huereca
 * 
 * To use this class, simply instantiate it using an argument to set the post type as follows:
 * new Reorder( array( 'post_type' => 'post', 'order'=> 'ASC' ) );
 * 
 * @copyright Copyright (c), Metronet
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Reorder {

	/**
	 * @var $post_type 
	 * @desc Post type to be reordered
	 * @access private
	 */
	private $post_type;
	
	/**
	 * @var $page_hook
	 * @desc Page hook to add reorder scripts/styles to
	 * @access private
	 */
	private $page_hook;

	/**
	 * @var $direction 
	 * @desc ASC or DESC
	 * @access private
	 */
	private $direction;

	/**
	 * @var $heading 
	 * @desc Admin page heading
	 * @access private
	 */
	private $heading;

	/**
	 * @var $initial 
	 * @desc HTML outputted at end of admin page
	 * @access private
	 */
	private $initial;

	/**
	 * @var $final 
	 * @desc HTML outputted at end of admin page
	 * @access private
	 */
	private $final;

	/**
	 * @var $post_statush
	 * @desc The post status of posts to be reordered
	 * @access private
	 */
	private $post_status;

	/**
	 * @var $menu_label 
	 * @desc Admin page menu label
	 * @access private
	 */
	private $menu_label;
	
	/**
	 * @var $icon
	 * @desc Admin page icon
	 * @access private
	 */
	private $icon;

	/**
	 * Class constructor
	 * 
	 * Sets definitions
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 * @access public
	 * @param array $args    If not set, then uses $defaults instead
	 */
	public function __construct( $args = array() ) {

		// Parse arguments
		$defaults = array(
			'post_type'   => 'post',                     // Setting the post type to be reordered
			'order'       => 'ASC',                      // Setting the order of the posts
			'heading'     => __( 'Reorder', 'reorder' ), // Default text for heading
			'initial'     => '',                         // Initial text displayed before sorting code
			'final'       => '',                         // Initial text displayed before sorting code
			'post_status' => 'publish',                  // Post status of posts to be reordered
			'custom_page' => '',						 // In case you want a custom page to reorder combined posts
			'add_query'   => array()					 // Add additional parameters to the query
		);
		extract( wp_parse_args( $args, $defaults ) );

		// Set variables
		$this->post_type   = $post_type;
		$this->order       = $order;
		$this->heading     = $heading;
		$this->initial     = $initial;
		$this->final       = $final;
		$this->menu_label  = $menu_label;
		$this->icon        = $icon;
		$this->post_status = $post_status;
		$this->custom_page = $custom_page;
		$this->add_query   = $add_query;
		$this->page_hook   = $post_type[0] . 'themewich_page_reorder';
		
		// Add actions
		add_action( 'wp_ajax_post_sort',   array( $this, 'ajax_save_post_order'  ) );
		add_action( 'admin_menu',          array( $this, 'enable_post_sort' ), 10, 'page' );
	}


	/**
	 * Saving the post order for later use
	 *
	 * @author Ryan Hellyer <ryan@metronet.no> and Ronald Huereca <ronald@metronet.no>
	 * @since Reorder 1.0
	 * @access public
	 * @global object $wpdb  The primary global database object used internally by WordPress
	 */
	public function ajax_save_post_order() {
		global $wpdb;

		// Verify nonce value, for security purposes
		if ( !wp_verify_nonce( $_POST['nonce'], 'sortnonce' ) ) die( '' );
		
		//Get JSON data
		$custom_order	= json_decode( str_replace( "\\", '', $_POST[ 'data' ]['typeorder'] ) );
		$post_data 		= json_decode( str_replace( "\\", '', $_POST[ 'data' ]['postorder'] ) );
		
		if ($custom_order) {
			//Iterate through post data
			$this->themewich_update_posts( $post_data, 0 );
		} else {
			//Iterate through post data
			$this->update_posts( $post_data, 0 );
		} 
		
	} //end ajax_save_post_order
	
	
	/**
	 * Saving the post order recursively	 
	 *
	 * @author Andre Gagnon
	 * @access public
	 * @global object $wpdb  The primary global database object used internally by WordPress
	 */
	private function themewich_update_posts( $post_data, $parent_id ) {
		global $wpdb;
		$count = 0;
		
			foreach( $post_data as $post_obj ) {
				$post_id = absint( $post_obj->id );
				
				// Use a custom field for a post order
				$order_key = 'homepage_order';
				$order = get_post_meta($post_id, $order_key, true);
				if($order==''){
					delete_post_meta($post_id, $order_key);
					add_post_meta($post_id, $order_key, $count);
				}else{
					update_post_meta($post_id, $order_key, $count);
				}
				
				$count += 1;
				
			} //end foreach $post_data
			
	} //end update_posts
	
	
	/**
	 * Saving the post order recursively	 
	 *
	 * @author Ronald Huereca <ronald@metronet.no>
	 * @since Reorder 1.0
	 * @access public
	 * @global object $wpdb  The primary global database object used internally by WordPress
	 */
	private function update_posts( $post_data, $parent_id ) {
		global $wpdb;
		$count = 0;
	
			foreach( $post_data as $post_obj ) {
				$post_id = absint( $post_obj->id );
				$children = isset( $post_obj->children ) ? $post_obj->children : false;
				if ( $children ) 
					$this->update_posts( $children, $post_id );
					
				$wpdb->update(
					$wpdb->posts,
					array( 'menu_order' => $count, 'post_parent' => $parent_id ),
					array( 'ID'         => $post_id )
				);
				$count += 1;
				
			} //end foreach $post_data
	} //end update_posts
	

	/**
	 * Print styles to admin page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 * @access public
	 * @global string $pagenow Used internally by WordPress to designate what the current page is in the admin panel
	 */
	public function print_styles() {
		global $pagenow;

		$pages = array( 'edit.php', 'admin.php' );

		if ( in_array( $pagenow, $pages ) )
			wp_enqueue_style( 'reorderpages_style', REORDER_URL . '/admin.css' );

	}

	/**
	 * Print scripts to admin page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 * @access public
	 * @global string $pagenow Used internally by WordPress to designate what the current page is in the admin panel
	 */
	public function print_scripts() {
		global $pagenow, $hook_suffix;
		$pages = array( 'edit.php', 'admin.php' );
		
		if(is_array($this->post_type)) {
			$hierarchical = 'false';
		} else {
			$hierarchical = is_post_type_hierarchical( $this->post_type ) ? 'true' : 'false';
		}
		
		if ( in_array( $pagenow, $pages ) ) {
			wp_register_script( 'reorder_nested', REORDER_URL . '/js/jquery.mjs.nestedSortable.js', array( 'jquery-ui-sortable' ), '1.3.5', true );
			wp_enqueue_script( 'reorder_posts', REORDER_URL . '/js/sort.js', array( 'reorder_nested' ) );
			wp_localize_script( 'reorder_posts', 'reorder_posts', array(
				'expand' => esc_js( __( 'Expand', 'reorder' ) ),
				'collapse' => esc_js( __( 'Collapse', 'reorder' ) ),
				'sortnonce' =>  wp_create_nonce( 'sortnonce' ),
				'hierarchical' => $hierarchical,
			) );
		}
	}

	/**
	 * Add submenu
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 * @access public
	 */
	public function enable_post_sort() {
		$post_type   = $this->post_type;
		$custom_page = $this->custom_page;
		
		if (!$custom_page) {
			
			if ( 'post' != $post_type ) {
				$hook = add_submenu_page(
					'edit.php?post_type=' . $post_type, // Parent slug
					$this->heading,                     // Page title (unneeded since specified directly)
					$this->menu_label,                  // Menu title
					'edit_posts',                       // Capability
					'reorder-' . $post_type,            // Menu slug
					array( $this, 'sort_posts' )        // Callback function
				);
			}
			else {
				$hook = add_posts_page(
					$this->heading,                     // Page title (unneeded since specified directly)
					$this->menu_label,                  // Menu title
					'edit_posts',                       // Capability
					'reorder-posts',                    // Menu slug
					array( $this, 'sort_posts' )        // Callback function
				);
			}
		
		} 
		else {
			
			if (is_array($this->post_type)) {
				$hook = add_menu_page(
				
						$this->heading,                     // Page title (unneeded since specified directly)
						$this->menu_label,                  // Menu title
						'edit_posts',                       // Capability
						'reorder-custom',                   // Menu slug
						array( $this, 'sort_posts' ),       // Callback function
						'',
						58
				); 
			}

		}
		
		add_action( 'admin_print_styles-' . $hook,  array( $this, 'print_styles'     ) );
		add_action( 'admin_print_scripts-' . $hook, array( $this, 'print_scripts'    ) );

	}
	
	/**
	* Post Row Output
	*
	* @author Ronald Huereca <ronald@metronet.no>
	* @since Reorder 1.0.1
	* @access private
	* @param stdclass $post object to post
	*/
	private function output_row( $the_post ) {
		global $post;
		$post = $the_post;
		setup_postdata( $post );
		?>
		<li id="list_<?php the_id(); ?>" class="custom-order-<?php echo get_post_meta($post->ID, 'homepage_order', true); ?>"><div><?php the_title(); ?></div></li>
		<?php
	} //end output_row
	
	
	/**
	* Post Row Output for Hierarchical posts
	*
	* @author Ronald Huereca <ronald@metronet.no>
	* @since Reorder 1.0.1
	* @access private
	* @param stdclass $post object to post
	* @param array $all_children - array of children 
	*/
	private function output_row_hierarchical( $the_post, $post_children, $all_children ) {
		global $post;
		$post = $the_post;
		$post_id = $the_post->ID;
		
		setup_postdata( $post );
		?>
		<li id="list_<?php the_id(); ?>">
			<div><?php the_title(); ?> <a href='#' style="float: right"><?php esc_html_e( 'Expand', 'reorder' ); ?></a></div>
			<ul class='children'>
			<?php $this->output_row_children( $post_children, $all_children ); ?>
			</ul>
		</li>
		<?php
		
		?>
		<?php
	} //end output_row_hierarchical
	
	
	/**
	* Output children posts
	*
	* @author Ronald Huereca <ronald@metronet.no>
	* @since Reorder 1.0.1
	* @access private
	* @param stdclass $post object to post
	* @param array $children_pages - array of children 
	*/
	private function output_row_children( $children_pages, $all_children ) {
		foreach( $children_pages as $child ) {
			$post_id = $child->ID;
			if ( isset( $all_children[ $post_id ] ) && !empty( $all_children[ $post_id ] ) ) {
				$this->output_row_hierarchical( $child, $all_children[ $post_id ], $all_children );
			} else {
				$this->output_row( $child );
			}
			
		} //end foreach $child
	} //end output_row_children	
	

	/**
	 * HTML output
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 * @access public
	 * @global string $post_type
	 */
	public function sort_posts() {
		?>
		<style type="text/css">
		#icon-reorder-posts {
			background:url(<?php echo $this->icon; ?>) no-repeat;
		}
		</style>
		<div class="wrap">
			<?php screen_icon( 'reorder-posts' ); ?>
			<h2>
				<?php echo $this->heading; ?>
				<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" id="loading-animation" />
			</h2>
            <p><?php _e('Drag and drop to reorder your posts. Changes are automatically saved.', 'framework'); ?></p>
			<div id="reorder-error"></div>
			<?php echo $this->initial; ?>
            
			<ul id="post-list" <?php if ($this->custom_page) { echo 'class="custom_page"'; } ?>>

			<?php 
			
			// If it's a combination of post types, force non-hierarchial
            if(is_array($this->post_type)) {
                $hierarchical = false;
            } else {
                $hierarchical = is_post_type_hierarchical( $this->post_type ) ? true : false;
            }
		
			if ( $hierarchical ) {
				$args = array(
					'sort_column' => 'menu_order',
					'post_type' => $this->post_type,
				);
				if ($this->add_query) {	
					foreach ($this->add_query as $key => $added) {
						$args[$key] = $added;
					}
				}
	
				 //Get hiearchy of children/parents
				 $top_level_pages = array();
				 $children_pages = array();
				 foreach( $pages as $page ) {
					if ( $page->post_parent == 0 ) {
						//Parent page
						$top_level_pages[] = $page;
					} else {
						$children_pages[ $page->post_parent ][] = $page;
					}
				 } //end foreach
							 
				 foreach( $top_level_pages as $page ) {
					$page_id = $page->ID;
					if ( isset( $children_pages[ $page_id ] ) && !empty( $children_pages[ $page_id ] ) ) {
						//If page has children, output page and its children
						$this->output_row_hierarchical( $page, $children_pages[ $page_id ], $children_pages );
					} else {
						$this->output_row( $page );
					}
				 }
				 			 
			} else {
			
				/* If it's a custom page
				/* ======================================== */
				if ($this->custom_page) {
				  $args = array(
					'ignore_sticky_posts' 	=> 1, 	// Ignore Sticky Posts
					'post_type' 			=> array( 'post', 'portfolio' ), // Get portfolio items and Posts
					'posts_per_page' 		=> -1, // Set posts per page
					'orderby' 		 		=> 'meta_value_num', // Custom order value
					'meta_key' 		 		=> 'homepage_order', // Custom order field
					'order'          		=> 'ASC', // Order by ascending values
					'meta_query' 			=> array( array( // Query only marked as featured
													'key' => 'ag_featured',
													'value' => 'Yes',
													'compare' => '=='
													)
												)
					);
					
				/* If it's a not a custom page
				/* ======================================== */	
				} else {
					$args = array(
						'post_type'      => $this->post_type,
						'posts_per_page' => -1,
						'orderby'        => 'menu_order',
						'order'          => $this->order,
						'post_status'    => $this->post_status
					);
					
					// Add custom query if there is one
					if ($this->add_query) {	
						foreach ($this->add_query as $key => $added) {
							$args[$key] = $added;
						}
					}
				}			
			
			$post_query = new WP_Query($args);
			$posts = $post_query->get_posts();
			if ( !$posts ) return;
			foreach( $posts as $post ) {
				$this->output_row( $post );
			} //end foreach
		}
		?>
		</ul>
		<?php
		echo $this->final; 
		?>
		</div><!-- .wrap -->
		<?php
	} //end sort_posts

}
