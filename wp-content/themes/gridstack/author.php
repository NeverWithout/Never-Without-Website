<?php get_header(); ?>

<?php 
/* #Get Page Title
======================================================*/
get_template_part('functions/templates/page-title-rotator'); ?>

<div id="postcontainer">
    <div class="container">
        <?php get_template_part('functions/templates/threecol'); ?>
    </div>
    <div class="clear"></div>
</div>

<?php 
/* Get Footer
================================================== */
get_footer(); ?>