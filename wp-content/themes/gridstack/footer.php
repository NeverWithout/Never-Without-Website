<div class="clear"></div>

<!-- Footer -->
<div id="footer" class="dark">
    <div class="container clearfix">
        <div class="sixteen columns">
            <div class="<?php echo $tw_column_width; ?> columns"><?php	/* Widget Area */ if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Footer') ) ?></div>
            
            <div class="clear"></div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<!-- End Footer -->

<!-- Theme Hook -->
<?php wp_footer(); ?>

<!-- End Site Container -->
</div> 
</body>
</html>