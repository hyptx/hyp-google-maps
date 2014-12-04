<?php
/*
Plugin Name: Hyp Google Maps
Plugin URI: https://github.com/hyptx/hyp-google-maps
Description: A Google Maps - API V3 plugin and geocoder. Geocode users, posts, pages and custom post types.
Version: 1.1
Author: Adam J Nowak
Author URI: http://hyperspatial.com/
License: 
*/

//Constants
define('HGM_PLUGIN',WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/');
define('HGM_INCLUDES',dirname(__FILE__) . '/includes/');

//Load Files
require(HGM_INCLUDES . 'map-classes.php');
require(HGM_INCLUDES . 'admin.php');

//Add User Geocoder
$hgm_user_geocoder = new HgmUserGeocoder();

/* ~~~~~~~~~~~~~~ Functions ~~~~~~~~~~~~~~ */

/*  Get User Location */
function hgm_get_user_location($user_id){ return get_user_meta($user_id,'hgm_user_geocode',true); }

/*  Get Post Location */
function hgm_get_post_location($post_id){ return get_post_meta($post_id,'hgm_user_geocode',true); }

/*  Get Post Location */
function hgm_get_full_map_link($location){ return 'https://maps.google.com/maps?t=h&z=16&q=loc:' . $location; }

/* Load Api */
function hgm_load_api(){ $api_loader = new HgmApiLoader(); }

/* Load Post Geocoder */
function hgm_load_post_geocoder(){ return new HgmPostGeocoder(); }
if(is_admin()) add_action('load-post.php','hgm_load_post_geocoder');

/* Geocoder */
function hgm_geocoder($args = '',$shortcode = false){
	global $hgm_geocoder;
	if($shortcode) ob_start();
	$hgm_geocoder = new HgmGeocoder($args);
	if($shortcode){
		$main_function_output = ob_get_contents();
		ob_end_clean();
		return $main_function_output;
	}
}

/* Map */
function hgm_map($args = '',$shortcode = false){
	global $hgm_maps;
	if($shortcode) ob_start();
	$hgm_maps[] = new HgmMap($args);
	if($shortcode){
		$main_function_output = ob_get_contents();
		ob_end_clean();
		return $main_function_output;
	}
}

/* Geocoder Shortcode - [hgm_geocoder width="400px" height="400px"] */
function hgm_geo_shortcode($args){ return hgm_geocoder($args,true); }

/* Map Shortcode - [hgm_map width="400px" height="400px"] */
function hgm_map_shortcode($args){ return hgm_map($args,true); }

/* Enqueue Styles */
function hgm_styles(){ wp_enqueue_style('hgm_styles',HGM_PLUGIN . 'style.css'); }

/* Enqueue Javascript */
function hgm_enqueue_js(){ wp_enqueue_script('hgm_scripts',HGM_PLUGIN . 'js/scripts.js'); }

/* ~~~~~~~~~~~~~~ Actions ~~~~~~~~~~~~~~ */

add_action('wp_footer','hgm_load_api');
add_action('admin_footer','hgm_load_api');
add_shortcode('hgm_geocoder','hgm_geo_shortcode');
add_shortcode('hgm_map','hgm_map_shortcode');
add_action('wp_print_styles','hgm_styles');
add_action('admin_print_styles','hgm_styles');
add_action('wp_print_scripts','hgm_enqueue_js');
add_action('admin_print_scripts','hgm_enqueue_js');
?>
