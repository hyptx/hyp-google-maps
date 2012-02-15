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
            script.src = "<?php echo $this->hgm_ssl() ?>maps.googleapis.com/maps/api/js?key=<?php echo get_option('hgm_api_key') ?>&sensor=false&callback=hgmInitialize";
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
			$map_js .= "
			var hgm{$instance_num}Options = {
				zoom: 10,
				center: new google.maps.LatLng(40.411928,-105.07267),
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map{$instance_num} = new google.maps.Map(document.getElementById('{$element_id}'),hgm{$instance_num}Options);
			";
		}
		echo $map_js;
	}
	/* SSL Check */
	private function hgm_ssl(){
		if($_SERVER['HTTPS'] == 'on') return 'https://';
		else return 'http://';
	}
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
			'height' => '400px'
		);
		$map_data_array = wp_parse_args($args,$defaults);
		foreach($map_data_array as $key => $value) $this->{$key} = $value;
		$this->print_canvas();
	}	
	/* Print Canvas */
	private function print_canvas(){
		echo '<div id="' . $this->element_id . '" style="width:' . $this->width . ';height:' . $this->height . ';"></div>';
	}
	/* Get Element Id */
	public function get_element_id(){ return $this->element_id; }	
	/* Get Instance Number */
	public function get_instance_num(){ return $this->instance_num; }	
}//END HgmMap
?>