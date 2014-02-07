<?php get_header(); ?> 

<?php 
/* #Get Page Title
======================================================*/
get_template_part('functions/templates/page-title-rotator'); ?>

<div id="postcontainer">
    <div class="container">
        <div class="ten columns singlecontent">
             <h4><?php _e('Try searching for it:', 'framework'); ?></h4>
             <p><?php get_search_form(true); ?></p>  
        </div>
        <div class="five columns offset-by-one">
          <?php /* Widget Area */   if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Page Sidebar') ) ?>
        </div> 
    </div>
    <div class="clear"></div>
</div>
<!-- END Page Content Area -->

<?php 
/* Get Footer
================================================== */
get_footer(); ?>