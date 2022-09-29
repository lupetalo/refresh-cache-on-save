<?php
/**
 * Plugin Name: Refresh Cache
 * Text Domain: rc
 * Plugin URI: https://github.dev/lupetalo/refresh-cache-on-save
 */

add_action( 'save_post', function ( $post_id ) {
  if ( wp_is_post_revision( $post_id ) )  return;
	$post_url=get_permalink( $post_id );
	$post_type=get_post_type($post_id);
  if (!is_post_type_viewable($post_type)) return;
	$scheduled=wp_schedule_single_event( time() + 5, 'visit_url', [$post_url] );
},10,1 );

add_action( 'visit_url', function ($post_url){
	if (is_callable('shell_exec')){
	    echo shell_exec("curl $post_url");
	    echo shell_exec("curl -I  $post_url");
	} else {
		error_log('shell_exec not available');
		return;
	}
}, 10, 1 );