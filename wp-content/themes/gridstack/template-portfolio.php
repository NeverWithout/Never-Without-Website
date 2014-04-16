<?php 
/* 
Template Name: Portfolio Page
*/

what_js();

get_header(); 

global $tw_column_width;
global $add_isotope;

// Add isotope script
$add_isotope = true;

/* #Get Variables Used In Page
======================================================*/
$numposts   = (of_get_option('of_portfolio_posts')) ? of_get_option('of_portfolio_posts') : '10'; // Number of posts
$postclass 	= ($tw_column_width == 'sixteen') ? 'full-width-post' : 'three-fourths-post';

// Get Current Page for Pagination 
$pagething  = (is_front_page()) ? 'page' : 'paged';
$paged      = (get_query_var($pagething)) ? get_query_var($pagething) : 1; 
$autoplay   = (of_get_option('of_portfolio_autoplay') == 'true') ? 'autoplay' : 'noautoplay'; // Autplay Option
?>

<?php get_template_part('functions/templates/page-title-rotator'); ?>

<?php 
/* #Get Only The Terms From Portfolio Items
======================================================*/
wp_reset_query();
               
// Query all portfolio posts               
$wp_query = new WP_Query( array( 
  'post_type' => 'portfolio', // Portfolio Post Type
  'posts_per_page' => -1, // Get Page Number From Theme Options
  ) 
);

$term_list = array();

// Get only terms from these posts
while ($wp_query->have_posts()) : $wp_query->the_post();
  $term_list = array_merge($term_list, wp_get_post_terms($post->ID, 'filter', array("fields" => "ids")));
endwhile; 

// Remove Duplicates and convert to string
$term_list = implode(',', array_unique($term_list));
?>  
<!-- Container Wrap -->
<div id="postcontainer">

  <!-- Homepage Filter -->
	<div class="grey">
	  <div class="container">
	    <div class="sixteen columns">
			  <div class="filter filter1">			    
		      <small>Filter By Client:</small>
		      <select class="filter dropdown" id="filters filter1" data-filter-group="client">
	           <option value="" data-filter="" selected><?php _e('All', 'framework');?></option>
	           <?php if (!empty($term_list)) { 
	               wp_list_categories(array(
	                 'title_li' => '', 
	                 'include' => $term_list, 
	                 'taxonomy' => 'filter', 
									 'child_of' => '21',
	                 'show_option_none'   => '', 
	                 'walker' => new Themewich_Walker_Portfolio_Filter()
	                 ));
	            } ?>
		      </select> 	     
			  </div>      
			  <div class="filter filter2">			    
		      <small>Filter By Medium:</small>
		      <select class="filter dropdown" id="filters filter2" data-filter-group="medium">
	           <option value="" data-filter="" selected><?php _e('All', 'framework');?></option>
	           <?php if (!empty($term_list)) { 
	               wp_list_categories(array(
	                 'title_li' => '', 
	                 'include' => $term_list, 
	                 'taxonomy' => 'filter', 
									 'child_of' => '12',
	                 'show_option_none'   => '', 
	                 'walker' => new Themewich_Walker_Portfolio_Filter()
	                 ));
	            } ?>
		      </select> 	     
			  </div>
				<!--
				<div class="filter">
		      <small>Filter By Client:</small>
					<dl class="dropdown">
						<dt>
							<a>
								<span>All</span>
							</a>
						</dt>
						<dd>
				      <ul class="filter" id="filters">
		           <?php if (!empty($term_list)) { 
		               wp_list_categories(array(
		                 'title_li' => '', 
		                 'include' => $term_list, 
		                 'taxonomy' => 'filter', 
										 'child_of' => '12',
		                 'show_option_none'   => '', 
		                 'walker' => new Themewich_Walker_Portfolio_Filter()
		                 ));
		            } ?>
				      </ul> 
						</dd>
					</dl>
				</div>
				-->
			</div>
		</div>
	</div>
  <div class="clear"></div>
  <!-- END Homepage Filter -->

  <!-- Grid Area -->
  <div  class="isotopeliquid home-tope" data-value="3">


  <?php

  wp_reset_query();
  /* #Query the Sticky Posts
  ======================================================*/
  $wp_query = new WP_Query(array(
                  // Ignore Sticky Posts
                  'ignore_sticky_posts' => 1,
				  // Sorted by Drag and Drop Order
				  'orderby' => 'menu_order', 
				  // Top to Bottom
				  'order' => 'ASC', 
                  // Get portfolio items and Posts
                  'post_type' => array( 'portfolio' ),
                  // Get only the most recent
                  'posts_per_page' => $numposts,
                  // Add pagination
                  'paged' => $paged,
              ));
  
  /* #Loop through sticky posts
  ======================================================*/
  while ($wp_query->have_posts()) : $wp_query->the_post(); 
  
  /* #Get Thumbnail
  ======================================================*/
   get_template_part('functions/templates/standard');
  
  endwhile; //End Loop ?>

      <div class="clear"></div>     
  </div>
  <!-- END Grid Area -->
	<div class="message-div"></div>
    
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
  
  <?php wp_reset_query();?>
    <div class="container">
      <div class="<?php echo $tw_column_width; ?> columns">
        <div class="singlecontent">
          <?php the_content(); ?>
        </div>
      </div>
      <div class="clear"></div>
    </div>

</div>
<!-- End containerwrap -->
<script type="text/javascript">
function pageload(hash) {
  var hash = location.hash.split("#");

	var selector = jQuery('ul.filter a[href="'+hash[1]+'"]');

	var filterthis = selector.attr('data-filter');

	jQuery('.filter a').each(function(){
		jQuery(this).removeClass('active');
	});
	jQuery(selector).addClass('active');
	jQuery('.home-tope').isotope({ 
		filter: filterthis		
	});
  if(hash == "") {
    jQuery('.filter li:first-child a').addClass('active');
  }
}

jQuery("ul.filter a").click(function(){
	/*
	var hash = this.getAttribute('href');
	hash = hash.replace(/^.*#/, '');
	jQuery.history.load(hash);
	return false;
	*/
});

jQuery(document).ready(function(){
	

	jQuery('.message-div').hide();
	
	jQuery('.filter1').find('ul').attr('data-filter-group','client');
	jQuery('.filter2').find('ul').attr('data-filter-group','medium');
  jQuery.history.init(pageload);
	
  
	
});
</script>
<?php   
/* #Get Footer
======================================================*/
get_footer(); ?>