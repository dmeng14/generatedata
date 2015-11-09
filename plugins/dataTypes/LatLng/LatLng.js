/*global $:false*/
define([
	"manager",
	"constants",
	"lang",
	"generator"
], function(manager, C, L, generator) {

	"use strict";

	/**
	* @name LatLng
	* @description JS code for the LatLng Data Type.
	* @see DataType
	* @namespace
	*/

	var MODULE_ID = "data-type-LatLng";
	var LANG = L.dataTypePlugins.LatLng;

	var _loadRow = function(rowNum, data) {
		return {
			execute: function() {
				$("#dtLat_base_" + rowNum).val(data.lat_base);
				$("#dtLat_dist_" + rowNum).val(data.lat_dist);
				$("#dtLng_base_" + rowNum).val(data.lng_base);
				$("#dtLng_dist_" + rowNum).val(data.lng_dist);
				$("#dtLatLng_max_" + rowNum).attr("checked", data.latlng_max);
				$("#dtLatLng_std_" + rowNum).attr("checked", data.latlng_std);
				$("#dtLat_center_" + rowNum).val(data.lat_cir);
				$("#dtLng_center_" + rowNum).val(data.lng_cir);
				$("#dtCir_dist_" + rowNum).val(data.dist_cir);
			},
			isComplete: function() {
				if ($("#dtLatLng_Lng" + rowNum).length) {
					
					if (data.lat) {
						$("#dtLatLng_Lat" + rowNum).attr("checked", "checked");
					} else {
						$("#dtLatLng_Lat" + rowNum).removeAttr("checked");
					}
					
					if (data.lng) {
						$("#dtLatLng_Lng" + rowNum).attr("checked", "checked");
					} else {
						$("#dtLatLng_Lng" + rowNum).removeAttr("checked");
					}
					
					if (data.cir){
						$("#dtLatLng_cir_" + rowNum).attr("checked", "checked");
					} else {
						$("#dtLatLng_cir_" + rowNum).removeAttr("checked");
					}
					
					return true;
				} else {
					return false;
				}
			}
		};
	};

	var _saveRow = function(rowNum) {
		return {
			"lat": ($("#dtLatLng_Lat" + rowNum).attr("checked")) ? "checked" : "",
			"lng": ($("#dtLatLng_Lng" + rowNum).attr("checked")) ? "checked" : "",
			"cir": ($("#dtLatLng_cir_" + rowNum).attr("checked")) ? "checked" : "",
			"lat_cir":   $("#dtLat_center_" + rowNum).val(),
			"lng_cir":   $("#dtLng_center_" + rowNum).val(),
			"dist_cir":   $("#dtCir_dist_" + rowNum).val(),
			"lat_base":   $("#dtLat_base_" + rowNum).val(),
			"lat_dist":   $("#dtLat_dist_" + rowNum).val(),
			"lng_base":   $("#dtLng_base_" + rowNum).val(),
			"lng_dist":   $("#dtLng_dist_" + rowNum).val(),
			"latlng_max": $("#dtLatLng_max_" + rowNum).attr("checked"),
			"latlng_std": $("#dtLatLng_std_" + rowNum).attr("checked")
		};
	};

	var _validate = function(rows) {
		var visibleProblemRows = [];
		var problemFields      = [];

		var numOnly = /^[+-]?\d+(\.\d+)?$/;
		for (var i=0; i<rows.length; i++) {
			var visibleRowNum = generator.getVisibleRowOrderByRowNum(rows[i]);
			var hasError = false;
			var lat = $.trim($("#dtLatLng_Lat" + rows[i]).attr("checked"));
			var lng = $.trim($("#dtLatLng_Lng" + rows[i]).attr("checked"));
			var cir = $.trim($("#dtLatLng_cir_" + rows[i]).attr("checked"));
			
			if(lat != "") {
				var lat_base = $.trim($("#dtLat_base_" + rows[i]).val());
				if (lat_base === "" || !numOnly.test(lat_base)) {
					hasError = true;
					problemFields.push($("#dtLat_base_" + rows[i]));
				}
				var lat_dist = $.trim($("#dtLat_dist_" + rows[i]).val());
				if (lat_dist === "" || !numOnly.test(lat_dist)) {
					hasError = true;
					problemFields.push($("#dtLat_dist_" + rows[i]));
				}
			}

			if(lng != "") {
				var lng_base = $.trim($("#dtLng_base_" + rows[i]).val());
				if (lng_base === "" || !numOnly.test(lng_base)) {
					hasError = true;
					problemFields.push($("#dtLng_base_" + rows[i]));
				}
				var lng_dist = $.trim($("#dtLng_dist_" + rows[i]).val());
				if (lng_dist === "" || !numOnly.test(lng_dist)) {
					hasError = true;
					problemFields.push($("#dtLng_dist_" + rows[i]));
				}
			}
			
			if(cir != "") {
				var lat_cir = $.trim($("#dtLat_center_" + rows[i]).val());
				if (lat_cir === "" || !numOnly.test(lat_cir)) {
					hasError = true;
					problemFields.push($("#dtLat_center_" + rows[i]));
				}
				var lng_cir = $.trim($("#dtLng_center_" + rows[i]).val());
				if (lng_cir === "" || !numOnly.test(lng_cir)) {
					hasError = true;
					problemFields.push($("#dtLng_center_" + rows[i]));
				}
				var cir_dist = $.trim($("#dtCir_dist_" + rows[i]).val());
				if (cir_dist === "" || !numOnly.test(cir_dist)) {
					hasError = true;
					problemFields.push($("#dtCir_dist_" + rows[i]));
				}
			}

			if (hasError) {
				visibleProblemRows.push(visibleRowNum);
			}
		}

		var errors = [];
		if (visibleProblemRows.length) {
			errors.push({ els: problemFields, error: LANG.incomplete_fields + " <b>" + visibleProblemRows.join(", ") + "</b>"});
		}
		return errors;
	};
		
	// register our module
	manager.registerDataType(MODULE_ID, {
		validate: _validate,
		loadRow: _loadRow,
		saveRow: _saveRow
	});

});