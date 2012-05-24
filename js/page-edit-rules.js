$(document).ready(function() {
	update_rules_list();


	$('#page-edit-rules-delete-rule').click(function() {
		var rule = $('#page-edit-rules-select-rule').val().split(',');
		
		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}		
		var url = 'http://' + root_url + '/32/' + rule[0];
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {
				alert("Successfully deleted rule");
				update_rules_list();	
			},
			failure: function() {
				alert("Didn't receive a response");
			},
		});		
	});
	
	$('#page-edit-rules-edit-rule').click(function() {
		var rule = $('#page-edit-rules-select-rule').val().split(',');
		
		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}		
		var url = 'http://' + root_url + '/32/' + rule[0];
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {
	
			},
			failure: function() {
			
			},
		});
		
		var type = rule[2];
		
		if(type == 1) { // digital
			populate_pin_mode_select();
			$('#page-create-rules-content-watch-digital-pin').show();
			$('#page-create-rules-content-watch-analog-pin').hide();			
			$('#page-create-rules-content-watch-ds1307').hide();			
			$('#page-create-rules-content-watch-ds18b20').hide();
			
			$('#page-create-rules-content-select-rule-type').val('watch-digital-pin').selectmenu();
			$('#page-create-rules-content-select-rule-type').val('watch-digital-pin').selectmenu('refresh', true);
					
			$('#page-create-rules-content-rule-name').val(rule[16]);
			
			$('#page-create-rules-content-watch-digital-pin-select-pin-input').val(rule[3]).selectmenu();
			$('#page-create-rules-content-watch-digital-pin-select-pin-input').val(rule[3]).selectmenu('refresh', true);			
			
			if(rule[4] == 0) {
				$('#page-create-rules-content-watch-digital-pin-radio-input-on').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-digital-pin-radio-input-on').prop('checked', false).checkboxradio("refresh");
				$('#page-create-rules-content-watch-digital-pin-radio-input-off').prop('checked', true).checkboxradio();
				$('#page-create-rules-content-watch-digital-pin-radio-input-off').prop('checked', true).checkboxradio("refresh");				
			} else {
				$('#page-create-rules-content-watch-digital-pin-radio-input-on').prop('checked', true).checkboxradio();
				$('#page-create-rules-content-watch-digital-pin-radio-input-on').prop('checked', true).checkboxradio("refresh");
				$('#page-create-rules-content-watch-digital-pin-radio-input-off').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-digital-pin-radio-input-off').prop('checked', false).checkboxradio("refresh");
			}
			
			$('#page-create-rules-content-watch-digital-pin-select-pin-output').val(rule[5]).selectmenu();
			$('#page-create-rules-content-watch-digital-pin-select-pin-output').val(rule[5]).selectmenu('refresh', true);
			
			if(rule[6] <= 1) {
				if(rule[6] == 0) {
					$('#page-create-rules-content-watch-digital-pin-radio-output-on').prop('checked', false).checkboxradio();
					$('#page-create-rules-content-watch-digital-pin-radio-output-on').prop('checked', false).checkboxradio("refresh");
					$('#page-create-rules-content-watch-digital-pin-radio-output-off').prop('checked', true).checkboxradio();
					$('#page-create-rules-content-watch-digital-pin-radio-output-off').prop('checked', true).checkboxradio("refresh");
				} else {
					$('#page-create-rules-content-watch-digital-pin-radio-output-on').prop('checked', true).checkboxradio();
					$('#page-create-rules-content-watch-digital-pin-radio-output-on').prop('checked', true).checkboxradio("refresh");
					$('#page-create-rules-content-watch-digital-pin-radio-output-off').prop('checked', false).checkboxradio();
					$('#page-create-rules-content-watch-digital-pin-radio-output-off').prop('checked', false).checkboxradio("refresh");				
				}
			} else {
				//TODO slider throws error when trying to update theme appearance
				$('#page-create-rules-content-watch-digital-pin-slider').val(rule[6]);
				$('#page-create-rules-content-watch-digital-pin-slider').slider("refresh");
			}
			$('#page-create-rules-content-select-pcf8574-device').val(rule[7]).selectmenu();
			$('#page-create-rules-content-select-pcf8574-device').val(rule[7]).selectmenu('refresh', true);
			$('#page-create-rules-content-select-pcf8574-pin').val(rule[8]).selectmenu();
			$('#page-create-rules-content-select-pcf8574-pin').val(rule[8]).selectmenu('refresh', true);			
		} else if(type == 2) { //analog
			populate_analog_pin_dropdowns();
			$('#page-create-rules-content-watch-digital-pin').hide();
			$('#page-create-rules-content-watch-analog-pin').show();			
			$('#page-create-rules-content-watch-ds1307').hide();			
			$('#page-create-rules-content-watch-ds18b20').hide();		
			$('#page-create-rules-content-select-rule-type').val('watch-analog-pin');

			$('#page-create-rules-content-select-rule-type').selectmenu();
			$('#page-create-rules-content-select-rule-type').selectmenu('refresh', true);
	
			$('#page-create-rules-content-rule-name').val(rule[16]);
			
			$('#page-create-rules-content-watch-analog-pin-select-pin-input').val(rule[2]).selectmenu();
			$('#page-create-rules-content-watch-analog-pin-select-pin-input').val(rule[2]).selectmenu('refresh', true);			
			
			if(rule[3] == 1) {
				$('#page-create-rules-content-watch-analog-pin-radio-input-less-than').prop('checked', true).checkboxradio();
				$('#page-create-rules-content-watch-analog-pin-radio-input-less-than').prop('checked', true).checkboxradio("refresh");
				$('#page-create-rules-content-watch-analog-pin-radio-input-greater-than').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-analog-pin-radio-input-greater-than').prop('checked', false).checkboxradio("refresh");				
				$('#page-create-rules-content-watch-analog-pin-radio-input-equal-to').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-analog-pin-radio-input-equal-to').prop('checked', false).checkboxradio("refresh");
			} else if(rule[3] == 2) {
				$('#page-create-rules-content-watch-analog-pin-radio-input-less-than').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-analog-pin-radio-input-less-than').prop('checked', false).checkboxradio("refresh");
				$('#page-create-rules-content-watch-analog-pin-radio-input-greater-than').prop('checked', true).checkboxradio();
				$('#page-create-rules-content-watch-analog-pin-radio-input-greater-than').prop('checked', true).checkboxradio("refresh");				
				$('#page-create-rules-content-watch-analog-pin-radio-input-equal-to').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-analog-pin-radio-input-equal-to').prop('checked', false).checkboxradio("refresh");
			} else if(rule[3] == 3) {
				$('#page-create-rules-content-watch-analog-pin-radio-input-less-than').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-analog-pin-radio-input-less-than').prop('checked', false).checkboxradio("refresh");
				$('#page-create-rules-content-watch-analog-pin-radio-input-greater-than').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-analog-pin-radio-input-greater-than').prop('checked', false).checkboxradio("refresh");				
				$('#page-create-rules-content-watch-analog-pin-radio-input-equal-to').prop('checked', true).checkboxradio();
				$('#page-create-rules-content-watch-analog-pin-radio-input-equal-to').prop('checked', true).checkboxradio("refresh");			
			}
			
			$('#page-create-rules-content-watch-analog-pin-input-value').val(rule[4]);
			
			$('#page-create-rules-content-watch-analog-pin-select-pin-output').val(rule[5]).selectmenu();
			$('#page-create-rules-content-watch-analog-pin-select-pin-output').val(rule[5]).selectmenu('refresh', true);			
			
			if(rule[6] <= 1) {
				if(rule[6] == 0) {
					$('#page-create-rules-content-watch-analog-pin-radio-output-on').prop('checked', false).checkboxradio();
					$('#page-create-rules-content-watch-analog-pin-radio-output-on').prop('checked', false).checkboxradio("refresh");
					$('#page-create-rules-content-watch-analog-pin-radio-output-off').prop('checked', true).checkboxradio();
					$('#page-create-rules-content-watch-analog-pin-radio-output-off').prop('checked', true).checkboxradio("refresh");
				} else {
					$('#page-create-rules-content-watch-analog-pin-radio-output-on').prop('checked', true).checkboxradio();
					$('#page-create-rules-content-watch-analog-pin-radio-output-on').prop('checked', true).checkboxradio("refresh");
					$('#page-create-rules-content-watch-analog-pin-radio-output-off').prop('checked', false).checkboxradio();
					$('#page-create-rules-content-watch-analog-pin-radio-output-off').prop('checked', false).checkboxradio("refresh");				
				}
			} else {
				//TODO slider throws error when trying to update theme appearance
				$('#page-create-rules-content-watch-analog-pin-slider').val(rule[6]);
				$('#page-create-rules-content-watch-analog-pin-slider').slider("refresh");
			}			

			$('#page-create-rules-content-select-pcf8574-device').val(rule[7]).selectmenu();
			$('#page-create-rules-content-select-pcf8574-device').val(rule[7]).selectmenu('refresh', true);
			$('#page-create-rules-content-select-pcf8574-pin').val(rule[8]).selectmenu();
			$('#page-create-rules-content-select-pcf8574-pin').val(rule[8]).selectmenu('refresh', true);			
		} else if(type == 3) { //time
			populate_ds1307_dropdowns();
			$('#page-create-rules-content-watch-digital-pin').hide();
			$('#page-create-rules-content-watch-analog-pin').hide();			
			$('#page-create-rules-content-watch-ds1307').show();			
			$('#page-create-rules-content-watch-ds18b20').hide();
			$('#page-create-rules-content-select-rule-type').val('watch-ds1307');		
			
			$('#page-create-rules-content-select-rule-type').selectmenu();			
			$('#page-create-rules-content-select-rule-type').selectmenu('refresh', true);
			
			$('#page-create-rules-content-rule-name').val(rule[16]);
			
			$('#page-create-rules-content-watch-ds1307-select-start-hour').val(rule[3]).selectmenu();			
			$('#page-create-rules-content-watch-ds1307-select-start-hour').selectmenu('refresh', true);

			$('#page-create-rules-content-watch-ds1307-select-start-minute').val(rule[4]).selectmenu();			
			$('#page-create-rules-content-watch-ds1307-select-start-minute').selectmenu('refresh', true);
			
			$('#page-create-rules-content-watch-ds1307-select-stop-hour').val(rule[5]).selectmenu();			
			$('#page-create-rules-content-watch-ds1307-select-stop-hour').selectmenu('refresh', true);
			
			$('#page-create-rules-content-watch-ds1307-select-stop-minute').val(rule[6]).selectmenu();			
			$('#page-create-rules-content-watch-ds1307-select-stop-minute').selectmenu('refresh', true);
			
			$('#page-create-rules-content-watch-ds1307-select-dow').val(rule[7]).selectmenu();			
			$('#page-create-rules-content-watch-ds1307-select-dow').selectmenu('refresh', true);
			
			$('#page-create-rules-content-watch-ds1307-select-fade-time').val(rule[10]).selectmenu();			
			$('#page-create-rules-content-watch-ds1307-select-fade-time').selectmenu('refresh', true);
			
			$('#page-create-rules-content-watch-ds1307-select-pin-output').val(rule[8]).selectmenu();			
			$('#page-create-rules-content-watch-ds1307-select-pin-output').selectmenu('refresh', true);																		
			if(rule[9] <= 1) {
				if(rule[9] == 0) {
					$('#page-create-rules-content-watch-ds1307-radio-output-on').prop('checked', false).checkboxradio();
					$('#page-create-rules-content-watch-ds1307-radio-output-on').prop('checked', false).checkboxradio("refresh");
					$('#page-create-rules-content-watch-ds1307-radio-output-off').prop('checked', true).checkboxradio();
					$('#page-create-rules-content-watch-ds1307-radio-output-off').prop('checked', true).checkboxradio("refresh");
				} else {
					$('#page-create-rules-content-watch-ds1307-radio-output-on').prop('checked', true).checkboxradio();
					$('#page-create-rules-content-watch-ds1307-radio-output-on').prop('checked', true).checkboxradio("refresh");
					$('#page-create-rules-content-watch-ds1307-radio-output-off').prop('checked', false).checkboxradio();
					$('#page-create-rules-content-watch-ds1307-radio-output-off').prop('checked', false).checkboxradio("refresh");				
				}
			} else {
				//TODO slider throws error when trying to update theme appearance
				$('#page-create-rules-content-watch-ds1307-slider').val(rule[9]);
				$('#page-create-rules-content-watch-ds1307-slider').slider("refresh");
			}			
			
			
			$('#page-create-rules-content-select-pcf8574-device').val(rule[11]).selectmenu();
			$('#page-create-rules-content-select-pcf8574-device').val(rule[11]).selectmenu('refresh', true);
			$('#page-create-rules-content-select-pcf8574-pin').val(rule[12]).selectmenu();
			$('#page-create-rules-content-select-pcf8574-pin').val(rule[12]).selectmenu('refresh', true);					
		} else if(type == 4) { //temp
			populate_onewire_device_dropdowns();
			$('#page-create-rules-content-watch-digital-pin').hide();
			$('#page-create-rules-content-watch-analog-pin').hide();			
			$('#page-create-rules-content-watch-ds1307').hide();			
			$('#page-create-rules-content-watch-ds18b20').show();
			$('#page-create-rules-content-select-rule-type').val('watch-ds18b20');
			
			$('#page-create-rules-content-select-rule-type').selectmenu();
			$('#page-create-rules-content-select-rule-type').selectmenu('refresh', true);

			$('#page-create-rules-content-rule-name').val(rule[16]);		

			$('#page-create-rules-content-watch-ds18b20-select-sensor').val(rule[3]).selectmenu();
			$('#page-create-rules-content-watch-ds18b20-select-sensor').selectmenu('refresh', true);	
			
			if(rule[4] == 1) {
				$('#page-create-rules-content-watch-ds18b20-radio-input-less-than').prop('checked', true).checkboxradio();
				$('#page-create-rules-content-watch-ds18b20-radio-input-less-than').prop('checked', true).checkboxradio("refresh");
				$('#page-create-rules-content-watch-ds18b20-radio-input-greater-than').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-ds18b20-radio-input-greater-than').prop('checked', false).checkboxradio("refresh");				
				$('#page-create-rules-content-watch-ds18b20-radio-input-equal-to').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-ds18b20-radio-input-equal-to').prop('checked', false).checkboxradio("refresh");
			} else if(rule[4] == 2) {
				$('#page-create-rules-content-watch-ds18b20-radio-input-less-than').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-ds18b20-radio-input-less-than').prop('checked', false).checkboxradio("refresh");
				$('#page-create-rules-content-watch-ds18b20-radio-input-greater-than').prop('checked', true).checkboxradio();
				$('#page-create-rules-content-watch-ds18b20-radio-input-greater-than').prop('checked', true).checkboxradio("refresh");				
				$('#page-create-rules-content-watch-ds18b20-radio-input-equal-to').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-ds18b20-radio-input-equal-to').prop('checked', false).checkboxradio("refresh");
			} else if(rule[4] == 3) {
				$('#page-create-rules-content-watch-ds18b20-radio-input-less-than').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-ds18b20-radio-input-less-than').prop('checked', false).checkboxradio("refresh");
				$('#page-create-rules-content-watch-ds18b20-radio-input-greater-than').prop('checked', false).checkboxradio();
				$('#page-create-rules-content-watch-ds18b20-radio-input-greater-than').prop('checked', false).checkboxradio("refresh");				
				$('#page-create-rules-content-watch-ds18b20-radio-input-equal-to').prop('checked', true).checkboxradio();
				$('#page-create-rules-content-watch-ds18b20-radio-input-equal-to').prop('checked', true).checkboxradio("refresh");
			}
			
			var temp_value = rule[5] + '.' + rule[6];
			
			$('#page-create-rules-content-watch-ds18b20-input-value').val(temp_value);
			
			$('#page-create-rules-content-watch-ds18b20-select-pin-output').val(rule[7]).selectmenu();
			$('#page-create-rules-content-watch-ds18b20-select-pin-output').selectmenu('refresh', true);
			
			if(rule[8] <= 1) {
				if(rule[8] == 0) {
					$('#page-create-rules-content-watch-ds18b20-radio-output-on').prop('checked', false).checkboxradio();
					$('#page-create-rules-content-watch-ds18b20-radio-output-on').prop('checked', false).checkboxradio("refresh");
					$('#page-create-rules-content-watch-ds18b20-radio-output-off').prop('checked', true).checkboxradio();
					$('#page-create-rules-content-watch-ds18b20-radio-output-off').prop('checked', true).checkboxradio("refresh");
				} else {
					$('#page-create-rules-content-watch-ds18b20-radio-output-on').prop('checked', true).checkboxradio();
					$('#page-create-rules-content-watch-ds18b20-radio-output-on').prop('checked', true).checkboxradio("refresh");
					$('#page-create-rules-content-watch-ds18b20-radio-output-off').prop('checked', false).checkboxradio();
					$('#page-create-rules-content-watch-ds18b20-radio-output-off').prop('checked', false).checkboxradio("refresh");
				}
			} else {
				//TODO slider throws error when trying to update theme appearance
				$('#page-create-rules-content-watch-ds18b20-slider').val(rule[8]);
				$('#page-create-rules-content-watch-ds18b20-slider').slider('disable');				
				$('#page-create-rules-content-watch-ds18b20-slider').slider('refresh');
			}						
			
			$('#page-create-rules-content-select-pcf8574-device').val(rule[9]).selectmenu();
			$('#page-create-rules-content-select-pcf8574-device').val(rule[9]).selectmenu('refresh', true);
			$('#page-create-rules-content-select-pcf8574-pin').val(rule[10]).selectmenu();
			$('#page-create-rules-content-select-pcf8574-pin').val(rule[10]).selectmenu('refresh', true);			
		}
		update_rules_list();
	});


});

function update_rules_list() {
	
	var select = $('#page-edit-rules-select-rule');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	//todo update for arduino mega support
	for(var i=0; i<=19; i++) {
		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}	
		var url = 'http://' + root_url + '/31/' + i;
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {
				var rule = data.RULE.split(",");
				if(rule[1] == 1) {
					output = '<option value="' + data.RULE + '">' + rule[16] + '</option>';
					$('#page-edit-rules-select-rule').append(output);
				}
			},
			failure: function() {
			
			},
		});	
	}
}