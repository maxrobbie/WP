<?php /**
 * Plugin Name:     WP Event Plugin
 * Description:     Event plugin
 * Author:          Max Robbie
 * Text Domain:     wp-event-plugin
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wp_Event_Plugin
 */

defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );
define('EVENT_DIR' , dirname(__FILE__));
define('EVENT_URL' , plugin_dir_url( __FILE__ ));
require_once( EVENT_DIR . '/post-types/event.php' );
if ( is_admin() ) {
	require_once( EVENT_DIR . '/admin/setting.php' );
}
function eventago($date) {
    $diff = abs($date - time());
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    $hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));
    $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
    $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));
    $display = "";
    if($years > 0){
        $display .= $years. "years ";
    }
    if($months > 0){
        $display .= $months. "months ";
    }
    if($days > 0){
        $display .= $days. "days ";
    }
    if($hours > 0){
        $display .= $hours. "hours ";
    }
    if($minutes > 0){
        $display .= $minutes. "minutes ";
    }
    echo "[".date("Y-m-d h:i:sa", $date)."] ". $display." left";
}
add_action( 'pre_get_posts', 'event_query' );
function event_query( $query ) {
	if ( $query->is_archive() && $query->is_main_query() && !is_admin() && $query->query_vars['post_type'] == 'event' ) { 
        $meta_query = array(
            array(
              'key'     => '_timestamp',
              'value'   => time(),
              'compare' => '>=',
            ),
        );
        $query->set('meta_query', $meta_query);
		$query->set('orderby','meta_value_num');
		$query->set('meta_key','_timestamp');
		$query->set('order','ASC'); 
	}
}
add_filter( 'template_include', 'event_list_template');
function event_list_template( $template_path ) {
	if ( is_archive( 'events' )  ) {
		 $template_path = EVENT_DIR. '/view/event-list.php';
    }
	return $template_path;
}
add_action("wp_head","archive_event_style");
function archive_event_style(){
    if ( is_archive( 'events' )  ) {
?>
    <style>
    .event_data{float:left;width:100%;margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #ccc;}
    .event_data span{float:left;width:50%;}
    </style>
<?php }
} 