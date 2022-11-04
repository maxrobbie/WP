<?php if ( ! function_exists('event_admin_page') ) :
add_action( 'admin_menu' , 'event_admin_page' );
function event_admin_page(){
    $settings_page = add_submenu_page(
        'edit.php?post_type=event',
        __('Settings'),
        __('Settings'),
        'manage_options',
        'event_admin_setting',
        'event_admin_setting');
	add_action( "load-{$settings_page}",'event_load_settings_page');
}
endif; 
function event_load_settings_page() {
	if ( $_POST["event-settings-submit"] == 'Y' ) {
		check_admin_referer( "event-settings-page" );
		event_import_function();
		$url_parameters = isset($_GET['tab'])? 'updated=true&tab='.$_GET['tab'] : 'updated=true';
		wp_redirect(admin_url('edit.php?post_type=event&page=event_admin_setting&'.$url_parameters));
		exit;
	}
}
function update_event_tags($eID,$tags){
    if(count($tags)> 0){
        foreach($tags as $t){
            $eTags = term_exists($t,'event-tag');
            if ($eTags['term_id'] == NULL){
                $nTag = wp_insert_term($t,'event-tag');
                wp_set_object_terms((int)$eID,(int)$nTag['term_id'],'event-tag',true);     
            }else{
                wp_set_object_terms((int)$eID,(int)$eTags['term_id'],'event-tag',true);     
            } 
        }
    }
}
function event_import_function() {
    if(isset($_POST['jsonContent'])){
        $jsonData = json_decode(stripslashes(wp_filter_post_kses($_POST['jsonContent'])),true);
        foreach($jsonData as $data){
            $args = array(
                'post_type' => 'event',
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => '_eventid',
                        'value' => (int)$data['id'],
                        'compare' => '=',
                    ),
                 ),
            );
            $eventQuery = new WP_Query( $args ); 
		    if ($eventQuery->have_posts()) {
                $a = $t = $u = 0;
                while ( $eventQuery->have_posts() ) : $eventQuery->the_post(); 
                    $timeCheck = get_post_meta(get_the_ID(),'_timestamp',true);
                    if($timeCheck >= time()){
                        $updateArry = array(
                            'ID' => get_the_ID(),
                            'post_title' => (string)$data['title'],
                            'post_content' => (string)$data['about'],
                            'post_status'   => 'publish'
                        );
                        wp_update_post($updateArry);
                        update_post_meta(get_the_ID(),'_organizer',(string)$data['organizer']);
                        update_post_meta(get_the_ID(),'_timestamp',strtotime((string)$data['timestamp']));
                        update_post_meta(get_the_ID(),'_email',(string)$data['email']);
                        update_post_meta(get_the_ID(),'_address',(string)$data['address']);
                        update_post_meta(get_the_ID(),'_latitude',(string)$data['latitude']);
                        update_post_meta(get_the_ID(),'_longitude',(string)$data['longitude']);
                        update_event_tags(get_the_ID(),$data['tags']);
                        $u++;
                    }else{
                        $trashArry = array(
                            'ID' => get_the_ID(),
                            'post_status'   => 'trash',
                        );
                        wp_update_post($trashArry); 
                        $t++;    
                    }
                endwhile;
            }else{
                $addAry = array(
                    'post_type'     => 'event',
                    'post_title' => $data['title'],
                    'post_content' => $data['about'],
                    'post_status'   => 'publish'
                );
                $eventID = wp_insert_post( $addAry ); 
                update_post_meta($eventID,'_eventid',(int)$data['id']);
                update_post_meta($eventID,'_organizer',(string)$data['organizer']);
                update_post_meta($eventID,'_timestamp',strtotime((string)$data['timestamp']));
                update_post_meta($eventID,'_email',(string)$data['email']);
                update_post_meta($eventID,'_address',(string)$data['address']);
                update_post_meta($eventID,'_latitude',(string)$data['latitude']);
                update_post_meta($eventID,'_longitude',(string)$data['longitude']);
                update_event_tags($eventID,$data['tags']); 
                $a++;  
            } 
        }
    }
    $body = "<p>Event Added : ".$a.'<br/>Event Updated : '.$u.'<br/>Event Trashed : '.$t."<p>";
    $to = 'logging@agentur-loop.com';
    $subject = 'Event Import Report';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail( $to, $subject, $body, $headers );
}
function event_admin_tabs( $current = 'event' ) { 
    $tabs = array( 'event' => 'Settings'); 
    $links = array();
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=event_admin_setting&tab=$tab'>$name</a>";       
    }
    echo '</h2>';
}
function event_admin_setting() { 
	global $pagenow;
	?>
	<style>
		.grid{width:960px;display:block;margin:0 auto;}
		#header-container{margin-bottom:30px;overflow:hidden;}
		#body-container{clear:both;overflow:hidden;}
		#navigation ul{list-style:none;margin:0;padding:0;}
		#navigation li{float: left;list-style:none;margin-right: 10px;}
		textarea{width:80%;height:350px;}
    </style> 
	<div class="wrap">
		<?php
			if ( 'true' == esc_attr( $_GET['updated'] ) ) echo '<div class="updated" ><p>Events imported successfully.</p></div>';
			if ( isset ( $_GET['tab'] ) ) event_admin_tabs($_GET['tab']); else event_admin_tabs('event');
		?>
		<div id="poststuff">
			<form method="post" action="<?php admin_url('edit.php?post_type=event&page=event_admin_setting'); ?>">
				<?php
				wp_nonce_field( "event-settings-page" ); 
				if ( $_GET['page'] == 'event_admin_setting' ){ 
				
					if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab']; 
					else $tab = 'event'; 
					
					echo '<table class="form-table">';
					switch ( $tab ){
						case 'event' :
							?>
							<tr>
								<th><label for="event_intro">Json Content : </label></th>
								<td>
									<textarea name="jsonContent"></textarea>
								</td>
							</tr>
                            <tr class="submit" style="clear: both;"><td style="padding:0px;margin-top:20px;">
								<input type="submit" name="Submit"  class="button-primary" value="Import" />
								<input type="hidden" name="event-settings-submit" value="Y" /></td>
							</tr>
							<?php
						break; 
					}
					echo '</table>';
				}
				?>
			</form>
		</div>
	</div>
<?php }