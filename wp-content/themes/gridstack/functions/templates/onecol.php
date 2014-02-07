<div class="ten columns">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>               
<div <?php post_class(array('articleinner', 'onecol')); ?>>
        <!-- Post Image
        ================================================== -->
        <?php /* if the post has a WP 2.9+ Thumbnail */
            if ( (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) : ?>
                <div class="thumbnailarea">
                    <a class="thumblink" title="<?php printf(__('Permanent Link to %s', 'framework'), get_the_title()); ?>" href="<?php the_permalink(); ?>">
                    <?php  the_post_thumbnail('blogonecol', array('class' => 'scale-with-grid')); /* post thumbnail settings configured in functions.php */ ?>
                      
                    </a>
                </div>
            <?php endif; 
        ?>
        <div class="datesection">
            <div class="fulldate">
                <span class="day">
                    <?php the_time('d'); ?>
                </span>
                <span class="monthyear">
                    <?php the_time('M'); ?> '<?php the_time('y'); ?>
                </span>
                <div class="clear"></div>
            </div>
            <p class="authorlink"><?php the_author_posts_link(); ?> <p>
            <a href="<?php comments_link(); ?>" title="<?php _e('Comments', 'framework'); ?>"><?php comments_number(__('No Comments', 'framework'), __('1 Comment', 'framework'), __('% Comments', 'framework')); ?> </a>
        </div>

        <div class="indexcontent">

            <div class="categories">
                <?php echo ag_get_cats(3); ?>
                <div class="clear"></div>
            </div>

            <h2 class="indextitle">
                <a href="<?php the_permalink(); ?>" title="<?php printf(__('Permanent Link to %s', 'framework'), get_the_title()); ?>">
                    <?php the_title(); ?>
                </a>
            </h2>

            <!-- Post Content
            ================================================== -->
            <?php 
			global $more; $more = 0;
            the_excerpt();
			?>

        </div>

         <div class="clear"></div>
</div> <!-- End full_col -->
    <?php endwhile; else : ?>
	<?php if (is_search()) {?>
    <div class="one_col isobrick">
			<h4><?php _e('Nothing Found.', 'framework'); ?> <br /><?php _e('Try Another Search:', 'framework'); ?></h4>
        	<p><?php get_search_form(true); ?></p>
    </div>
     <?php }?>       
	<?php endif; wp_reset_query(); ?>

    <div class="clear"></div>

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

</div>


<div class="five columns sidebar offset-by-one">
    <?php   /* Widget Area */   if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Blog Sidebar') ) ?>
</div>
