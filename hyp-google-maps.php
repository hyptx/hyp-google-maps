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

//Constants
define('HGM_PLUGIN',WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/');
define('HGM_INCLUDES',dirname(__FILE__) . '/includes/');

//Load Files
require(HGM_INCLUDES . 'map-classes.php');
require(HGM_INCLUDES . 'admin.php');

/* ~~~~~~~~~~~~~~ Functions ~~~~~~~~~~~~~~ */

/* Load Api */
function hgm_load_api(){ $api_loader = new HgmApiLoader(); }

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

/* Shortcode - [hgm_map width="400" height="400"] */
function hgm_shortcode($args){ return hgm_map($args,true); }

/* ~~~~~~~~~~~~~~ Actions ~~~~~~~~~~~~~~ */

add_action('wp_footer','hgm_load_api');
add_shortcode('hgm_map','hgm_shortcode');
?>