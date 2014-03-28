<?php 
/* 
Template Name: Philosophy Page
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
              <div class="tw-column tw-one-sixth">
                <a href="#" class="nav rslides_nav rslides1_nav prev">Previous</a>
              </div>
              <div class="tw-column tw-two-third">                
                <ul class="rslides">
                  <?php if( have_rows('philosophy') ): ?>
                      <?php while ( have_rows('philosophy') ) : the_row(); ?>
                        <li>
                          <h2><?php the_sub_field('title'); ?></h2>
                          <?php the_sub_field('description'); ?>
                        </li>
                      <?php endwhile; else : ?>
 
 
                  <?php endif; ?>
                </ul>
              </div>
              <div class="tw-column tw-one-sixth tw-column-last">
                <a href="#" class="nav rslides_nav rslides1_nav next">Next</a>
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