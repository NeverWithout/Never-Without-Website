<?php get_header(); 

/* Count The Number of Views For Popular Posts
================================================== */
setPostViews(get_the_ID());

if ( have_posts() ) : while ( have_posts() ) : the_post();

	get_template_part('functions/templates/standard'); 

endwhile; else: ?>
	<!-- Nothing Found -->
	<p><?php _e('Sorry, no posts matched your criteria.', 'framework'); ?></p>
    <!-- END Nothing Found -->
<?php endif; ?>
	
<?php 
/* Get Footer
================================================== */
get_footer(); ?>