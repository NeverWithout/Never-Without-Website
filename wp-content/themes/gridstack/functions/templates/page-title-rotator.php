<?php 
/**
 * This file provides the html structure for the page title rotator
 */

global $post;
global $tw_options;
global $add_slider;

// Add slider jQuery
$add_slider = true;

/**
 * Get the page ID
 */
if (is_home() || is_archive() || is_404() || is_search()) :
  $pageID = get_option('page_for_posts');
else :
  $pageID = ($post) ? $post->ID : get_option('page_for_posts');
endif;


/**
 * Get variables for page options
 */
$tw_options = themewich_page_title_options($pageID);
?>

    <?php 
    /**
     * If there's an intro title or subtitle set and only on pages
     */
    if (isset($tw_options['intro']) && !themewich_array_empty($tw_options['intro'])) : ?>

    <!-- Page Title -->
    <div class="pagetitle <?php echo $tw_options['titlecolor']; ?> <?php echo ($tw_options['titleimage'] || $tw_options['titlebg']) ? 'background-not-transparent' : 'background-is-transparent'; ?>" <?php echo $tw_options['pagebgstyle']; ?>>

      <?php if ($tw_options['titlebg'] && $tw_options['titlebg'] != '') : ?>
        <div class='titleoverlay' style="<?php echo ($tw_options['titlebg'] && $tw_options['titlebg'] != '') ? 'background:' . $tw_options['titlebg'] . ';' : '' ?> <?php echo ($tw_options['titlebg'] && $tw_options['titlebg'] != '') ? 'opacity:'.$tw_options['opacity'] . ';' : '' ?>"></div>
        <?php echo $tw_options['o_helper'];  //Opacity helper ?>
      <?php endif; ?>

      <div class="container">

      <!-- Title  -->
        <div class="eleven columns info titlerotator pager <?php echo $tw_options['autoplay']; ?> <?php echo $tw_options['adapt']; ?>" data-pause="<?php echo $tw_options['pause']?>000" data-speed="1500">

        <?php echo ($tw_options['plural']) ? '<ul class="bxslider">' : ''; ?> 

            <?php 
            $i = 1;
            foreach($tw_options['intro'] as $index => $row) : ?>

              <?php echo ($tw_options['plural']) ? '<li>' : ''; ?>

              <?php if ($i > 1) {
                $tw_options['heading']    = '2';
                $tw_options['subheading'] = '3';
              } ?>

                <?php if (isset($tw_options['intro'][$index][0]) && $tw_options['intro'][$index][0] != '') : ?>
                  <!-- Title -->
                  <h<?php echo $tw_options['heading']; ?> class="title"><?php echo strip_tags( apply_filters('the_content', $tw_options['intro'][$index][0]) , '<a>, <br>' ); ?></h<?php echo $tw_options['heading']; ?>> 
                  <!-- End Title -->
                <?php endif; ?>

                <?php if (isset($tw_options['intro'][$index][1]) && $tw_options['intro'][$index][1] != '') : ?>
                  <!-- Subtitle -->
                  <h<?php echo $tw_options['subheading']; ?> class="subtitle"><?php echo strip_tags( apply_filters('the_content', $tw_options['intro'][$index][1]) , '<a>, <br>' ); ?></h<?php echo $tw_options['subheading']; ?>>
                  <!-- End Subtitle -->
                <?php endif; ?>
       
                <?php if (isset($tw_options['intro'][$index][3]) && $tw_options['intro'][$index][3] != '') : ?>
                  <!-- Button -->
                  <a href="<?php echo strip_tags( apply_filters('the_content', $tw_options['intro'][$index][3]) , '<a>' ); ?>" class="button">
                    <?php echo strip_tags( apply_filters('the_content', $tw_options['intro'][$index][2]) , '<a>' ); ?>
                  </a>
                  <!-- End Button -->
                <?php endif; ?>
            
              <?php echo ($tw_options['plural']) ? '</li>' : ''; ?>
              <?php $i++; ?>
            <?php endforeach; ?>

        <?php echo ($tw_options['plural']) ? '</ul>' : ''; ?>

        </div>
      <!-- End Title -->

      </div>
    </div>

    <?php 
    /**
     * Otherwise, just use regular title
     */
    else :

    get_template_part('functions/templates/regular-title'); 

    endif; ?>

