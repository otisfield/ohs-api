<?php
/*
Plugin Name: OHS Rest API Plugin
Description: Enables the OHS API
Author: Derek Dorr
Version: 0.1
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

register_activation_hook( __FILE__, 'ohsapi_activate' );

function ohsapi_activate() {
	flush_rewrite_rules();
}

add_action( 'rest_api_init', 'ohsapi_register_api_hooks' );

function ohsapi_register_api_hooks() {
	$namespace = 'ohs/v1';
	
	/**
	 * Menu API
	 */
	 
	register_rest_route( $namespace, '/menu/', array(
		'methods'  => 'GET',
		'callback' => 'ohsapi_get_menu',
	) );
	
	/**
	 * Events API
	 */
	 
	register_rest_route( $namespace, '/events/', array(
		'methods'  => 'GET',
		'callback' => 'ohsapi_get_events',
	) );
	
	/**
	 * Add Fields to Posts API
	 */
	 
	register_rest_field( 'post', 'media', array(
		'get_callback' => 'posts_register_media',
		'update_callback' => null,
		'schema' => null
	) );
	
	/** 
	 * Add Fields to Pages API
	 */
	 
	register_rest_field( 'post', 'media', array(
		'get_callback' => 'posts_register_media',
		'update_callback' => null,
		'schema' => null
	) );
	
}

function ohsapi_get_menu() {
	
	$menu = wp_get_nav_menu_items(21);
	
	return $menu;
}

function ohsapi_get_events() {
	$events = new WP_Query(array('post_type' => 'event'));
	
	return $events;
}

function posts_register_media($object, $field_name, $request) {
	$featuredImageId = get_post_thumbnail_id($object['id']);
	
	$media = null;
	
	if ($featuredImageId) {
		$fullSizeFeatured = wp_get_attachment_image_src( $featuredImageId, 'full', false);
		$thumbnailFeatured = wp_get_attachment_image_src( $featuredImageId, 'thumbnail', false);
		
		$media = array(
			'id' => $featuredImageId,
			'url' => $fullSizeFeatured[0],
			'width' => $fullSizeFeatured[1],
			'height' => $fullSizeFeatured[2],
			'thumbnail' => array(
				'url' => $thumbnailFeatured[0],
				'width' => $thumbnailFeatured[1],
				'height' => $thumbnailFeatured[2]
			)
		);
	}
	
	return $media;
}
