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
	if (is_callable('shell_exec') && false === stripos(ini_get('disable_functions'))){
		rc_shex($post_url);
	} else if(ini_get('allow_url_fopen')){
		rc_php_only($post_url);
	} else {
		return;
	}
}, 10, 1 );



function rc_shex($url){
	shell_exec("curl $url");
	echo shell_exec("curl -I  $url");
}

function rc_wp_only($url){
	$args=[
		'method'=>'GET',
		'body'=>[
			'url'=>$url,
			'time'=>date("d/m/Y - H:i:s")
		]
	];
	$request_raw = wp_remote_request($post_url, $args);
}

function rc_php_only($url){
	file_get_contents($url);
}