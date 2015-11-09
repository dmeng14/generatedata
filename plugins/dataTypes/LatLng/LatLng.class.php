<?php

/**
 * @package DataTypes
 */

class DataType_LatLng extends DataTypePlugin {
	protected $isEnabled = true;
	protected $dataTypeName = "Latitude / Longitude";
	protected $dataTypeFieldGroup = "geo";
	protected $dataTypeFieldGroupOrder = 100;
	protected $jsModules = array("LatLng.js");

	// $this->cachedMath = array();
	private $helpDialogWidth = 410;

	/**
	 * Valid ranges:
	 *   Lat: -90 -> + 90
	 *   Lng: -180 -> +180
	 */
	public function generate($generator, $generationContextData) {
		$options = $generationContextData["generationOptions"];
		$lat = $options["cir_lat"];
		$lng = $options["cir_lng"];
		$circum = $options["cir_dist"];
		
		$latBase = $options["lat_base"];
		$latDist = $options["lat_dist"];
		$lngDist = $options["lng_dist"];
		$latoffset = $this->latDistToDegree($latDist);
		
		$info = array();
		if ($options["lat"] && $options["lng"]) {
			$lngoffset = $this->lngDistToDegree($latBase, $lngDist);
			if ($options["latlngType"] == "MAX"){
				$info[] = $this->random_low_high($latBase - $latoffset, $latBase + $latoffset);
				$info[] = $this->random_low_high($options["lng_base"] - $lngoffset, $options["lng_dist"] + $lngoffset);
			}
			else if($options["latlngType"] == "STD"){
				$info[] = $this->gauss_ms($latBase, $latoffset);
				$info[] = $this->gauss_ms($options["lng_base"], $lngoffset);
			}
		} else if ($options["lat"]) {
			if ($options["latlngType"] == "MAX"){
				$info[] = $this->random_low_high($latBase - $latoffset, $latBase + $latoffset);
			}
			else if($options["latlngType"] == "STD"){
				$info[] = $this->gauss_ms($latBase, $latoffset);
			}
		} else if ($options["lng"]) {
			$latBase = mt_rand(0, 90);
			$lngoffset = $this->lngDistToDegree($latBase, $lngDist);
			if ($options["latlngType"] == "MAX"){
				$info[] = $this->random_low_high($options["lng_base"] - $lngoffset, $options["lng_dist"] + $lngoffset);
			}
			else if($options["latlngType"] == "STD"){
				$info[] = $this->gauss_ms($options["lng_base"], $lngoffset);
			}
		} else if ($options["cir"]) {
			$latlngpoint = $this->cirLatLng($lat,$lng,$circum);
			$info[] = $latlngpoint["lat"];
			$info[] = $latlngpoint["lng"];
		}

		return array(
			"display" => join(", ", $info)
		);
	}

	public function getRowGenerationOptionsUI($generator, $postdata, $column, $numCols) {
		if (!isset($postdata["dtLatLng_Lat$column"]) && empty($postdata["dtLatLng_Lng$column"]) && empty($postdata["dtLatLng_cir_$column"])) {
			return false;
		}
		$options = array(
			"lat" => isset($postdata["dtLatLng_Lat$column"]) ? true : false,
			"lng" => isset($postdata["dtLatLng_Lng$column"]) ? true : false,
			"cir" => isset($postdata["dtLatLng_cir_$column"]) ? true : false,
			"cir_lat" => $postdata["dtLat_center_$column"],
			"cir_lng" => $postdata["dtLng_center_$column"],
			"cir_dist" => $postdata["dtCir_dist_$column"],
			"lat_base" => $postdata["dtLat_base_$column"],
			"lat_dist" => $postdata["dtLat_dist_$column"],
			"lng_base" => $postdata["dtLng_base_$column"],
			"lng_dist" => $postdata["dtLng_dist_$column"],
			"latlngType" => $postdata["dtLatLng_type_$column"] 
		);
		return $options;
	}

	public function getRowGenerationOptionsAPI($generator, $json, $numCols) {
		//$latlngType = $json->settings->latlngType; // MAX or STD
		$options = array(
			"lat" => $json->settings->lat,
			"lng" => $json->settings->lng,
		//	"lat_base" => $json->settings->latBase,
			//"lat_dist" => $json->settings->latDist,
			//"lng_base" => $json->settings->lngBase,
			//"lng_dist" => $json->settings->lngDist,
			//"latlngType" => ucfirst($latlngType)
		);
		return $options;
	}

