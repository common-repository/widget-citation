<?php

# We've added a little code that will prevent
# our functions file from being accessed
# directly in the browser. If this happens
# the user will receive a message denying access 
# access and the code will be terminated.

if( !defined("WPINC") ) { 
	_e('Access denied...', 'widget-citation'); 
	exit;
}


# Here we have our plugin activation function. 
# It will be associated with the hook 'plugin_activate' 
# to be executed on activation of the plugin. 
# That is, if your plugin needs to do anything before 
# do anything before it fucntions for the first time, this is the time.

function wdctt_activate_plugin() {

	# whenever we need to manipulate the database 
	# through Wordpress inside a function we'll need to 
	# declare the global variable $wpdb.
	global $wpdb;

	# Our array of quotes will now be added to the 
	# database on activation of the plugin.
	include( WDCTT_PATH . '\includes\data.php');


	# we need to create our table to store our messages. 
	# We'll put its structure in a string.

	$table_query =  "CREATE TABLE `". $wpdb->prefix . WDCTT_TABLE . "` (
			`id` int(11) NOT NULL auto_increment,
			`author` varchar(50) NOT NULL,
			`msg` varchar(255) NOT NULL,
			`status` int(11) NOT NULL DEFAULT 1,
			PRIMARY KEY  (`id`))"; 
	
	$wpdb->query( $table_query );

	# we check if there was an error in the creation of the table.
	$db_error = $wpdb->last_error;

	if( !$db_error ) {

		# there was no error, so we proceeded with entering the data.
		foreach ( $citation as $key => $value) {

			$wpdb->insert( $wpdb->prefix . WDCTT_TABLE, $value );
		}
		
	} else { 

		# an error occurred and the data was not installed. 
		# In the future we will look at error handling.
		echo __('An error ocurred: ', 'widget-citation') . $db_error; 

	}

}


function wdctt_deactivate_plugin(){

	global $wpdb;

	# We remove the widget data from the options table. 
	# Wordpress will create a record in the options table 
	# following the widget_{widget_name} pattern. 
	# In our case this will be widget_widgetcitation.
	delete_option('widget_widgetcitation');

	# we delete the table from the database.
	$wpdb->query( "DROP TABLE IF EXISTS `" . $wpdb->prefix . WDCTT_TABLE . "`" );

}

# At widget initialization we will register our
# widget so that it is available to the user.
# To do this we use the function 'register_widget' which will be
# called by a generic function in the 'widgets_init' hook.
# That is, when the 'widgets_init' hook is executed, it will 
# execute the generic function that will register our widget.


add_action( 'widgets_init', function(){

	# effectively registering our widget through our
	# WidgetCitation class.
	register_widget('WidgetCitation');

});


# Enqueue stylesheet functions.

function wdctt_enqueue_style() {  
		
	$style_sheet = plugins_url( 'css/wdctt_style.css', dirname(__FILE__) );
	wp_enqueue_style( "wc-style", $style_sheet);
}

function wdctt_admin_enqueue_style(){

	$style_sheet = plugins_url( 'css/wdctt_adm_style.css', dirname(__FILE__) );
	wp_enqueue_style( 'wc-admin-style', $style_sheet, array(),'1.0' );
}


# We use the wordpress add_menu_page() function to create our menu. 
# We put it inside a function that will be triggered by the hook 
# 'admin_menu' as parameters, we supply the names that will be displayed 
# in the menu and on the page, the permission level that the user 
# needs to have to access the menu, a nickname for the menu, 
# the name of the function that will be called to render the page 
# and the icon that will be used.

function wdctt_create_menu() {

	add_menu_page( __('Widget Citation Page','widget-citation'), __('Citations','widget-citation'), 'manage_options', 'widget-citation', 'wdctt_adm_page', 'dashicons-format-quote' );

}

add_action( 'admin_menu', 'wdctt_create_menu' ); 


# function that will be called on add_menu_page() to 
# render the page. In our case it will just make an
# include the file with our HTML code.

function wdctt_adm_page(){

	include( WDCTT_PATH . "includes/wdctt_adm_page.php");
}

# Our function for inserting the data into a table. 

function wdctt_new_message() {

	global $wpdb;

	#recuperamos os dados que foram enviados via POST.
	$author = filter_input(INPUT_POST, 'wdctt_author');
	$message = filter_input(INPUT_POST, 'wdctt_message');

	# fazemos algumas verificações nos dados 
	if( $author != NULL OR  $message != NULL) {

		if( !empty( trim($author) ) ) {

			$response['author'] = $author;

			if ( !empty( trim($message) ) ) {

				$response['message'] = $message;

				$data = array('author' => $author , 'msg' => $message);

				$wpdb->insert( $wpdb->prefix . WDCTT_TABLE, $data );

				$db_error = $wpdb->last_error;

				if( !$db_error ) {

					echo "<span class='wm-info'> " . __('Your message has been included','widget-citation') . "</span>";

				} else {

					print_r($db_error);
				}

			} else {

				$response['message'] = '';

				# The form has been sent, but the message field is empty.
				echo "<span class='wm-info'>" . __("The message's field can't be empty",'widget-citation'). "</span>";
			}

		} else {

			$response['author'] = '';

			# The form has been submitted, but the author field is empty.
			echo "<span class='wm-info'>" . __("The author's field can't be empty",'widget-citation'). "</span>";
		}

		return $response;

	} else {

		# The form was not sent. Then we do nothing. 
		return array('author' => '' , 'message' => '');
	}
}


function wdctt_random_citation() {

	# declare $wpdb.
	global $wpdb;

	# we set up a query that will randomly fetch a record from the table.
	$query = "SELECT * FROM " . $wpdb->prefix . WDCTT_TABLE . " WHERE status = 1 ORDER BY rand() LIMIT 1";

	# we use the get_results() method to retrieve the information we want.
	$messages = $wpdb->get_results( $query, ARRAY_A);

	if( $messages ){ 

		return $messages[0];

	} else {

		return array('author' => 'adm' , 'msg' => false );
	}

} 


function wdctt_list_citation() {

	global $wpdb;

	$limit = 10;

	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

	$start = ( $pagenum - 1 ) * $limit;

	$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM " . $wpdb->prefix . WDCTT_TABLE );

	$pages = ceil( $total / $limit );

	$query = "SELECT * FROM " . $wpdb->prefix . WDCTT_TABLE . " ORDER BY 'author' LIMIT $start, $limit";

	$messages = $wpdb->get_results( $query, ARRAY_A);

	if( $messages ){ 

		$return['m'] =  $messages;

		$page_links = paginate_links( array(
		    'base' => add_query_arg( 'pagenum', '%#%' ),
		    'format' => 'wdctt_nav_links',
		    'prev_text' => __( '&laquo;', 'widget-citation' ),
		    'next_text' => __( '&raquo;', 'widget-citation' ),
		    'total' => $pages,
		    'current' => $pagenum
		));

		if ( $page_links ) {
		    $return['p'] = '<div class="wdctt_nav"><div class="wdctt_nav_pages" style="margin: 1em 0">' . $page_links . '</div></div>';
		}

	} else {

		return false;
	}

	return $return;

}

function wdctt_delete_item( $id = false ) {

	global $wpdb;

	if( $id ) {

		$wpdb->delete( $wpdb->prefix . WDCTT_TABLE, array( 'id'=>$id ) );

	}

}

function wdctt_change_status( $id = false, $stat = false ) {

	global $wpdb;	

	$wpdb->update( 
		$wpdb->prefix . WDCTT_TABLE, 
		array( 'status'=>$stat ),
		array( 'id'=>$id )
	);

}