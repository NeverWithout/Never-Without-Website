<?php

/*******************************************************
*
*	Custom Posts Tab Widget
*	By: Andre Gagnon
*	http://www.themewich.com
*
*******************************************************/

// Initialize widget
add_action( 'widgets_init', 'ag_tab_widgets' );

// Register widget
function ag_tab_widgets() {
	register_widget( 'AG_Tab_Widget' );
}

// Widget class
class ag_tab_widget extends WP_Widget {

/*----------------------------------------------------------*/
/*	Set up the Widget
/*----------------------------------------------------------*/
	
	function AG_Tab_Widget() {
	
		/* General widget settings */
		$widget_ops = array( 'classname' => 'ag_tab_widget', 'description' => __('A widget that displays popular posts, facebook like box.', 'framework') );

		/* Widget control settings */
		$control_ops = array( 'width' => 400, 'height' => 350, 'id_base' => 'ag_tab_widget' );

		/* Create widget */
		$this->WP_Widget( 'ag_tab_widget', __('Custom Posts Tab Widget', 'framework'), $widget_ops, $control_ops );
	}

/*----------------------------------------------------------*/
/*	Display The Widget 
/*----------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );
		
		// Add tabs script
		global $add_tabs;
		$add_tabs = true;
		
		$title = apply_filters('widget_title', $instance['title'] );

		/* Variables from settings. */
		$numposts = $instance['numposts'] ;

		/* Before widget (defined in functions.php). */
		echo $before_widget;

		/* Display The Widget */
		?>

 <?php 
/* Display the widget title & subtitle if one was input (before and after defined by themes). */
if ( $title ) echo '<h3 class="widget-title">'.$title.'</h3>' ?>

<?php $twocol = 'twocol';  ?>
		
<div class="tabswrap">

<ul class="tabs <?php echo $twocol; ?>">
	<li><a class="active" href="#tab1"><?php _e('Popular', 'framework')?></a></li>
	<li><a href="#tab2"><?php _e('Recent', 'framework')?></a></li>
</ul>	
<div class="clear"></div>

<ul class="tabs-content">

	<!-- First Tab 
	================================================-->
	<li id="tab1" class="active">
	
	<?php query_posts('meta_key=post_views_count&orderby=meta_value_num&order=DESC&ignore_sticky_posts=1&posts_per_page='. $numposts ); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<div class="tabpost">
        <div class="featuredimagewidget thumbnailarea">
            <?php 
            if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.?>
                <a class="thumblink" href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('tinyfeatured'); ?>
                </a>
            <?php } ?>
        </div>

		<p class="tab-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
		<p class="views"><?php echo getPostViews(get_the_ID()); ?></p>
		<div class="clear"></div>
	</div>

	<?php 
	endwhile; 
	endif; 
	wp_reset_query(); ?>

	</li>

	<!-- Second Tab 
	================================================-->
	<li id="tab2">

	<?php
		query_posts('ignore_sticky_posts=1&posts_per_page='. $numposts);
		if (have_posts()) : while (have_posts()) : the_post(); 
	?>

	<div class="tabpost">
		
        <div class="featuredimagewidget thumbnailarea">
            <?php 
            if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.?>
                <a class="thumblink" href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('tinyfeatured'); ?>
                </a>
            <?php } ?>
        </div>

		<p class="tab-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
		<p class="views"><?php the_time(get_option('date_format')); ?></p>
		<div class="clear"></div>
	</div>
	<?php endwhile; endif; wp_reset_query(); ?>
	
	</li>
</ul>
	<div class="clear"></div>
</div>


<?php
	/* After widget (defined by themes). */
		echo $after_widget;
	}

/*----------------------------------------------------------*/
/*	Update the Widget
/*----------------------------------------------------------*/
	
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		
		/* Remove HTML: */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['numposts'] = strip_tags( $new_instance['numposts'] );
		$instance['facebook'] = strip_tags( $new_instance['facebook'] );
	
		return $instance;
	}
	

/*----------------------------------------------------------*/
/*	Widget Settings
/*----------------------------------------------------------*/
	 
	function form( $instance ) {

		/* Default widget settings */
		$defaults = array(
		'title' => '',
		'numposts' => '6',
		'facebook' => '',		
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
<p>
  <label for="<?php echo $this->get_field_id( 'title' ); ?>">
        <?php _e('Tabs Title (Optional):', 'framework') ?>
    </label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'numposts' ); ?>">
        <?php _e('Number of Posts', 'framework') ?>
    </label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" value="<?php echo $instance['numposts']; ?>" />
</p>

<?php
	}
}
?>