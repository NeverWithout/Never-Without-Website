<?php 
/* 
Template Name: 404
*/
get_header(); ?> 

<div class="pagetitle light background-not-transparent" style=' background-image:url(http://10.0.1.72:8888/wp-content/uploads/2013/11/unsplash_5249ec4b15749_1-1500x1500.jpg);'>

  
  <div class="container">
    <div class="twelve columns">
      <!-- Title -->
              <h1 class="title">Never Without...</h1>
							<h2 class="subtitle">an error page</h2>
        <!-- End Title -->

        
        
                  </div>
  </div>
</div>


<div id="postcontainer">
    <div class="container">
        <div class="sixteen columns">
            <div class="singlecontent">
							<?php 
							$the_page    = null;
							$errorpageid = get_option( '404pageid', 0 ); 
 
							if ($errorpageid !== 0) {
							    // Typecast to an integer
							    $errorpageid = (int) $errorpageid;
 
							    // Get our page
							    $the_page = get_page($errorpageid);
							}
							?>
 
							<div id="four-oh-four">
							    <?php if ($the_page == NULL || isset($the_page->post_content) && trim($the_page->post_content == '')): ?>
							        <h1>There was an error and nobody defined a custom 404 page message, so you're seeing this instead.</h1>
							    <?php else: ?>
											<?php echo apply_filters( 'the_content', $the_page->post_content ); ?>
							    <?php endif; ?>
							</div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<!-- END Page Content Area -->

<?php 
/* Get Footer
================================================== */
get_footer(); ?>