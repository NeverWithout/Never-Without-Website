<?php 
global $tw_options;
global $post; ?>

<!-- Page Title -->
<div class="pagetitle <?php echo $tw_options['titlecolor']; ?> <?php echo ($tw_options['titleimage'] || $tw_options['titlebg']) ? 'background-not-transparent' : 'background-is-transparent'; ?>" <?php echo $tw_options['pagebgstyle']; ?>>

  <?php if ($tw_options['titlebg'] && $tw_options['titlebg'] != '') : ?>
    <div class='titleoverlay' style="<?php echo ($tw_options['titlebg'] && $tw_options['titlebg'] != '') ? 'background:' . $tw_options['titlebg'] . ';' : '' ?> <?php echo ($tw_options['titlebg'] && $tw_options['titlebg'] != '') ? 'opacity:'.$tw_options['opacity'] . ';' : '' ?>"></div>
    <?php echo $tw_options['o_helper'];  //Opacity helper ?>
  <?php endif; ?>

  <div class="container">
    <div class="twelve columns">
      <!-- Title -->
      <?php if(!is_page('contact')){ ?>
        <h<?php echo $tw_options['heading']; ?> class="title">
          <?php echo (is_author()) ? __('Posts By', 'framework') . ' ' : ''; 

          if (is_search()) {
            echo __("Search Results For:", 'framework') .'<br /><span class="highlight">"' . get_search_query() . '"</span>';
          } else {
            wp_title('', true); 
          } ?>
        </h<?php echo $tw_options['heading']; ?>>
        <!-- End Title -->

        <?php if (is_category() && category_description() && (category_description() != '')) : ?> 
          <h<?php echo $tw_options['subheading']; ?> class="subtitle"><?php echo strip_tags( apply_filters('the_content', category_description()) , '<a>, <br>' ); ?></h<?php echo $tw_options['subheading']; ?>>
        <?php endif; ?>

        <?php if (is_tag() && tag_description() && (tag_description() != '')) : ?> 
          <h<?php echo $tw_options['subheading']; ?> class="subtitle"><?php echo strip_tags( apply_filters('the_content', tag_description()) , '<a>, <br>' ); ?></h<?php echo $tw_options['subheading']; ?>>
        <?php endif; ?>

        <?php if (is_tax() && term_description() && (term_description() != '')) : ?> 
          <h<?php echo $tw_options['subheading']; ?> class="subtitle"><?php echo strip_tags( apply_filters('the_content', term_description()) , '<a>, <br>' ); ?></h<?php echo $tw_options['subheading']; ?>>
        <?php endif; ?>
      <?php } ?>
    </div>
  </div>
</div>