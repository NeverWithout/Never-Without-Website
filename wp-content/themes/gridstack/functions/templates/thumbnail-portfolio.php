<?php 
/**
 * This template file displays the thumbnail of a post on the homepage.
 */

// Get post
global $post;


/**
 * Get options for thumbnail
 */
$hasimages      = (has_post_thumbnail()) ? 'hasimages' : 'noimages'; // Image class for sizing
$featured_size  = (get_post_meta(get_the_ID(), 'ag_featured_size', true)) ? strtolower(preg_replace('/\s+/', '-', get_post_meta(get_the_ID(), 'ag_featured_size', true))) : 'slim'; // Featured Size of image 
$terms          = get_the_terms( get_the_ID(), 'filter' ); // Filter terms
$autoplay       = (of_get_option('of_portfolio_autoplay') == 'true') ? 'autoplay' : 'noautoplay'; // Autoplay option


/**
 * Add additional post classes
 * @var array
 */
$postclasses = array(
    $featured_size,
    'isobrick',
    $hasimages
);


/**
 * Add terms to post classes for filtering
 */
if ($terms) : 
    foreach ($terms as $term) : 
        array_push($postclasses, strtolower(preg_replace('/\s+/', '-', $term->slug)));
    endforeach; 
endif;  

?>

<!-- The Post -->
<div <?php post_class($postclasses); ?>>
  <div class="postphoto info <?php echo $autoplay; ?> random customspeed" data-speed="1000">
    <?php echo themewich_thumbnail_post_slideshow($featured_size, $post->ID, 3); // Defined in functions.php ?>
  </div>
</div>
<!-- End The Post -->