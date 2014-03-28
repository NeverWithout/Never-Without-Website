<?php 
/* 
Template Name: About Page
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
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
              <div class="tw-column tw-one-half tw-column-last ">
                <?php the_field('left_content'); ?>
              </div><div style="clear:both;"></div>
              <div class="tw-column tw-one-whole">
                <img src="<?php the_field('image1'); ?>" />
                <img src="<?php the_field('image2'); ?>" />
                <img src="<?php the_field('image3'); ?>" />
              </div>
              <div class="tw-column tw-one-half tw-right">
                <?php the_field('right_content'); ?>
              </div>
            <?php endwhile; endif; ?>
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