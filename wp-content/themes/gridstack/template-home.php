<?php 
/* 
Template Name: Homepage
*/
get_header(); 

global $tw_column_width;
global $add_isotope;

// Add isotope script
$add_isotope = true;

/* #Get Variables Used In Page
======================================================*/
$numposts   = (of_get_option('of_home_posts')) ? of_get_option('of_home_posts') : '10'; // Number of posts
$postclass 	= ($tw_column_width == 'sixteen') ? 'full-width-post' : 'three-fourths-post';

// Get Current Page for Pagination 
$pagething  = (is_front_page()) ? 'page' : 'paged';
$paged      = (get_query_var($pagething)) ? get_query_var($pagething) : 1; 
$autoplay   = (of_get_option('of_home_autoplay') == 'true') ? 'autoplay' : 'noautoplay'; // Autplay Option
?>

<?php 
/* #Get Page Title
======================================================*/
get_template_part('functions/templates/page-title-rotator'); ?>

<!-- Container Wrap -->
<div id="postcontainer">

  <!-- Homepage Filter -->
  <div class="container filtercontainer">
    <div class="sixteen columns">
      <ul class="filter" id="filters">
        <li><a href="#" data-filter="*" class="active no-ajaxy"><?php _e('All', 'framework');?></a></li>
        <?php wp_list_categories(array(
          'title_li'  => '', 
          'taxonomy'  => 'filter', 
          'show_option_none'   => '', 
          'walker'    => new Themewich_Walker_Portfolio_Filter()
          )); ?>
      </ul> 
    </div>      
    <div class="clear"></div>
  </div>
  <!-- END Homepage Filter -->

  <!-- Grid Area -->
  <div  class="isotopeliquid" data-value="3">

  <?php
  /* #Query the featured posts
  ======================================================*/
  $args = array(
	'ignore_sticky_posts' 	=> 1, 	// Ignore Sticky Posts
	'post_type' 			  => array( 'post', 'portfolio' ), // Get portfolio items and Posts
	'posts_per_page' 		=> $numposts, // Set posts per page
	'paged' 				    => $paged, 	// Add pagination
	'orderby' 		 		  => 'meta_value_num', // Custom order value
	'meta_key' 		 		  => 'homepage_order', // Custom order field
	'order'          		=> 'ASC', // Order by ascending values
	'meta_query' 			  => array( 
    array( // Query only marked as featured
			'key' => 'ag_featured',
			'value' => 'Yes',
			'compare' => '=='
			)
	 )
	);
	$wp_query = new WP_Query($args);
  
  /* #Loop through posts
  ======================================================*/
  while ($wp_query->have_posts()) : $wp_query->the_post(); 
  
  /* #Get Proper Post Format for Thumbnail
  ======================================================*/
  get_template_part('functions/templates/standard');
  
  endwhile; //End Loop ?>

  <div class="clear"></div>     
  </div>
  <!-- END Grid Area -->
    
  <!-- Pagination -->
  <?php if (get_next_posts_link()) : ?>
  <div class="container infinite-pagination">
    <div class="sixteen columns">
        <p class="more-posts"><?php next_posts_link(__('Load More Work', 'framework')); ?></p>
        <div class="clear"></div>
    </div>
  </div>
  <?php endif; ?>
  <!-- END Pagination -->

  <?php wp_reset_query();?>

  <!-- Page Content -->
  <div class="container">
    <div class="<?php echo $tw_column_width; ?> columns">
      <div class="singlecontent">
        <?php the_content(); ?>
      </div>
    </div>
    <div class="clear"></div>
  </div>
  <!-- END Page Content -->

</div>
<!-- End containerwrap -->

<?php   
/* #Get Footer
======================================================*/
get_footer(); ?>