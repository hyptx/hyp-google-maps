<?php /* The Map Classes */

class HgmApiLoader{
	public function __construct(){
		global $hgm_maps,$hgm_geocoder;	
		if($hgm_maps) $this->load_api();
		elseif($hgm_geocoder) $this->load_api(1);
		else return;
	}
	/* Load Api */
	private function load_api($geocode = false){
		if($geocode) $callback = '&callback=hgmGeoInitialize';
		else $callback = '&callback=hgmInitialize';
		?>
		<script type="text/javascript">
        function hgmLoadApi(){
            var script = document.createElement("script");
            script.type = "text/javascript";
            script.src = "<?php echo $this->is_ssl() ?>maps.googleapis.com/maps/api/js?key=<?php echo get_option('hgm_api_key') ?>&sensor=false<?php echo $callback ?>";
            document.body.appendChild(script);
        }
        window.onload = hgmLoadApi;
		<?php if(!$geocode): ?>
        function hgmInitialize(){ <?php $this->print_map_js() ?> }
		<?php endif ?>
        </script>
    	<?php		
	}
	/* Print Map Scripts */
	private function print_map_js(){
		global $hgm_maps;
		foreach($hgm_maps as $map){
			$element_id = $map->get_element_id();
			$instance_num = $map->get_instance_num();
			$options = $this->map_opt_check($map->options);	
			$comma_sep = $this->map_opt_sep($options);
			$map_js .= "
			hgm{$instance_num}LatLong = new google.maps.LatLng($map->center);
			var hgm{$instance_num}Options = {
				center: hgm{$instance_num}LatLong,
				zoom: $map->zoom,
				mapTypeId: google.maps.MapTypeId.ROADMAP{$comma_sep}
				$options
			}
			var map{$instance_num} = new google.maps.Map(document.getElementById('$element_id'),hgm{$instance_num}Options);
			var hgm{$instance_num}Marker = new google.maps.Marker({
    			position: hgm{$instance_num}LatLong
			});
			hgm{$instance_num}Marker.setMap(map{$instance_num});
			";
			$map_js .= $this->map_info_window($map,$instance_num);
		}
		echo $map_js;
	}
	/* Map Info Window */
	private function map_info_window($map,$instance_num){
		if($map->heading || $map->content){
			if($map->heading) $heading = "<h2>$map->heading</h2>";
			if($map->content) $content = "<div class=\"hgm-info-content\">$map->content</div>";
			$info_window .= "
			var hgm{$instance_num}Content = '<div class=\"hgm-info-window\">{$heading}{$content}</div>';
			var hgm{$instance_num}Infowindow = new google.maps.InfoWindow({
				content: hgm{$instance_num}Content,
				maxWidth: 300
			});
			google.maps.event.addListener(hgm{$instance_num}Marker,'click',function(){
				hgm{$instance_num}Infowindow.open(map{$instance_num},hgm{$instance_num}Marker);
			});			
			";
			return $info_window;
		}
	}
	/* SSL Check */
	private function is_ssl(){
		if($_SERVER['HTTPS'] == 'on') return 'https://';
		else return 'http://';
	}
	/* Map Options Check */
	private function map_opt_check($options){
		if(substr($options,-1) == ',') return $options = trim(substr_replace($options,'',-1));
		else return $options;
	}
	/* Map Options Seperator */
	private function map_opt_sep($options){ if($options) return $comma_sep = ','; }
}//END HgmApiLoader



