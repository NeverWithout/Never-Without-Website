<div class="eleven columns">
    <div class="isotopecontainer twocol" data-value="2">
    <?php $i=1;
	global $add_isotope;

	// Add isotope script
	$add_isotope = true; ?>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                
    <div class="one-half isobrick">
        <?php $borderclass = ($i <=2) ? 'noborder' : 'border'; ?>
        <div <?php post_class(array($borderclass,'articleinner')); ?>>
            <div class="twocol-inner">

            <div class="categories">
                <?php echo ag_get_cats(3); ?>
                <div class="clear"></div>
            </div>
            

            <h2 class="indextitle">
                <a href="<?php the_permalink(); ?>" title="<?php printf(__('Permanent Link to %s', 'framework'), get_the_title()); ?>">
                    <?php the_title(); ?>
                </a>
            </h2>

            <span class="date">
                <?php 
                    the_time(get_option('date_format')); ?> | <?php the_author_posts_link();
                    $num_comments = get_comments_number(); // get_comments_number returns only a numeric value
                    if ( comments_open() && ($num_comments != 0) ) : ?>
                       | <a class="bubble" href="<?php comments_link(); ?>"><?php comments_number('0 Comments', '1 Comment', '% Comments'); ?></a>
                    <?php endif; 
                ?>
            </span>

                <!-- Post Image
                ================================================== -->
                <?php /* if the post has a WP 2.9+ Thumbnail */
                    if ( (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) : ?>
                        <div class="thumbnailarea">
                            <a class="thumblink" title="<?php printf(__('Permanent Link to %s', 'framework'), get_the_title()); ?>" href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('blog', array('class' => 'scale-with-grid')); /* post thumbnail settings configured in functions.php */ ?>
                            </a>
                        </div>
                    <?php endif; 
                ?>

                <!-- Post Content
                ================================================== -->
                <?php 
				global $more; $more = 0;
				if (preg_match('/<!--more/', $post->post_content)) {
					$content = apply_filters('the_content',get_the_content(__('Read More', 'framework')));
					echo $content;
				} else {
					the_excerpt(__('Read More', 'framework'));
				}
				?>

             <div class="clear"></div>
            </div>
        </div> <!-- End articleinner -->
    </div> <!-- End full_col -->
    <?php $i++; ?>
    <?php endwhile; ?>
	<div class="clear"></div>
	<?php else : ?>
    	<?php if (is_search()) {?>
            <div class="one_col isobrick">
        			<h4><?php _e('Nothing Found.', 'framework'); ?> <br /><?php _e('Try Another Search:', 'framework'); ?></h4>
                	<p><?php get_search_form(true); ?></p>
            </div>
         <?php }?>       
	<?php endif; wp_reset_query(); ?>
    <div class="clear"></div>

</div>

    <!-- Pagination
    ================================================== -->        
    <div class="pagination">
        <?php
            global $wp_query;
    
            $big = 999999999; // need an unlikely integer
    
            echo paginate_links( array(
                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format' => '?paged=%#%',
                'current' => max( 1, get_query_var('paged') ),
                'total' => $wp_query->max_num_pages
            ) );
        ?>   
        <div class="clear"></div>
    </div> 
    <!-- End pagination --> 

</div><!-- End isonormal -->

<div class="five columns sidebar">
    <?php   /* Widget Area */   if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Blog Sidebar') ) ?>
</div>
