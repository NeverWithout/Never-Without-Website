<?php 
/**
 * This file displays a standard WordPress post.
 */

// If post is single.
if (is_single()) : 

   // Get set column width from functions.php
   global $tw_column_width;

  /**
   * Get post options
   */
  $subheadline  = get_post_meta($post->ID, 'ag_subheadline', true); // Subheadline
  $columns      = get_post_meta($post->ID, 'ag_fullwidth', true) == 'Full' ? 'sixteen' : $tw_column_width; // number of columns
  $postclass 	  = ($columns == 'sixteen') ? 'full-width-post' : 'three-fourths-post';
  ?>

  <!-- Post Classes -->
  <div <?php post_class($postclass); ?>>

    <!-- Post Title -->
    <div class="pagetitle">
      <div class="container">

        <!-- Title -->
          <div class="thirteen columns">
             <h1 class="title"><?php the_title(); ?></h1>
              <?php if ($subheadline && $subheadline != '') { ?>
                 <h2 class="subtitle">
                    <?php echo $subheadline; ?> 
                 </h2>
              <?php } ?>
          </div>
          <!-- End Title -->

          <!-- Controls -->
          <div class="three columns">
              <?php get_template_part('functions/templates/postcontrols'); ?>
          </div>
          <!-- End Controls -->

      </div>
    </div>
    <!-- End Post Title -->

      <!-- Post Container -->
      <div class="container">
         <div class="<?php echo $columns; ?> columns">
               <div class="intro-content"><?php the_field('intro_content'); ?></div>
               <div class="portfolio-media"><?php the_field('images_or_video'); ?></div>
              <!-- Content -->
              <div class="singlecontent">
                  <!--<?php the_content(); ?>-->
                  <div class="tw-column tw-one-third tw-column-first">
                    <p><strong>Client</strong><br/><?php the_field('client'); ?><br/>
                    <strong>Date</strong><br/><?php the_field('date'); ?><br/>
                    <a target="_blank" href="<?php the_field('project_link'); ?>">View the Project</a></p>
                    <?php $terms = get_the_terms( $post->ID , 'filter' ); 
                                        foreach ( $terms as $term ) {
                                            $term_link = get_term_link( $term, 'filter' );
                                            if( is_wp_error( $term_link ) )
                                            continue;
                                        echo '<a href="/' . $term->name . '">' . $term->name . '</a>';
                                        } 
                                    ?>
                  </div>
                  <div class="tw-column tw-two-third tw-column-last ">
                    <?php the_field('summary'); ?>
                  </div>
              </div> <div class="clear"></div>
              <!-- End Content --> 
              
          </div>  
      </div>
      <!-- End Post Container -->

</div>
<!-- End Post Classes -->

<?php 
else :
  // Otherwise display thumbnail  
  get_template_part('functions/templates/thumbnail-portfolio'); 
endif; ?>