<?php 
/**
 * This file displays the blog category
 */
get_header(); 

global $add_isotope;

// Add isotope script
$add_isotope = true;

// Get current filter
$current_filter 	    = get_query_var('filter');
$current_filter_tax   = get_terms('filter', 'slug='.$current_filter); 
$numposts   		      = (of_get_option('of_portfolio_posts')) ? of_get_option('of_portfolio_posts') : '10'; // Number of posts

// Get Portfolio Page ID and URL      
$portfolio_pages = get_pages(array(
  'meta_key'    => '_wp_page_template',
  'meta_value'  => 'template-portfolio.php'
));

// If there's not template-portfolio.php page set
if (!$portfolio_pages) {
  $portfolio_pages = get_pages(array(
    'meta_key'    => '_wp_page_template',
    'meta_value'  => 'template-portfolio-fixed.php'
  ));
}

// Set the portfolio page ID
foreach($portfolio_pages as $port){
    $pageID = $port->ID;
}

/**
 * Get variables for page options
 */
$tw_options = themewich_page_title_options($pageID);

/**
 * Get Page Title 
 */
get_template_part('functions/templates/regular-title'); 

/* #Get Only The Terms From These Filtered Portfolios
======================================================*/
$term_list = array();

// Get only terms from these posts
while ($wp_query->have_posts()) : $wp_query->the_post();
    $term_list = array_merge($term_list, wp_get_post_terms($post->ID, 'filter', array("fields" => "ids")));
endwhile; 

$term_list = array_unique($term_list); // Remove Duplicate
if(($key = array_search($current_filter_tax[0]->term_taxonomy_id, $term_list)) !== false) {
    unset($term_list[$key]); // Remove Current term
}
$term_list = implode(',', $term_list); 
?> 

<!-- Container Wrap -->
<div id="postcontainer">

  <!-- Homepage Filter -->
  <div class="container filtercontainer">
      <div class="sixteen columns">
          <ul class="filter" id="filters">
            <li><a href="" data-filter="<?php echo '.'.$current_filter; ?>" class="active"><?php echo $current_filter_tax[0]->name; ?></a></li>
               <?php if (!empty($term_list)) { 
                  wp_list_categories(array(
                    'title_li'    => '', 
                    'include'     => $term_list, 
                    'taxonomy'    => 'filter', 
                    'show_option_none'   => '', 
                    'walker'      => new Themewich_Walker_Portfolio_Filter()
                    ));
                  } ?>
          </ul> 
      </div>      
      <div class="clear"></div>
  </div>
  <!-- END Homepage Filter -->

  <!-- Grid Area -->
  <div class="container">
    <div class="sixteen columns isotopecontainer" data-value="3">
    <?php

    /* #Query the Posts
    ======================================================*/
    $wp_query = new WP_Query(array(
                    // Ignore Sticky Posts
                    'ignore_sticky_posts' => 1,
                    // Add pagination
                    'paged'          => $paged,
  				          // Get only the most recent
                    'posts_per_page' => $numposts,
                    // Only Posts From Current Filter
                    'filter'         => $current_filter 
                ));
    
    /* #Loop through posts
    ======================================================*/
    while ($wp_query->have_posts()) : $wp_query->the_post(); 
    
    /* #Get Thumbnail
    ======================================================*/
    get_template_part('functions/templates/standard');
    
    endwhile; //End Loop ?>

    <div class="clear"></div>     
    </div>
  </div>
  <!-- END Grid Area -->
    
  <!-- Pagination -->
  <?php if (get_next_posts_link()) : ?>
  <div class="container">
    <div class="sixteen columns">
        <p class="more-posts"><?php next_posts_link(__('Load More Work', 'framework')); ?></p>
        <div class="clear"></div>
    </div>
  </div>
  <?php endif; ?>
  <!-- END Pagination -->

</div>
<!-- End containerwrap -->

<?php   
/* #Get Footer
======================================================*/
get_footer(); ?>