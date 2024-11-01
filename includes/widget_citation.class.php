<?php

# # Class to create the widget. The WidgetCitation class 
# extends the functionality of the Wordpress base class 
# for creating widgets called WP_Widget


class WidgetCitation extends WP_Widget{

	# In the constructor method of our class we pass the arguments 'name' 
	# and 'description' to the constructode of the parent class. 

    public function __construct(){
    	
		$widget_arg = array('description' => __('Use this widget to display a randon famous citation','widget-citation')
		);

		parent::__construct( false , $name =__('Widget Citation','widget-citation') ,$widget_arg );
    
    }

  	# Method that displays the content of the Widget in the frontend. This is what the user will see. 

    function widget( $args, $instance ){
		
		extract($args);

		# Title of our widget
		$title = apply_filters('widget_title', empty( $instance['title']) ? __('Widget Citation','widget-citation') : $instance['title']);


		# Here we assign to the variable $msg the message data that will be displayed using our wdctt_random_citation() function.
		$msg = wdctt_random_citation();

		# widget encapsulation html
		echo $before_widget;

		# title encapsulation html
		echo $before_title . $title . $after_title;

		# html-code of our widget including the message.
		echo "<div class='wdctt_widget'>";
		echo "<div class='msg'>";
		echo "<span class='dashicons dashicons-editor-quote wdctt_quote'></span>";
		echo "<p>";
		echo $msg['msg'];
		echo "</p>";
		echo "<span style='display: block;text-align: right;margin-top: 10px;font-weight: bold;'>";
		echo $msg['author'];
		echo "</span></div>";

		echo "</div>";
	
		# closing html of the widget encapsulation
		echo $after_widget;
	
	}
	
  	# Method to save the data of the widget instance. In the case of our widget, the title.

    function update( $new_instance, $old_instance ){

		$instance = $old_instance;
		$instance['title'] = stripslashes( $new_instance['title'] );

		return $instance;
	}


  	# widget configuration form. It is displayed in the Wordpress control panel under Appearance > Widgets.

    function form( $instance ){

		$instance = wp_parse_args( (array) $instance, array('title'=>__('Widget Citation','widget-citation')) );

		$title = htmlspecialchars( $instance['title'] );

		echo '<p>';
		echo '<label for="' . $this->get_field_id('title') . '">';
		echo __('Title:','widget-citation');
		echo '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" />';

		echo '</p>';
		
	}

} 

?>