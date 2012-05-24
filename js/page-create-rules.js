$(document).ready(function() {
	//run stuff on DOM load
	
	//show the initial digital pin macro set
	$('#page-create-rules-content-watch-digital-pin').show();
	
	$('#page-create-rules-content-select-rule-type').change(function() {
		var type = $('#page-create-rules-content-select-rule-type').val();
		
		if(type == 'watch-digital-pin') {
			$('#page-create-rules-content-watch-digital-pin').show();
			$('#page-create-rules-content-watch-analog-pin').hide();			
			$('#page-create-rules-content-watch-ds1307').hide();			
			$('#page-create-rules-content-watch-ds18b20').hide();			
		} else if(type == 'watch-analog-pin'){
			populate_analog_pin_dropdowns();
			$('#page-create-rules-content-watch-digital-pin').hide();
			$('#page-create-rules-content-watch-analog-pin').show();			
			$('#page-create-rules-content-watch-ds1307').hide();						
			$('#page-create-rules-content-watch-ds18b20').hide();						
		} else if(type == 'watch-ds1307') {
			populate_ds1307_dropdowns();
			$('#page-create-rules-content-watch-ds1307').show();
			$('#page-create-rules-content-watch-digital-pin').hide();
			$('#page-create-rules-content-watch-analog-pin').hide();			
			$('#page-create-rules-content-watch-ds18b20').hide();			
		} else if(type == 'watch-ds18b20') {
			populate_onewire_device_dropdowns();
			$('#page-create-rules-content-watch-ds18b20').show();
			$('#page-create-rules-content-watch-ds1307').hide();
			$('#page-create-rules-content-watch-digital-pin').hide();
			$('#page-create-rules-content-watch-analog-pin').hide();					
		}
	});
	
	
	//create rule button click
	$('#page-create-rules-content-button-create-rule').click(function() {
		var type = $('#page-create-rules-content-select-rule-type').val();
		
		if(type == 'watch-digital-pin') {
			var rule_name = $('#page-create-rules-content-rule-name').val();
			var input_pin = $('#page-create-rules-content-watch-digital-pin-select-pin-input').val();
			var input_pin_action = $('input:radio[name=page-create-rules-content-watch-digital-pin-radio-input]:checked').val();
			var output_pin = $('#page-create-rules-content-watch-digital-pin-select-pin-output').val();
			var output_pin_action = $('input:radio[name=page-create-rules-content-watch-digital-pin-radio-output]:checked').val(); 
			var pcf8574_device = $('#page-create-rules-content-select-pcf8574-device').val();
			var pcf8574_pin = $('#page-create-rules-content-select-pcf8574-pin').val();
			var pwm_value = $('#page-create-rules-content-watch-digital-pin-slider').val();
			
			if(pwm_value > 0 && output_pin != 254) {
				output_pin_action = pwm_value;
			}
			
			if(localStorage.currentDeviceUseRemoteURL == 0) {
				var root_url = localStorage.currentDeviceIP;
			} else {
				var root_url = localStorage.currentRemoteAccessURL;
			}			
			var url = 'http://' + root_url + '/5/' + rule_name + '/1/' + input_pin + '/' + input_pin_action + '/' + output_pin + '/' + output_pin_action + '/' + pcf8574_device + '/' + pcf8574_pin;
			$.ajax({
				dataType: "jsonp",
				url: url, 
				success: function(data) {
					alert("Successfully created new rule");	
				},
				failure: function() {
					alert("Didn't receive a response");
				},
			});
			
		} else if(type == 'watch-analog-pin') {
			var rule_name = $('#page-create-rules-content-rule-name').val();
			var input_pin = $('#page-create-rules-content-watch-analog-pin-select-pin-input').val();
			var input_pin_action = $('input:radio[name=page-create-rules-content-watch-analog-pin-radio-input]:checked').val();
			var input_pin_value = $('#page-create-rules-content-watch-analog-pin-input-value').val();			
			var output_pin = $('#page-create-rules-content-watch-analog-pin-select-pin-output').val();
			var output_pin_action = $('input:radio[name=page-create-rules-content-watch-analog-pin-radio-output]:checked').val(); 
			var pcf8574_device = $('#page-create-rules-content-select-pcf8574-device').val();
			var pcf8574_pin = $('#page-create-rules-content-select-pcf8574-pin').val();
			var pwm_value = $('#page-create-rules-content-watch-analog-pin-slider').val();
			
			if(pwm_value > 0 && output_pin != 254) {
				output_pin_action = pwm_value;
			}
			
			if(localStorage.currentDeviceUseRemoteURL == 0) {
				var root_url = localStorage.currentDeviceIP;
			} else {
				var root_url = localStorage.currentRemoteAccessURL;
			}			
			var url = 'http://' + root_url + '/5/' + rule_name + '/2/' + input_pin + '/' + input_pin_value + '/' + input_pin_action + '/' + output_pin + '/' + output_pin_action + '/' + pcf8574_device + '/' + pcf8574_pin;
			$.ajax({
				dataType: "jsonp",
				url: url, 
				success: function(data) {
					alert("Successfully created new rule");	
				},
				failure: function() {
					alert("Didn't receive a response");
				},
			});		
		} else if(type == 'watch-ds1307') {
			var rule_name = $('#page-create-rules-content-rule-name').val();		
			var hour_start = $('#page-create-rules-content-watch-ds1307-select-start-hour').val();
			var minute_start = $('#page-create-rules-content-watch-ds1307-select-start-minute').val();
			var hour_stop = $('#page-create-rules-content-watch-ds1307-select-stop-hour').val();
			var minute_stop = $('#page-create-rules-content-watch-ds1307-select-stop-minute').val();
			var dow = $('#page-create-rules-content-watch-ds1307-select-dow').val();
			var fade_time = $('#page-create-rules-content-watch-ds1307-select-fade-time').val();
			var output_pin = $('#page-create-rules-content-watch-ds1307-select-pin-output').val();
			var output_action = $('input:radio[name=page-create-rules-content-watch-ds1307-radio-output]:checked').val(); 
			var pwm_value = $('#page-create-rules-content-watch-ds1307-slider').val();
			var pcf8574_device = $('#page-create-rules-content-select-pcf8574-device').val();
			var pcf8574_pin = $('#page-create-rules-content-select-pcf8574-pin').val();
			
			if(pwm_value > 0 && output_pin != 254) {
				output_action = pwm_value;
			}			
			
			if(localStorage.currentDeviceUseRemoteURL == 0) {
				var root_url = localStorage.currentDeviceIP;
			} else {
				var root_url = localStorage.currentRemoteAccessURL;
			}			
			var url = 'http://' + root_url + '/5/' + rule_name + '/3/' + hour_start + '/' + minute_start + '/' + hour_stop + '/' + minute_stop + '/' + dow + '/' + output_pin + '/' + output_action + '/' + fade_time + '/' + pcf8574_device + '/' + pcf8574_pin;
			$.ajax({
				dataType: "jsonp",
				url: url, 
				success: function(data) {
					alert("Successfully created new rule");	
				},
				failure: function() {
					alert("Didn't receive a response");
				},
			});
		} else if(type == 'watch-ds18b20') {
			var rule_name = $('#page-create-rules-content-rule-name').val();		
			var sensor_num = $('#page-create-rules-content-watch-ds18b20-select-sensor').val();
			var input_pin_action = $('input:radio[name=page-create-rules-content-watch-ds18b20-radio-input]:checked').val(); 
			var input_value = $('#page-create-rules-content-watch-ds18b20-input-value').val().split('.');
			var output_pin = $('#page-create-rules-content-watch-ds18b20-select-pin-output').val();
			var output_action = $('input:radio[name=page-create-rules-content-watch-ds18b20-radio-output]:checked').val(); 
			var pwm_value = $('#page-create-rules-content-watch-ds18b20-slider').val();
			var pcf8574_device = $('#page-create-rules-content-select-pcf8574-device').val();
			var pcf8574_pin = $('#page-create-rules-content-select-pcf8574-pin').val();
			
			if(pwm_value > 0 && output_pin != 254) {
				output_action = pwm_value;
			}			
			
			if(localStorage.currentDeviceUseRemoteURL == 0) {
				var root_url = localStorage.currentDeviceIP;
			} else {
				var root_url = localStorage.currentRemoteAccessURL;
			}			
			var url = 'http://' + root_url + '/5/' + rule_name + '/4/' + sensor_num + '/' + input_pin_action + '/' + input_value[0] + '/' + input_value[1] + '/' + output_pin + '/' + output_action + '/' + pcf8574_device + '/' + pcf8574_pin;
			
			$.ajax({
				dataType: "jsonp",
				url: url, 
				success: function(data) {
					alert("Successfully created new rule");	
				},
				failure: function() {
					alert("Didn't receive a response");
				},
			});
		}
	});

});