<?php get_header(); 

if ( have_posts() ) : while ( have_posts() ) : the_post(); 

    get_template_part('functions/templates/standard-portfolio'); 

endwhile; else: ?>
    <!-- Nothing Found -->
    <p><?php _e('Sorry, no posts matched your criteria.', 'framework'); ?></p>
    <!-- END Nothing Found -->
<?php endif; ?>
    
<?php 
/* Get Footer
================================================== */
get_footer(); ?>