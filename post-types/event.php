<?php /**
 * Registers the `event` post type.
 */
function event_init() {
	register_post_type(
		'event',
		[
			'labels'                => [
				'name'                  => __( 'Events', 'wp-event-plugin' ),
				'singular_name'         => __( 'Event', 'wp-event-plugin' ),
				'all_items'             => __( 'All Events', 'wp-event-plugin' ),
				'archives'              => __( 'Event Archives', 'wp-event-plugin' ),
				'attributes'            => __( 'Event Attributes', 'wp-event-plugin' ),
				'insert_into_item'      => __( 'Insert into Event', 'wp-event-plugin' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Event', 'wp-event-plugin' ),
				'featured_image'        => _x( 'Featured Image', 'event', 'wp-event-plugin' ),
				'set_featured_image'    => _x( 'Set featured image', 'event', 'wp-event-plugin' ),
				'remove_featured_image' => _x( 'Remove featured image', 'event', 'wp-event-plugin' ),
				'use_featured_image'    => _x( 'Use as featured image', 'event', 'wp-event-plugin' ),
				'filter_items_list'     => __( 'Filter Events list', 'wp-event-plugin' ),
				'items_list_navigation' => __( 'Events list navigation', 'wp-event-plugin' ),
				'items_list'            => __( 'Events list', 'wp-event-plugin' ),
				'new_item'              => __( 'New Event', 'wp-event-plugin' ),
				'add_new'               => __( 'Add New', 'wp-event-plugin' ),
				'add_new_item'          => __( 'Add New Event', 'wp-event-plugin' ),
				'edit_item'             => __( 'Edit Event', 'wp-event-plugin' ),
				'view_item'             => __( 'View Event', 'wp-event-plugin' ),
				'view_items'            => __( 'View Events', 'wp-event-plugin' ),
				'search_items'          => __( 'Search Events', 'wp-event-plugin' ),
				'not_found'             => __( 'No Events found', 'wp-event-plugin' ),
				'not_found_in_trash'    => __( 'No Events found in trash', 'wp-event-plugin' ),
				'parent_item_colon'     => __( 'Parent Event:', 'wp-event-plugin' ),
				'menu_name'             => __( 'Events', 'wp-event-plugin' ),
			],
			'public' => true,
			'publicly_queryable' => true,
			'has_archive' => __( 'events'),   
			'menu_icon' => 'dashicons-buddicons-groups',
			'show_ui' => true, 
			'query_var' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array('slug' => __( 'event')), 
			'menu_position' => 10,
			'supports' => array('title','editor'),
			'show_in_rest' => true,
			'rest_base'             => 'event',
			'rest_controller_class' => 'WP_REST_Posts_Controller'
		]
	);
	$taglabels = array(
		'name' => __( 'Tags','wp-event-plugin'),
		'singular_name' => __( 'Tags','wp-event-plugin'), 
		'search_items' =>  __( 'Search Tag','wp-event-plugin' ),
		'all_items' => __( 'All Tag','wp-event-plugin' ),
		'edit_item' => __( 'Edit Tag','wp-event-plugin' ), 
		'update_item' => __( 'Update Tag','wp-event-plugin' ),
		'add_new_item' => __( 'Add New Tag','wp-event-plugin' ),
		'new_item_name' => __( 'New Tag Name','wp-event-plugin' ),
		'menu_name' => __( 'Tag','wp-event-plugin' ),
	); 	
	register_taxonomy('event-tag','event', 
		array(
			'hierarchical' => false,
			'labels' => $taglabels,
			'show_ui' => true,
			'query_var' => false,
			'show_in_rest' => true,
			'rest_base'             => 'genre',
    		'rest_controller_class' => 'WP_REST_Terms_Controller',
		),
	);
	flush_rewrite_rules();  
}
add_action( 'init', 'event_init' );

/**
 * Sets the post updated messages for the `event` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `event` post type.
 */
function event_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['event'] = [
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Event updated. <a target="_blank" href="%s">View Event</a>', 'wp-event-plugin' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'wp-event-plugin' ),
		3  => __( 'Custom field deleted.', 'wp-event-plugin' ),
		4  => __( 'Event updated.', 'wp-event-plugin' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Event restored to revision from %s', 'wp-event-plugin' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Event published. <a href="%s">View Event</a>', 'wp-event-plugin' ), esc_url( $permalink ) ),
		7  => __( 'Event saved.', 'wp-event-plugin' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Event submitted. <a target="_blank" href="%s">Preview Event</a>', 'wp-event-plugin' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Event</a>', 'wp-event-plugin' ), date_i18n( __( 'M j, Y @ G:i', 'wp-event-plugin' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Event draft updated. <a target="_blank" href="%s">Preview Event</a>', 'wp-event-plugin' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	];

	return $messages;
}

add_filter( 'post_updated_messages', 'event_updated_messages' );

/**
 * Sets the bulk post updated messages for the `event` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `event` post type.
 */
function event_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages['event'] = [
		/* translators: %s: Number of Events. */
		'updated'   => _n( '%s Event updated.', '%s Events updated.', $bulk_counts['updated'], 'wp-event-plugin' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Event not updated, somebody is editing it.', 'wp-event-plugin' ) :
						/* translators: %s: Number of Events. */
						_n( '%s Event not updated, somebody is editing it.', '%s Events not updated, somebody is editing them.', $bulk_counts['locked'], 'wp-event-plugin' ),
		/* translators: %s: Number of Events. */
		'deleted'   => _n( '%s Event permanently deleted.', '%s Events permanently deleted.', $bulk_counts['deleted'], 'wp-event-plugin' ),
		/* translators: %s: Number of Events. */
		'trashed'   => _n( '%s Event moved to the Trash.', '%s Events moved to the Trash.', $bulk_counts['trashed'], 'wp-event-plugin' ),
		/* translators: %s: Number of Events. */
		'untrashed' => _n( '%s Event restored from the Trash.', '%s Events restored from the Trash.', $bulk_counts['untrashed'], 'wp-event-plugin' ),
	];

	return $bulk_messages;
}
add_filter( 'bulk_post_updated_messages', 'event_bulk_updated_messages', 10, 2 );
add_action( 'add_meta_boxes', 'event_add_meta_box' );
function event_add_meta_box() {
	add_meta_box( 'Extra Details', 'Extra Details', 'extra_details', 'event', 'normal' ); 
}
function extra_details($post) { ?>
<table width="100%" class="extraInfo">
	<tr>
		<td valign="top" style="width:20%;"><strong><?php _e('Organizer','wp-event-plugin'); ?></strong></td>
		<td style="width:80%;"><input type="text" name="organizer" value="<?php echo get_post_meta( $post->ID, '_organizer', true ); ?>" style="width:100%;"></td>
	</tr>
	<tr>
		<td valign="top" style="width:20%;"><strong><?php _e('Event Time','wp-event-plugin'); ?></strong></td>
		<td style="width:80%;"><input type="text" name="event_time" value="<?php echo date("Y-m-d h:i:sa", get_post_meta( $post->ID, '_timestamp', true )); ?>" style="width:100%;"></td>
	</tr>
	<tr>
		<td valign="top" style="width:20%;"><strong><?php _e('E-mail','wp-event-plugin'); ?></strong></td>
		<td style="width:80%;"><input type="text" name="Email" value="<?php echo get_post_meta( $post->ID, '_email', true ); ?>" style="width:100%;"></td>
	</tr>
	<tr>
		<td valign="top" style="width:20%;"><strong><?php _e('Address','wp-event-plugin'); ?></strong></td>
		<td style="width:80%;"><input type="text" name="address" value="<?php echo get_post_meta( $post->ID, '_address', true ); ?>" style="width:100%;"></td>
	</tr>
	<tr>
		<td valign="top" style="width:20%;"><strong><?php _e('Latitude','wp-event-plugin'); ?></strong></td>
		<td style="width:80%;"><input type="text" name="latitude" value="<?php echo get_post_meta( $post->ID, '_latitude', true ); ?>" style="width:100%;"></td>
	</tr>
	<tr>
		<td valign="top" style="width:20%;"><strong><?php _e('Longitude','wp-event-plugin'); ?></strong></td>
		<td style="width:80%;"><input type="text" name="longitude" value="<?php echo get_post_meta( $post->ID, '_longitude', true ); ?>" style="width:100%;"></td>
	</tr>
</table> 
<?php }
add_action( 'save_post_event', 'save_event_extra_info' );
function save_event_extra_info() {
	global $post;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
	if ( isset( $_POST['organizer'] ) ){ 
		update_post_meta( $post->ID, '_organizer', $_POST['organizer'] ); 
	} 
    if ( isset( $_POST['event_time'] ) ){ 
		update_post_meta( $post->ID, '_timestamp', $_POST['event_time'] ); 
	}
    if ( isset( $_POST['Email'] ) ){ 
		update_post_meta( $post->ID, '_email', $_POST['Email'] ); 
	}
    if ( isset( $_POST['address'] ) ){ 
		update_post_meta( $post->ID, '_address', $_POST['address'] ); 
	}
    if ( isset( $_POST['latitude'] ) ){ 
		update_post_meta( $post->ID, '_latitude', $_POST['latitude'] ); 
	}
    if ( isset( $_POST['longitude'] ) ){ 
		update_post_meta( $post->ID, '_longitude', $_POST['longitude'] ); 
	}
}
add_action( 'rest_api_init', function () {
	register_rest_route( 'wp/v2', '/event/', array(
		'methods' => 'GET',
		'callback' => 'getevent'
		) );
	} 
);
function getevent(){
   	$args = array( 
      	'post_type' => 'event', 
      	'post_status' => 'publish', 
		'orderby' =>'meta_value_num',
		'meta_key' =>'_timestamp',
		'order' =>'ASC',
       	'nopaging' => true 
    );
    $query = new WP_Query( $args ); 
    $posts = $query->get_posts();   
    $output = array();
    foreach( $posts as $post ) {
		$eTime = date("Y-m-d h:i:sa", get_post_meta( $post->ID, '_timestamp', true ));
		$eID = get_post_meta($post->ID,"_eventid",true);
		$eOrganizer = get_post_meta($post->ID,"_organizer",true);
		$eEmail = get_post_meta($post->ID,"_email",true);
		$eAddress = get_post_meta($post->ID,"_address",true);
		$eLatitude = get_post_meta($post->ID,"_latitude",true);
		$eLongitude = get_post_meta($post->ID,"_longitude",true);
		$eTags = get_the_terms($post->ID,'event-tag');
		if(count($eTags)>0){
			$t = array();
			foreach($eTags as $tg){
				$t[] = $tg->name;
			}
			$show = join(", ",$t);	
		} 
    	$output[] = array( 
			'event_id' => $eID,
			'title' => $post->post_title,
			'event_time' => $eTime,
			'content' => $post->post_content,
			'organizer' => $eOrganizer,
			'email' => $eEmail,
			'address' => $eAddress,
			'latitude' => $eLatitude,
			'longitude' => $eLongitude,
			'tags' => $show
		);
    }
    wp_send_json( $output );
}