	public function getOptionsColumnHTML() {
		$html =<<< EOF
<table cellspacing="0" cellpadding="0" width="260">		
	<tr>
		<td><input type="checkbox" name="dtLatLng_Lat%ROW%" id="dtLatLng_Lat%ROW%" checked="checked" />
		<label for="dtLatLng_Lat%ROW%">{$this->L["latitude"]}</label></td>
		
		<td><label for="dtLat_base_%ROW%">{$this->L["latbase"]}</label>
		<input type="text" size="6" name="dtLat_base_%ROW%" id="dtLat_base_%ROW%" value="" /></td>
		
		<td><label for="dtLat_dist_%ROW%">{$this->L["latdist"]}</label>
		<input type="text" size="2" name="dtLat_dist_%ROW%" id="dtLat_dist_%ROW%" value="" /></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="dtLatLng_Lng%ROW%" id="dtLatLng_Lng%ROW%" checked="checked" />
		<label for="dtLatLng_Lng%ROW%">{$this->L["longitude"]}</label></td>
		
		<td><label for="dtLng_base_%ROW%">{$this->L["lngbase"]}</label>
		<input type="text" size="6" name="dtLng_base_%ROW%" id="dtLng_base_%ROW%" value="" /></td>
		
		<td><label for="dtLng_dist_%ROW%">{$this->L["lngdist"]}</label>
		<input type="text" size="2" name="dtLng_dist_%ROW%" id="dtLng_dist_%ROW%" value="" /></td>
	</tr>
	<tr>
		<td colspan="2"><label for="dtLatLng_max_%ROW%">{$this->L["latlngmax"]}</label>
		<input type="radio" name="dtLatLng_type_%ROW%" id="dtLatLng_max_%ROW%" value="MAX" checked="checked" />
		
		<label for="dtLatLng_std_%ROW%">{$this->L["latlngstd"]}</label>
		<input type="radio" name="dtLatLng_type_%ROW%" id="dtLatLng_std_%ROW%" value="STD" /></td>
	</tr>	
	<tr>
		<td colspan="2"><input type="checkbox" name="dtLatLng_cir_%ROW%" id="dtLatLng_cir_%ROW%" checked="checked" />
		<label for="dtLatLng_cir_%ROW%">{$this->L["circular"]}</label>&nbsp;&nbsp;
		<input type="text" size="4" name="dtCir_dist_%ROW%" id="dtCir_dist_%ROW%" value="" /></td>

	</tr>
	<tr>
		<td colspan="3"><label for="dtLatLng_center_%ROW%">{$this->L["cir_center"]}</label>&nbsp;
		<label for="dtLat_center_%ROW%">{$this->L["lat"]}</label>&nbsp;
		<input type="text" size="6" name="dtLat_center_%ROW%" id="dtLat_center_%ROW%" value="" />
		&nbsp;&nbsp;
		<label for="dtLng_center_%ROW%">{$this->L["lng"]}</label>&nbsp;
		<input type="text" size="6" name="dtLng_center_%ROW%" id="dtLng_center_%ROW%" value="" /></td>
	</tr>
</table>	
EOF;
		return $html;
	}


	public function getHelpHTML() {
		return "<p>{$this->L["help"]}</p>";
	}

	public function getDataTypeMetadata() {
		return array(
			"SQLField" => "varchar(30) default NULL",
			"SQLField_Oracle" => "varchar2(30) default NULL",
			"SQLField_MSSQL" => "VARCHAR(30) NULL"
		);
	}
	
	public function latDistToDegree($latDist){
		return $latDist / 69.172;
	}
	public function lngDistToDegree($latBase, $lngDist){
		$lngoffset = $lngDist / (69.172 * $this->lngCos($latBase));
		return $lngoffset;
	}
	public function lngCos($latBase){
		$latBase = (int)($latBase / 2) * 2;
		$cosTable = array(
			0 => 1.000,
			2 => 0.999,
			4 => 0.998,
			6 => 0.995,
			8 => 0.990,
			10 => 0.985,
			12 => 0.978,
			14 => 0.970,
			16 => 0.961,
			18 => 0.951,
			20 => 0.940,
			22 => 0.927,
			24 => 0.914,
			26 => 0.899,
			28 => 0.883,
			30 => 0.866,
			32 => 0.848,
			34 => 0.829,
			36 => 0.809,
			38 => 0.788,
			40 => 0.766,
			42 => 0.743,
			44 => 0.719,
			46 => 0.695,
			48 => 0.669,
			50 => 0.643,
			52 => 0.616,
			54 => 0.588,
			56 => 0.559,
			58 => 0.530,
			60 => 0.500,
			62 => 0.469,
			64 => 0.438,
			66 => 0.407,
			68 => 0.375,
			70 => 0.342,
			72 => 0.309,
			74 => 0.276,
			76 => 0.242,
			78 => 0.208,
			80 => 0.174,
			82 => 0.139,
			84 => 0.105,
			86 => 0.070,
			88 => 0.070,
			90 => 0.000
		);
		// round up to even
		return $cosTable[$latBase];
	}
	public function gauss() { 
	// N(0,1)
	// returns random number with normal distribution:
	// mean=0
	// std dev=1

	// auxiliary vars
	$x=$this->random_0_1();
	$y=$this->random_0_1();
	// two independent variables with normal distribution N(0,1)
	$u=sqrt(-2*log($x))*cos(2*pi()*$y);
	$v=sqrt(-2*log($x))*sin(2*pi()*$y);
	// i will return only one, couse only one needed
	return $u;
	}
	public function gauss_ms($m=0.0,$s=1.0) { 
	// N(m,s)
	// returns random number with normal distribution:
	// mean=m
	// std dev=s
	return $this->gauss()*$s+$m;
	}
	public function random_0_1() {
	// auxiliary function
	// returns random number with flat distribution from 0 to 1
	return (float)rand()/(float)getrandmax();
	}
	public function random_low_high($low, $high) {
	return $low + (float)rand()/(float)(getrandmax() / ($high - $low));
	}
	
	public function cirLatLng($lat,$lng,$circum){
		// earth radius is 6371 km
		$R = 6371;
		$rad =  $circum / (2 * 3.14);
		$minlat = $lat - rad2deg($rad/$R);
		$maxlat = $lat + rad2deg($rad/$R);
		$minlng = $lng - rad2deg($rad/$R/cos(deg2rad($lat)));
		$maxlng = $lng + rad2deg($rad/$R/cos(deg2rad($lat)));
		do {
			$randlat = $this->random_low_high($minlat, $maxlat);
			$randlng = $this->random_low_high($minlng, $maxlng);
		} while (acos( sin($lat)*sin($randlat) + cos($lat)*cos($randlat)*cos($lng-$randlng) ) * $R > $rad);
		$result = array("lat"=>$randlat, "lng"=>$randlng);
		return $result;
	}

}
