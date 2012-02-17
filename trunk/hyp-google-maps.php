<?php
/*
Plugin Name: Hyp Google Maps
Plugin URI: http://google-maps.myhyperspace.com/
Description: A Google Map Plugin
Version: 1.0
Author: Adam J Nowak
Author URI: http://hyperspatial.com/
License: 
*/

/*

Geocode Args:

width = enter css width value, 100px, 50% etc
height = enter css height value, 100px, 50% etc
center = enter DDS lat,long separated by a comma, or enter 'user_id' for user location
zoom = google zoom value
options = Enter a comma seperates list of javascript options

Map Args:

width = enter css width value, 100px, 50% etc
height = enter css height value, 100px, 50% etc
center = enter DDS lat,long separated by a comma, or enter 'user_id' for user location
zoom = google zoom value
heading = Enter a heading for the info window
content = Enter content for the info window
options = Enter a comma seperates list of javascript options

*/

//Constants
define('HGM_PLUGIN',WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/');
define('HGM_INCLUDES',dirname(__FILE__) . '/includes/');

//Load Files
require(HGM_INCLUDES . 'map-classes.php');
require(HGM_INCLUDES . 'admin.php');

/* ~~~~~~~~~~~~~~ Functions ~~~~~~~~~~~~~~ */

/* Load Api */
function hgm_load_api(){ $api_loader = new HgmApiLoader(); }

/* Geocoder */
function hgm_geocoder($args,$shortcode = false){
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
function hgm_map($args,$shortcode = false){
	global $hgm_maps;
	if($shortcode) ob_start();
	$hgm_maps[] = new HgmMap($args);
	if($shortcode){
		$main_function_output = ob_get_contents();
		ob_end_clean();
		return $main_function_output;
	}
}

/* Geocoder Shortcode - [hgm_geocoder width="400" height="400"] */
function hgm_geo_shortcode($args){ return hgm_geocoder($args,true); }

/* Map Shortcode - [hgm_map width="400" height="400"] */
function hgm_map_shortcode($args){ return hgm_map($args,true); }

/* Enqueue Styles */
function hgm_styles(){ wp_enqueue_style('hgm_styles', HGM_PLUGIN . 'style.css'); }

/* ~~~~~~~~~~~~~~ Actions ~~~~~~~~~~~~~~ */

add_action('wp_footer','hgm_load_api');
add_shortcode('hgm_geocoder','hgm_geo_shortcode');
add_shortcode('hgm_map','hgm_map_shortcode');
add_action('wp_print_styles','hgm_styles');
?>