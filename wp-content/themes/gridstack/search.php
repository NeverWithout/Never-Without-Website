<?php 
/**
 * This file displays the blog index
 */
get_header(); 

// Get blog style
$blogstyle = of_get_option('of_blog_style') ? of_get_option('of_blog_style') : 'threecol'; // Autplay Option
?>

<?php get_template_part('functions/templates/page-title-rotator'); ?>

<!-- Container Wrap -->
<div id="postcontainer">
    <div class="container">
        <?php get_template_part('functions/templates/'.$blogstyle); ?>
    </div>
    <div class="clear"></div>
</div>
<!-- END Container Wrap -->

<?php 
/* Get Footer
================================================== */
get_footer(); ?>