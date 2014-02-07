<?php 
/**
 * This file displays the blog category
 */
get_header(); 

// Get blog style
$blogstyle 	= of_get_option('of_blog_style') ? of_get_option('of_blog_style') : 'threecol'; // Autplay Option
$pageID 	= get_option('page_for_posts');

/**
 * Get variables for page options
 */
$tw_options = themewich_page_title_options($pageID);

/**
 * Get Page Title 
 */
get_template_part('functions/templates/regular-title'); ?>

<div id="postcontainer">
    <div class="container">
        <?php get_template_part('functions/templates/'.$blogstyle); ?>
    </div>
    <div class="clear"></div>
</div>

<?php 
/* Get Footer
================================================== */
get_footer(); ?>