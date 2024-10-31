<?php
/**
 * Plugin Name: QuickWeather
 * Description: A simple and quick weather widget. Very basic, displays the conditions and temp of the specified city.
 * Version: 1.0
 * Author: Robert Thompson
 * Author URI: http://robgt.us
 */

/**
 * Add function to widgets_init that'll load our widget.
 */
 
add_action( 'widgets_init', 'QuickWeather_load_widgets' );

function QuickWeather_load_widgets() {
	register_widget( 'QuickWeather_widget' );
}

 
class QuickWeather_widget extends WP_Widget {
/**
	 * Widget setup.
	 */
	function QuickWeather_widget() {
		/* Widget settings. */
		$widget_options = array( 
		 'classname' => 'QuickWeather_widget', 
		 'description' => __('A Simple widget that displays weather.') );

		/* Widget control settings. */
		$control_options = array( 
		'width' => 300, 
		'height' => 350, 
		'id_base' => 'quickweather' );

		/* Create the widget. */
		$this->WP_Widget( 'quickweather', 'QuickWeather Widget', $widget_options, $control_options );
	
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$city =($instance['city'] != "")?$instance['city'] :New York;
 
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		
        $url = 'http://api.openweathermap.org/data/2.5/weather?q='.$city.'&mode=xml&units=imperial'; 
           
		$xml = (string)file_get_contents($url); 
		if ($xml && !empty($xml))
		{
			$xml = simplexml_load_string($xml); 
			if ($xml && is_object($xml)){
					$condition = $xml->clouds['name']; 
					$precipitation = $xml->precipitation;
					$city = $xml->city['name'];				 					
			    	        $temperature = round($xml->temperature['value']); 
          
			        $markup='<div style="display:block;font-weight:bold;">'. 
			                        $city."\n".
					        $condition. " ". 
					        $temperature . "&deg; F"."\n".			
					         '</div>';
					echo nl2br($markup);
				}
		}				
		/* After widget (defined by themes). */
		echo $after_widget;
	}
 

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for name to remove HTML (important for text inputs). */
		$instance['city'] = strip_tags( $new_instance['city'] );
 		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 
		'title' => __('weather', 'weather'), 
		'city' => __('New York', 'New York')
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- City Input -->
		<p>
		<label for="<?php echo $this->get_field_id( 'city' ); ?>"><?php _e('City:', 'city'); ?></label>
		<input id="<?php echo $this->get_field_id( 'city' ); ?>" name="<?php echo $this->get_field_name( 'city' ); ?>" value="<?php echo $instance['city']; ?>" style="width:100%;" />
		</p>
	<?php
	}
}
?>