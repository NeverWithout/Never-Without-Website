<div class="clear"></div>

<!-- Footer -->
<div id="footer" class="dark">
    <div class="container clearfix">
        <div class="sixteen columns">
                <div class="tw-one-fourth tw-column tw-column-first" id="logo">
                    <?php echo is_front_page() ? '<h1>' : '<h2>'; ?>
                        <a href="<?php echo home_url(); ?>">
                            <?php if ( $logo = of_get_option('of_logo') ) { ?>
                            <img src="<?php echo $logo; ?>" alt="<?php bloginfo( 'name' ); ?>" />
                            <?php } else { bloginfo( 'name' );} ?>
                            </a> 
                     <?php echo is_front_page() ? '</h1>' : '</h2>'; ?> 
                     <img src="<?php bloginfo('template_directory'); ?>/images/vertLine.gif" class="vertLine"/>
                </div>
                <?php	/* Widget Area */ if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Footer Column 1') ) ?>
                <?php	/* Widget Area */ if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Footer Column 2') ) ?>
                <?php	/* Widget Area */ if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Footer Column 3') ) ?>
            
            <div class="clear"></div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<div id="footer" class="light">
    <div class="container clearfix">
        <div class="sixteen columns">
            <div class="tw-one-half tw-column tw-column-first">
                <p>All rights reserved. ©<?php echo date("Y") ?> <?php bloginfo('name'); ?></p>
            </div>
            <div class="tw-one-half tw-column tw-column-last">
                <?php wp_nav_menu( array('menu' => 'Footer Nav' )); ?>
            </div>
        </div>
    </div>
</div>
<!-- End Footer -->

<!-- Theme Hook -->
<?php wp_footer(); ?>

<!-- End Site Container -->
</div> 
</body>
</html>