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
   * @var [type]
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

        <?php get_template_part('functions/templates/postcontrols'); ?>

        <!-- Title -->
          <div class="thirteen columns">
            <span class="date">
              <i class="icon-bookmark-empty"></i><?php the_time(get_option('date_format')); ?>
            </span>
             <h1 class="title"><?php the_title(); ?></h1>
              <?php if ($subheadline && $subheadline != '') { ?>
                 <h2 class="subtitle">
                    <?php echo $subheadline; ?> 
                 </h2>
              <?php } ?>
          </div>
          <!-- End Title -->

      </div>
    </div>
    <!-- End Post Title -->

      <!-- Post Container -->
      <div class="container">
         <div class="<?php echo $columns; ?> columns">

              <!-- Content -->
              <div class="singlecontent">
                  <?php the_content(); ?>
              </div> <div class="clear"></div>
              <!-- End Content --> 
              
              <?php 
			  // Link Pages
              wp_link_pages(); ?> 
          </div>  
      </div>
      <!-- End Post Container -->

      <!-- Comments Container -->
      <div class="commentwrapper">
        <div class="container">

          <?php 
          /**
           * If Comments are open
           */
          if ('open' == $post->comment_status) : ?>

            <!-- Comments -->
            <div class="eleven columns">
              <?php comments_template('', true); ?>
            </div>
            <!-- End Comments -->

            <!-- Post Details -->
            <div class="four columns offset-by-one">
                <span class="details">

                    <?php 
                    // Get author information
                    $author_id    = get_the_author_meta('ID');
                    $author_name  = get_the_author_meta('display_name'); 
                    $author_url   = get_the_author_meta('user_url'); 
                    ?>

                    <div class="detailgroup author">
                      <div class="avatar">
                        <?php echo get_avatar($author_id, 64); ?>
                      </div>
                      <p class="detailtitle">
                        <?php _e('Posted By', 'framework'); ?>
                      </p>
                      <p><?php the_author_posts_link(); ?></p>
                    </div>

                    <div class="detailgroup cats">
                      <p class="detailtitle">
                        <?php _e('Categories', 'framework'); ?>
                      </p>
                      <p><?php echo ag_get_cats(10, 'list'); ?></p>
                    </div>

                    <div class="detailgroup tags">
                      <?php the_tags('<p class="detailtitle">'.__("Tags", "framework"). '</p><p>', ', ', '</p>'); ?>                    
                    </div>

                </span>
            </div>
            <!-- End Post Details -->

          <?php 
          /**
           * If comments are closed
           */
          else : ?>

          <!-- Post Author -->
          <div class="one-third">
            <span class="details">

              <?php 
              // Get author information
              $author_id    = get_the_author_meta('ID');
              $author_name  = get_the_author_meta('display_name'); 
              $author_url   = get_the_author_meta('user_url'); 
              ?>

              <div class="detailgroup author">
                <div class="avatar left">
                  <?php echo get_avatar($author_id, 64); ?>
                </div>
                <p class="detailtitle">
                  <?php _e('Posted By', 'framework'); ?>
                </p>
                <p><?php the_author_posts_link(); ?></p>
              </div>

            </span>
          </div>
          <!-- End Post Author -->

          <!-- Post Categories -->
          <div class="one-third">
            <span class="details">
              <div class="detailgroup cats">
                <p class="detailtitle"><?php _e('Categories', 'framework'); ?></p>
                <p><?php echo ag_get_cats(10, 'list'); ?></p>
              </div>
            </span>
          </div>
          <!-- End Post Categories -->

          <!-- Post Tags -->
          <div class="one-third column-last">
            <span class="details">
              <div class="detailgroup tags">
                <?php the_tags('<p class="detailtitle">'.__("Tags", "framework"). '</p><p>', ', ', '</p>'); ?>                    
              </div>
            </span>
          </div>
          <!-- End Post Tags -->

          <?php endif; ?>

        </div>
      </div>
      <!-- End Comments Container -->

  </div>
  <!-- End Post Classes -->

<?php 
else :
  // Otherwise display thumbnail  
  get_template_part('functions/templates/thumbnail'); 
endif; ?>