class HgmMap{
	private static $instance;
	private $element_id,$instance_num;
	public function __construct($args = ''){
		global $hgm_geocoder;
		if($hgm_geocoder) echo '<div class="hgm-error">Geocoder cannot be created on a page with another map</div>';
		self::$instance += 1;
		$this->element_id = 'hgm-map-canvas-' . self::$instance;
		$this->instance_num = self::$instance;
		$this->save_map_data($args);
	}
	/* Save Map Data */
	private function save_map_data($args){
		$default_location = get_option('hgm_location');
		if(!$default_location) $default_location = '42.284821,-72.837902';
		$defaults = array(
			'width' => '400px',
			'height' => '400px',
			'center' => $default_location,
			'zoom' => 10,
		);
		$map_data_array = wp_parse_args($args,$defaults);
		foreach($map_data_array as $key => $value) $this->{$key} = $value;
		$this->print_canvas();
	}	
	/* Print Canvas */
	private function print_canvas(){
		echo '<div id="' . $this->element_id . '" class="hgm-map" style="max-width:' . $this->width . ';height:' . $this->height . ';"></div>';
	}
	/* Get Element Id */
	public function get_element_id(){ return $this->element_id; }	
	/* Get Instance Number */
	public function get_instance_num(){ return $this->instance_num; }	
}//END HgmMap


class HgmGeocoder{
	private static $instance;
	public function __construct($args = ''){
		global $hgm_maps,$hgm_geocoder;
		if($hgm_maps) echo '<div class="hgm-error">Geocoder cannot be created on a page with another map</div>';
		if(self::$instance >= 1) return;
		self::$instance = 1;
		$this->save_geo_data($args);
	}
	/* Save Geo Data */
	private function save_geo_data($args){
		$default_location = get_option('hgm_location');
		if(!$default_location) $default_location = '42.284821,-72.837902';
		$defaults = array(
			'width' => '500px',
			'height' => '300px',
			'center' => $default_location,
			'marker' => '',
			'zoom' => 12,
			'position' => 'below'
		);
		$geo_data_array = wp_parse_args($args,$defaults);
		foreach($geo_data_array as $key => $value) $this->{$key} = $value;
		$this->print_geo_html();
	}
	/* Print Geo Html */
	public function print_geo_html(){
		$options = $this->map_opt_check($this->options);	
		$comma_sep = $this->map_opt_sep($options);
		?>
		<script type="text/javascript">
		var hgmGeocoder;
		var hgmGeoMap;
		var marker;
		function hgmGeoInitialize(){
			hgmGeocoder = new google.maps.Geocoder();
			hgmGeoLatlng = new google.maps.LatLng(<?php echo $this->center ?>);
			var hgmGeoOptions = {
				center: hgmGeoLatlng,
				zoom: <?php echo $this->zoom ?>,
				mapTypeId: google.maps.MapTypeId.ROADMAP<?php echo $comma_sep ?>
				<?php echo $options ?>
			}
			hgmGeoMap = new google.maps.Map(document.getElementById("hgm-geo-map"),hgmGeoOptions);
			<?php if($this->marker): ?>
			hgmGeoMarker = new google.maps.Marker({
				map: hgmGeoMap, 
				position: new google.maps.LatLng(<?php echo $this->marker ?>)
			});
			<?php endif ?>		
		}
		</script>
        <?php if($this->position == 'below'): ?>
		<div id="hgm-geo-map" class="hgm-map" style="max-width:<?php echo $this->width ?>; height:<?php echo $this->height ?>;"></div>
        <?php endif ?>   
        <div id="hgm-geocode-form">
        	<label>Enter Location</label><br />
        	<input id="hgm-address" class="hgm-textbox" type="text" onKeyPress="if(event.keyCode == 13) { hgmCodeAddress(); return false;}" />
            <input id="hgm-geocode" class="button" type="button" value="GeoCode" onclick="hgmCodeAddress();" />
            <input id="hgm-latlong" name="hgm_user_geocode" class="hgm-textbox" type="text" readonly="readonly"/>
        </div>
        <?php if($this->position == 'above'): ?>
		<div id="hgm-geo-map" style="width:<?php echo $this->width ?>; height:<?php echo $this->height ?>;"></div>
        <?php
		endif;                    
	}
	/* Map Options Check */
	private function map_opt_check($options){
		if(substr($options,-1) == ',') return $options = trim(substr_replace($options,'',-1));
		else return $options;
	}
	/* Map Options Seperator */
	private function map_opt_sep($options){ if($options) return $comma_sep = ','; }
}//END HgmGeoLoader


