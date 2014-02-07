<?php
global $post;
$back_url = false;

/* Get Portfolio Page ID and URL
================================================== */ 
switch($post->post_type) {
    case "portfolio":

    $portfolio_pages = false;

    $page_templates = array(
            'template-portfolio.php',
            'template-portfolio-fixed.php'
        );

    $counter = 0;
    while (!$portfolio_pages && $counter < 20) {
        $portfolio_pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => $page_templates[$counter]
        ));
        $counter++;
    }
        
    if ($portfolio_pages) {
        foreach($portfolio_pages as $page){
            $portfolio_page_id = $page->ID;
            $back_url = get_permalink($portfolio_page_id);
        }
    }
        
    break;

    default:
        $back_url = get_permalink( get_option('page_for_posts' ) ); 
    break;
}    

$post_object = get_post_type_object( $post->post_type );

?>
<div class="controlswrap">
    <div class="controls">
        <?php if ($back_url) : ?>
            <span class="closepost">
                <a class="xout" href="<?php echo $back_url; ?>"><?php _e('Back to', 'framework'); ?> <?php echo $post_object->labels->name; ?></a>
            </span>
        <?php endif; ?>
        <span class="nextpost">
            <?php next_post_link('%link', __('Next Post', 'framework')); ?>
        </span>
        <span class="prevpost">
            <?php previous_post_link('%link', __('Previous Post', 'framework')); ?> 
        </span> 
        <div class="clear"></div>
        <p class="hoverhelper"></p>
    </div>
    <div class="clear"></div>
</div>