<?php /* Admin Page */

$hgm_field_names = array(
	'hgm_api_key'

);
/* Admin Menu */
function hgm_create_admin_menu(){
	add_menu_page('HGM Maps','HGM&nbsp;Maps',3,'hgm-settings','hgm_settings_page',HGM_PLUGIN . 'graphics/icon.png');
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
	foreach($hgm_field_names as $field_name){ ${$field_name} = get_option($field_name); }
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
		<h2>HGM Maps Settings</h2>
		<form name="settings" method="post" action="options.php">
			<?php settings_fields('hgm_options') ?>
            <h3>Basic Options<input type="submit" class="save-btn" value="<?php _e('save') ?>" /><span class="save-btn" onclick="hgmExpandCollapse('hgm-help');">view help<span style="color:#222">&nbsp;&nbsp;|&nbsp;&nbsp;</span></span></h3>
            <div id="hgm-help" style="display:none">
            	<p>This plugin allows you to easily create Google maps on your site</p>
            	<ul>
                	<li><strong>API Key</strong> - Obtain your <a href="https://code.google.com/apis/console" target="_blank">API Key</a> and enter it into this field</li>
            	</ul>
            </div>
            <p>
            <label>Google API Key - <a href="https://code.google.com/apis/console" target="_blank">Get Key</a></span></label><span class="help"><br />
            	<input name="hgm_api_key" type="text" value="<?php echo $hgm_api_key ?>" size="50"/>
            </p>
            <div>
            	<label>Slider</label><br />
            	<select id="slider-dropdown" name="hgm_slider_type" onChange="this.form.submit();">
					<?php foreach($hgm_sliders as $slider): ?>
                    <option <?php if ($slider == $hgm_slider_type) echo 'selected="selected"' ?>><?php echo $slider ?></option>
                    <?php endforeach ?>
                </select>
            </div>   
            <p>
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
    	</form>
    </div><!--/.wrap-->
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
			case 'hgm_slider_type': hgm_ck_option('hgm_slider_type','jQuery Cycle'); break;
			
		}
	}	
	return true;
}
?>