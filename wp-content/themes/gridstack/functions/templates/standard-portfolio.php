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
               <div class="portfolio-media"><?php the_field('images_or_video'); ?></div>
              <!-- Content -->
              <div class="singlecontent">
                  <!--<?php the_content(); ?>-->                  
                  <div class="tw-column tw-two-third tw-column-first ">
                    <small>Abstract</small>
                    <?php the_field('summary'); ?>
                  </div>
                  <div class="tw-column tw-one-sixth tw-column-last share">
                    
                    <small>Tags</small>                    
                    <?php $terms = get_the_terms( $post->ID , 'filter' ); 
                                        foreach ( $terms as $term ) {
                                            $term_link = get_term_link( $term, 'filter' );
                                            if( is_wp_error( $term_link ) )
                                            continue;
                                        echo '<p>' . $term->name . '</p>';
                                        } 
                                    ?>
                  </div>
                  <div class="tw-column tw-one-sixth tw-column-last share">
                    <small>Share</small>
                    <div id='fb-root'></div>
                    <script src='http://connect.facebook.net/en_US/all.js'></script>
                   
                    <p><a onclick='postToFeed(); return false;'>Facebook</a></br>
                       <a class="twitter popup" href="http://twitter.com/share?text=Check%20out%20this%20case%20study%20by%20@neverwithout%20<?php the_permalink(); ?>">Tweet</a>
                    </p>
                    
                    <p id='msg'></p>
                    <script>
                      FB.init({appId: "1410820855844255", status: true, cookie: true});

                      function postToFeed() {

                        var obj = {
                          method: 'feed',
                          redirect_uri: '<?php the_permalink(); ?>',
                          link: 'https://developers.facebook.com/docs/reference/dialogs/',
                          picture: '<?php the_field('facebook_thumbnail'); ?>',
                          name: '<?php the_title(); ?>',
                          caption: 'A Case Study from Never Without',
                          description: '<?php the_field('excerpt'); ?>'
                        };

                        function callback(response) {
                          document.getElementById('msg').innerHTML = "Post ID: " + response['post_id'];
                        }

                        FB.ui(obj, callback);
                      }
                      jQuery('.popup').click(function(event) {
                        var width  = 575,
                            height = 400,
                            left   = (jQuery(window).width()  - width)  / 2,
                            top    = (jQuery(window).height() - height) / 2,
                            url    = this.href,
                            opts   = 'status=1' +
                                     ',width='  + width  +
                                     ',height=' + height +
                                     ',top='    + top    +
                                     ',left='   + left;

                        window.open(url, 'twitter', opts);

                        return false;
                      });
                    </script>
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