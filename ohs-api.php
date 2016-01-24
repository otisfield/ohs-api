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
	 *	Homepage API
	 */
	 
	register_rest_route( $namespace, '/home/', array(
		'methods' => 'GET',
		'callback' => 'ohsapi_get_home'
	) ); 
	 
	/**
	 * Menu API
	 */
	 
	register_rest_route( $namespace, '/menu/', array(
		'methods'  => 'GET',
		'callback' => 'ohsapi_get_menu',
	) );
	
	/**
	 * Options API
	 */
	 
	register_rest_route( $namespace, '/options/', array(
		'methods'  => 'GET',
		'callback' => 'ohsapi_get_options',
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
	 
	register_rest_field( 'page', 'media', array(
		'get_callback' => 'posts_register_media',
		'update_callback' => null,
		'schema' => null
	) );
	
}

function ohsapi_get_menu() {
	
	$menu['navigation'] = wp_get_nav_menu_items(21);
	$theme_options = get_option('ohs_theme_options');
	
	if (isset($theme_options) && $theme_options != '') {
		if (isset($theme_options['logo']) && $theme_options['logo'] != '') {
			$menu['logo'] = $theme_options['logo'];
		}
	}
	
	$menu['homeLink'] = site_url();
	
	return $menu;
}

function ohsapi_get_options() {

	$theme_options = get_option('ohs_theme_options');
	
	return $theme_options;
}

function ohsapi_get_home() {

	$theme_options = get_option('ohs_theme_options');
	
	$home = array(
		'media' => array(
			'featured' => $theme_options['defaultHero']
		),
	);
	
	return $home;
}

function posts_register_media($object, $field_name, $request) {
	$featuredImageId = get_post_thumbnail_id($object['id']);
	
	$media = array();
	
	if ($featuredImageId) {
		$fullSizeFeatured = wp_get_attachment_image_src( $featuredImageId, 'full', false);
		$largeFeatured = wp_get_attachment_image_src( $featuredImageId, 'large', false);
		$mediumFeatured = wp_get_attachment_image_src( $featuredImageId, 'medium', false);
		$thumbnailFeatured = wp_get_attachment_image_src( $featuredImageId, 'thumbnail', false);
		$displayFeatured = wp_get_attachment_image_src( $featuredImageId, 'display', false);
		
		$media = array(
			'id' => $featuredImageId,
			'url' => $fullSizeFeatured[0],
			'width' => $fullSizeFeatured[1],
			'height' => $fullSizeFeatured[2],
			'thumbnail' => array(
				'url' => $thumbnailFeatured[0],
				'width' => $thumbnailFeatured[1],
				'height' => $thumbnailFeatured[2]
			),
			'display' => array(
				'url' => $displayFeatured[0],
				'width' => $displayFeatured[1],
				'height' => $displayFeatured[2]
			),
			'medium' => array(
				'url' => $mediumFeatured[0],
				'width' => $mediumFeatured[1],
				'height' => $mediumFeatured[2]
			),
			'large' => array(
				'url' => $largeFeatured[0],
				'width' => $largeFeatured[1],
				'height' => $largeFeatured[2]			
			),
			'featured' => $fullSizeFeatured[0]
		);
	} else {
		$theme_options = get_option('ohs_theme_options');
		$media['featured'] = $theme_options['defaultHero'];
	}
	
	return $media;
}
