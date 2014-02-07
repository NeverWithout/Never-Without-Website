<?php 
/* 
Template Name: Full Width Page
*/
get_header(); ?>

<?php 
/* #Get Page Title
======================================================*/
get_template_part('functions/templates/page-title-rotator'); ?>

<div id="postcontainer">
    <div class="container">
        <div class="sixteen columns">
            <div class="singlecontent">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post();  
                the_content(); 
            endwhile; endif; ?>
            </div>
        </div> 
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>

<?php 
/* Get Footer
================================================== */
get_footer(); ?>