class HgmUserGeocoder{
	public function __construct(){
		$hgm_geocode_users = get_option('hgm_geocode_users');
		if($hgm_geocode_users != 'true') return false;
		add_action('show_user_profile',array(&$this,'print_user_geocoder'),1,2);
		add_action('edit_user_profile',array(&$this,'print_user_geocoder'),1,2);
		add_action('personal_options_update',array(&$this,'save_user_geocode'));
		add_action('edit_user_profile_update',array(&$this,'save_user_geocode'));
	}
	/* print User Geocoder */
	public function print_user_geocoder($user){
		$user_location = esc_attr(get_the_author_meta('hgm_user_geocode',$user->ID));
		if($user_location) $args = array('width'=>'100%','height'=>'220px','center' => $user_location,'marker' => $user_location);
		else{
			$args = array('width'=>'100%','height'=>'220px');
			$user_location = 'Undefined - Enter Location below';
		}
		?>
        <h3>HGM Geocoding</h3>
        <table class="form-table">
    		<tr>
                <th><label for="hgm-user-geocoder">User Location</label></th>
                <td><span><?php echo '<strong>Current Location: </strong>' . $user_location; ?><span style="float:right"><input type="checkbox" name="hgm_remove" value="remove"/> Remove on save</span></span><?php hgm_geocoder($args)?></td>
            </tr>
        </table>
    	<?php
	}
	/* Save User Geocode */
	public function save_user_geocode($user_id){
		if(!current_user_can('edit_user',$user_id))	return false;
		if($_POST['hgm_remove'] == 'remove') delete_user_meta($user_id,'hgm_user_geocode');	
		else update_user_meta($user_id,'hgm_user_geocode',$_POST['hgm_user_geocode']);
	}
}//END HgmUserGeocoder


class HgmPostGeocoder{
    public function __construct(){
		$post_types = get_option('hgm_post_types');
		if(!$post_types) return false;
        add_action('add_meta_boxes',array(&$this,'add_hgm_meta_box'));
		add_action('save_post',array(&$this,'save_postdata'),1,2);
    }
	/* Add meta box */
    public function add_hgm_meta_box(){
		$post_types = get_option('hgm_post_types');
		foreach($post_types as $key => $value){
			add_meta_box(
				'hgm_' . $key . '_geocoder',
				'HGM Geocoder',
				array(&$this,'meta_box_content'),
				$key,
				'advanced',
				'high'
			);
		}
    }
	/* Meta box content */
    public function meta_box_content($post){
		$location = get_post_meta($post->ID,'hgm_user_geocode',true);
		if($location) $args = array('width'=>'100%','height'=>'220px','center' => $location,'marker' => $location);
		else{
			$args = array('width'=>'100%','height'=>'220px');
			$location = 'Undefined - Enter Location below';
		}?>
        <span><?php echo '<strong>Current Location: </strong>' . $location; ?><span style="float:right"><input type="checkbox" name="hgm_remove" value="remove"/> Remove on save</span></span><?php hgm_geocoder($args) ?>
        <input type="hidden" name="hgm_noncename" id="hgm_noncename" value="<?php echo wp_create_nonce(plugin_basename(__FILE__)) ?>" />
        <?php
    }
	/* Save Postdata */
	public function save_postdata($post_id){
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if(!wp_verify_nonce($_POST['hgm_noncename'],plugin_basename(__FILE__))) return $post_id;
		if(!current_user_can('edit_post',$post_id)) return $post_id;
		$new_location = $_POST['hgm_user_geocode'];
		if($_POST['hgm_remove'] == 'remove') delete_post_meta($post_id,'hgm_user_geocode');	
		else if($new_location) update_post_meta($post_id,'hgm_user_geocode',$new_location);
	}
}//END HgmPostGeocoder
?>