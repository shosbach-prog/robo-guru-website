<?php 
/* create SFSI widget */
class Sfsi_Plus_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'classname' => 'sfsi_plus sfsi_plus_widget_main_container', 'description' => __( 'Ultimate Social Media PLUS widgets', 'ultimate-social-media-plus' ) );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sfsi-plus-widget' );

		parent::__construct(
			// Base ID of your widget
			'sfsi-plus-widget', 
	
			// Widget name will appear in UI
			__( 'Ultimate Premium', 'ultimate-social-media-plus' ),
	
			// Widget description
			$widget_ops,
			
			$control_ops
		);
	}
	
	function widget( $args, $instance ) {

		if( sfsi_plus_shall_show_icons( 'round_icon_widgetc' ) && sfsi_premium_is_any_standard_icon_selected() ) {
			$before_title = $args['before_title'];
			$after_title = $args['after_title'];
			$before_widget = $args['before_widget'];
			$after_widget = $args['after_widget'];
			$widget_name = isset($args['widget_name']) ? sanitize_text_field($args['widget_name']):'';
			$widget_id = intval($args['widget_id']);
			$description = isset($args['description']) ? sanitize_text_field($args['description']) : '';
			$name = isset($args['description']) ? sanitize_text_field($args['name']): '';

			//if show via widget is checked
			$sfsi_premium_section8_options = get_option("sfsi_premium_section8_options");
			$sfsi_premium_section8_options = maybe_unserialize($sfsi_premium_section8_options);

			$sfsi_plus_show_via_widget = $sfsi_premium_section8_options['sfsi_plus_show_via_widget'];
			
			
				/*Our variables from the widget settings. */
				$title     = isset($instance['title']) ? apply_filters('widget_title', $instance['title'] ) : '';
				$show_info = isset( $instance['show_info'] ) ? $instance['show_info'] : false;
				global $is_floter;     

				echo $before_widget;
				/* Display the widget title */
				if( "" === $before_title ) {
					$before_title = '<h2 class="widget-title">';
				}

				if( "" === $after_title ) {
					$after_title = '</h2>';
				}

				echo "<div class='sfsi_premium_widget_container' id='".$widget_id."'>";
					if ( $title ){
						echo "<div class='sfsi_premium_widget_title'>";
							echo $before_title . $title . $after_title;
						echo "</div>";
					}		
					
					echo "<div class='sfsi_plus_widget sfsi_plus_widget_sub_container' data-position='widget'>";
						echo "<div id='sfsi_plus_wDiv'></div>";

							/* Link the main icons function */
							if (wp_is_mobile())
							{
								if(isset($sfsi_premium_section8_options['sfsi_plus_widget_show_on_mobile']) && $sfsi_premium_section8_options['sfsi_plus_widget_show_on_mobile'] == 'yes')
								{
									echo sfsi_plus_check_mobile_visiblity(0,$sfsi_premium_section8_options);
								}
							} else {
								if(isset($sfsi_premium_section8_options['sfsi_plus_widget_show_on_desktop']) && $sfsi_premium_section8_options['sfsi_plus_widget_show_on_desktop'] == 'yes')
								{
									echo sfsi_plus_check_visiblity(0,$sfsi_premium_section8_options);
								}
							} 

						echo "<div style='clear: both;'></div>";
					echo "</div>";
					echo "<div style='clear: both;'></div>";
				echo "</div>";
				
				echo $after_widget;
		}
	}
	
	/*Update the widget */ 
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		//Strip tags from title and name to remove HTML
		if( $new_instance['showf'] == 0 ) {
		    $instance['showf'] = 1;
		} else {
		    $instance['showf'] = 0;
		}

		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
	
	/* Set up some default widget settings. */
	function form( $instance ) {
		$defaults = array( 'title' =>"" );
		$instance = wp_parse_args( (array) $instance, $defaults );

		if( isset( $instance['showf'] ) ) {
			if( $instance['showf'] == 0 && empty( $instance['title'] ) ) {
				$instance['title']= __( 'Please follow & like us :)', 'ultimate-social-media-plus' );
			}
		} else {
			$instance['title']= __( 'Please follow & like us :)', 'ultimate-social-media-plus' );
		}
		?>
		<p>
		    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title', 'ultimate-social-media-plus' ); ?>:
            </label>
		    <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		    <input type="hidden" value="<?php echo isset($instance['showf']) && !empty($instance['showf']) ? $instance['showf'] : ''; ?>" id="<?php echo $this->get_field_id( 'showf' ); ?>" name="<?php echo $this->get_field_name( 'showf' ); ?>" />
		</p>
		<p>
			<?php _e( 'Please go to the plugin page to set your preferences:', 'ultimate-social-media-plus' ); ?>
			<a href="admin.php?page=sfsi-plus-options"><?php _e( 'Click here', 'ultimate-social-media-plus' ); ?></a>
		</p>
	<?php
	}
}
/* END OF widget Class */

/* register widget to wordpress */
function register_sfsi_plus_widgets() 
{
	if( false != License_Manager::validate_license() ) {
		register_widget( 'sfsi_plus_widget' );
	}
}
add_action( 'widgets_init', 'register_sfsi_plus_widgets' );