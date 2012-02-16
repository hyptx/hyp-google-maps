<?php /* The Map Classes */

class HgmApiLoader{
	private $var;
	public function __construct(){ $this->load_api(); }
	/* Load Api */
	private function load_api(){?>
		<script type="text/javascript">
        function hgmLoadApi(){
            var script = document.createElement("script");
            script.type = "text/javascript";
            script.src = "<?php echo $this->is_ssl() ?>maps.googleapis.com/maps/api/js?key=<?php echo get_option('hgm_api_key') ?>&sensor=false&callback=hgmInitialize";
            document.body.appendChild(script);
        }
        window.onload = hgmLoadApi;
        function hgmInitialize(){ <?php $this->print_map_js() ?> }
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
	public function __construct($args){
		self::$instance += 1;
		$this->element_id = 'hgm-map-canvas-' . self::$instance;
		$this->instance_num = self::$instance;
		$this->save_map_data($args);
	}
	/* Save Map Data */
	private function save_map_data($args){
		$defaults = array(
			'width' => '400px',
			'height' => '400px',
			'center' =>'42.284821,-72.837902',
			'zoom' => 10,
		);
		$map_data_array = wp_parse_args($args,$defaults);
		foreach($map_data_array as $key => $value) $this->{$key} = $value;
		$this->print_canvas();
	}	
	/* Print Canvas */
	private function print_canvas(){
		echo '<div id="' . $this->element_id . '" class="hgm-map" style="width:' . $this->width . ';height:' . $this->height . ';"></div>';
	}
	/* Get Element Id */
	public function get_element_id(){ return $this->element_id; }	
	/* Get Instance Number */
	public function get_instance_num(){ return $this->instance_num; }	
}//END HgmMap
?>