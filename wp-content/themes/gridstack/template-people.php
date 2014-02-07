<?php 
/* 
Template Name: People Page
*/
get_header(); 

global $tw_column_width; ?>

<?php 
/* #Get Page Title
======================================================*/
get_template_part('functions/templates/page-title-rotator'); ?>

<!-- Container Wrap -->
<div id="postcontainer" class="three-fourths-post">
    <div class="container">
        <div class="sixteen columns">
        	<div class="singlecontent">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post();  
                the_content(); 
            endwhile; endif; ?>
            <div class="staff-directory">
              <?php if(function_exists('staff_directory')){echo staff_directory();} ?>
            </div> 
            </div>
        </div> 
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<!-- END Container Wrap -->

<?php 
/* Get Footer
================================================== */
get_footer(); ?>