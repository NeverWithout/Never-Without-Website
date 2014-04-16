<?php 
/* 
Template Name: Disciplines Page
*/
get_header(); 

what_js();

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
            <ul id="og-grid" class="og-grid">
              <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                  <?php if( have_rows('discipline') ): ?>
                      <?php while ( have_rows('discipline') ) : the_row(); ?>
                        <li>
                          <a href="<?php the_permalink() ?>" data-largesrc="<?php the_sub_field('image'); ?>" data-title="<?php the_sub_field('discipline_name'); ?>" data-description="<?php the_sub_field('description'); ?>" data-bulletpoints="<?php the_sub_field('bullet_points'); ?>">
                            <img src="<?php the_sub_field('image'); ?>" alt="img01" />
                            <h4><?php the_sub_field('discipline_name'); ?></h4>
                          </a>
                        </li>
                    <?php endwhile; else : ?>
                  <?php endif; ?>
              <?php endwhile; endif; ?>
            </ul>
          </div>
        </div> 
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<!-- END Container Wrap -->
  <script>
    jQuery(function() {
    	if (jQuery("#og-grid").length){
    		Grid.init();              
    	}
    });
  </script>
		
<?php 
/* Get Footer
================================================== */
get_footer(); ?>