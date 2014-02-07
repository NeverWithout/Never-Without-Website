<?php 
/**
 * This file displays a page with sidebar
 */
get_header(); 

/* #Get Page Title
======================================================*/
get_template_part('functions/templates/page-title-rotator'); ?>

<div id="postcontainer">
    <div class="container">
        <div class="ten columns singlecontent">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post();  ?>
            <?php the_content(); ?>
            <?php endwhile; endif; ?>
        </div> 
        <div class="five columns sidebar offset-by-one">
          <?php /* Widget Area */   if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Page Sidebar') ) ?>
        </div> 
    </div>
    <div class="clear"></div>
</div>

<?php 
/* Get Footer
================================================== */
get_footer(); ?>