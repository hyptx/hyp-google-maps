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

width = Enter css width value, 100px, 50% etc
height = Enter css height value, 100px, 50% etc
center = Enter DDS lat,long separated by a comma, or enter 'user_id' for user location
zoom = Google zoom value
options = Enter a comma seperates list of javascript options
position = Enter 'above' to display map above the o form

Map Args:

width = Enter css width value, 100px, 50% etc
height = Enter css height value, 100px, 50% etc
center = Enter DDS lat,long separated by a comma, or enter 'user_id' for user location
zoom = Google zoom value
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

//add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
//add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { ?>

	<h3>Extra profile information</h3>

	<table class="form-table">

		<tr>
			<th><label for="hgm-user-geocoder">HGM User Location</label></th>

			<td>
				<input type="text" name="hgm_user_geocoder" id="hgm-user-geocoder" value="<?php echo esc_attr( get_the_author_meta( 'hgm_user_geocode', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter your Twitter username.</span>
                
                
                <?php hgm_geocoder()?>
                
			</td>
		</tr>

	</table>
<?php }



add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_usermeta( $user_id, 'hgm_user_geocoder', $_POST['hgm_user_geocoder'] );
}


$hgm_user_geocoder = new HgmUserGeocoder();


class HgmUserGeocoder{
	public function __construct(){
		add_action('show_user_profile',array(&$this,'print_user_geocoder'),1,2);
		add_action('edit_user_profile',array(&$this,'print_user_geocoder'),1,2);
	}
	public function print_user_geocoder($user){?>
        <h3>HGM Geocoding</h3>
        <table class="form-table">
    		<tr>
                <th><label for="hgm-user-geocoder">User Location</label></th>
                <td><?php hgm_geocoder()?></td>
            </tr>
        </table>
    	<?php
	}
}




/* Load Api */
function hgm_load_api(){ $api_loader = new HgmApiLoader(); }

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

/* Geocoder Shortcode - [hgm_geocoder width="400" height="400"] */
function hgm_geo_shortcode($args){ return hgm_geocoder($args,true); }

/* Map Shortcode - [hgm_map width="400" height="400"] */
function hgm_map_shortcode($args){ return hgm_map($args,true); }

/* Enqueue Styles */
function hgm_styles(){ wp_enqueue_style('hgm_styles', HGM_PLUGIN . 'style.css'); }

/* ~~~~~~~~~~~~~~ Actions ~~~~~~~~~~~~~~ */

add_action('wp_footer','hgm_load_api');
add_action('admin_footer','hgm_load_api');
add_shortcode('hgm_geocoder','hgm_geo_shortcode');
add_shortcode('hgm_map','hgm_map_shortcode');
add_action('wp_print_styles','hgm_styles');
add_action('admin_print_styles','hgm_styles');
?>