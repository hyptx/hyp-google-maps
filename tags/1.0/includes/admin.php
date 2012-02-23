<?php /* Admin Page */

$hgm_field_names = array(
	'hgm_api_key',
	'hgm_location',
	'hgm_post_types'
);
/* Admin Menu */
function hgm_create_admin_menu(){
	add_menu_page('HGMaps','HGMaps',3,'hgm-settings','hgm_settings_page',HGM_PLUGIN . 'graphics/icon.png');
	add_action('admin_init','hgm_register_options');
}
add_action('admin_menu','hgm_create_admin_menu');
/* Register hgm Options */
function hgm_register_options(){
	global $hgm_field_names;
	foreach($hgm_field_names as $field_name){ register_setting('hgm_options',$field_name); }
}
/* Settings Page */
function hgm_settings_page(){
	global $hgm_field_names;
	hgm_set_option_defaults();
	foreach($hgm_field_names as $field_name){ ${$field_name} = get_option($field_name);}
	?>
	<style type="text/css">
	a{text-decoration:none}
	.shadow{box-shadow:1px 1px 4px #333; -webkit-box-shadow:1px 1px 4px #333}
	input[type="text"],textarea{color:#222; background-color:#f4f4f4}
	.slide-card input[type="text"]{width:380px}
	.help-text{font-size:11px; font-style:italic;}
	.top-help{display:block; margin-top:-12px}
	.example{color:#669;}
	form {margin-top:20px;}
	h3{margin-top:40px;border-bottom:1px solid #8E7556; width:620px; color:#8E7556; padding-bottom:5px; font-size:21px}
	h4{font-size:16px; margin:0; padding:4px; background:#C9BBA8}
	label{font-weight:bold;}
	input.button-primary{margin-top:20px;}
	form p{margin-bottom:12px;}
	form .remaining{background-color:#FFF0D3; width:3em}
	div.left{float:left; margin:0 32px 20px 0}
	.wrap .save-btn{float:right; border:none; background:none; color:#21759B; font-size:14px; font-weight:normal; cursor:pointer; margin:0; padding:0; line-height:18px;}
	.wrap .save-btn:hover{color:#D54E21}
	</style>
    <script language="javascript">
	function hgmExpandCollapse() {
		for (var i=0; i<hgmExpandCollapse.arguments.length; i++) {
			var element = document.getElementById(hgmExpandCollapse.arguments[i]);
			element.style.display = (element.style.display == "none") ? "block" : "none";
		}
	}
	</script>
	<div class="wrap">
		<h2>HGMaps Settings</h2>
		<form name="settings" method="post" action="options.php">
			<?php settings_fields('hgm_options') ?>
            <h3>Basic Options<input type="submit" class="save-btn" value="<?php _e('save') ?>" /><span class="save-btn" onclick="hgmExpandCollapse('hgm-help');">view help<span style="color:#222">&nbsp;&nbsp;|&nbsp;&nbsp;</span></span></h3>
            <div id="hgm-help" style="display:none;border-bottom:1px solid #8E7556; width:620px">
            	<p>This plugin allows you to easily create Google maps and geocode posts, pages and custom post types. To learn more about map options see <a href="http://code.google.com/apis/maps/documentation/javascript/reference.html#MapOptions" target="_blank">Google MapOptions</a></p>
            	<ul>
                	<li><strong>API Key</strong> - Obtain your <a href="https://code.google.com/apis/console" target="_blank">API Key</a> and enter it into this field</li>
                    <li><strong>Geocoded Post Types</strong> - Post types to geocode and display the map/form</li>
                    <li><strong>Default Location</strong> - Stored Lat,Long in decimal degrees for the starter map</li>
                    <li><strong>Enter Location</strong> - Input an address or general location, then press the 'Geocode' button</li>
            	</ul>
                <h4>Manual Usage:</h4>
                <p>
                	<strong>Shortcode:</strong><br />
                	<code>[hgm_map width="400px" height="400px"]</code><br />
					<code>[hgm_geocoder width="400px" height="400px"]</code>                
                </p>
                <p>
                	<strong>PHP:</strong><br />
                    <code>&lt;php hgm_map('width=400px&height=400px')); ?&gt;</code><br />
                    <code>&lt;php hgm_map(array('width' => '400px','height' => '400px')); ?&gt;</code><br />
					<code>&lt;php hgm_geocoder(array('width' => '400px','height' => '400px')); ?&gt;</code><br /><br />
                    <code>&lt;php hgm_get_user_location($user_id); ?&gt;</code><br /> 
                    <code>&lt;php hgm_get_post_location($post_id); ?&gt;</code>
                </p>
                <p>
               	<h4>Map Arguments:</h4><br />
                    <ul>
                    	<li><strong>width</strong> - Enter css width value, 100px, 50% etc</li>
                        <li><strong>height</strong> - Enter css height value, 100px, 50% etc</li>
                        <li><strong>center</strong> - Enter lat,long (decimal deg) separated by a comma</li>
                        <li><strong>zoom</strong> - Google zoom value</li>
                        <li><strong>heading</strong> - Enter a heading for the info window</li>
                        <li><strong>content</strong> - Enter content for the info window</li>
                        <li><strong>options</strong> - Enter a comma separated list of <a href="http://code.google.com/apis/maps/documentation/javascript/reference.html#MapOptions" target="_blank">Google MapOptions</a>.  Ex = <em>maxZoom:2,minZoom:10</em></li>
                    </ul>
                </p>
                <p>
               	<h4>Geocode Arguments:</h4><br />
                    <ul>
                    	<li><strong>width</strong> - Enter css width value, 100px, 50% etc</li>
                        <li><strong>height</strong> - Enter css height value, 100px, 50% etc</li>
                        <li><strong>center</strong> - Enter lat,long (decimal deg) separated by a comma</li>
                        <li><strong>zoom</strong> - Google zoom value</li>
                         <li><strong>options</strong> - Enter a comma separated list of Google MapOptions</li>
                        <li><strong>position</strong> - Enter 'above' to display map above the form</li>
                    </ul>
                </p>
                <p>
               	<h4>Geocode Complete Event:</h4><br />
                    <code>&lt;script type="text/javascript"&gt;<br />
                    function saveValue(){ FORMFIELD.value = hgmLocation; }
					eventObject.addEventlistener('geocoded',saveValue);<br />
                    &lt;/script&gt;</code>                
                </p>  
                <span class="save-btn" onclick="hgmExpandCollapse('hgm-help');">close help</span>            
            </div>
            <p>
            	<label>Google API Key - <a href="https://code.google.com/apis/console" target="_blank">Get Key</a></span></label><span class="help"><br />
            	<input name="hgm_api_key" type="text" value="<?php echo $hgm_api_key ?>" size="50"/>
            </p>
            <p>
            	<label>Geocoded Post Types</label>
				<p>
				<?php 
				$post_types = get_post_types(array('public' => 'true'),'names');
				$rep = array('_','-');
				foreach($post_types as $post_type){
                    if($post_type == 'attachment') continue; ?>
                    <input type="checkbox" name="hgm_post_types[<?php echo $post_type ?>]" value="true" <?php if($hgm_post_types[$post_type] == 'true') echo 'checked="checked"' ?> /> <?php echo str_replace($rep,' ',$post_type) ?><br />
                    <?php
                }
                ?>
                </p>
            </p>           
            <p>
            	<label>Default Location</label><span class="help"><br />
            	<input name="hgm_location" id="hgm-location" type="text" value="<?php echo $hgm_location ?>" size="50"/>
                <?php hgm_geocoder(array('marker' => $hgm_location,'height' => '200px')) ?>
            </p>              
            <p>
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
    	</form>
    </div><!--/.wrap-->
    <script type="text/javascript">
		var hgmLocationField = document.getElementById('hgm-location');
		function hgmPopulateLoc(){ hgmLocationField.value = hgmLocation; }
		eventObject.addEventlistener('geocoded',hgmPopulateLoc);
	</script>

    <?php
}
/* ~~~~~~~~~~~ Functions ~~~~~~~~~~~ */

/* Ck Option */
function hgm_ck_option($field,$default){
	$option_value = get_option($field);
	if(!$option_value || $option_value == '') update_option($field,$default);
}

/* Defaults */
function hgm_set_option_defaults(){
	global $hgm_field_names;
	foreach($hgm_field_names as $field){
		switch($field){
			case 'hgm_location': hgm_ck_option('hgm_location','42.284821,-72.837902'); break;
		}
	}	
	return true;
}
?>