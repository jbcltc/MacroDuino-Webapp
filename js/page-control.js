$(document).ready(function() {
	//run stuff on DOM load
	$("input:radio[name=page-control-device-content-control-pin]").change(function(){
		if ($("input:radio[name=page-control-device-content-control-pin]:checked").val() == 1)   {
			var action = 1;
		}
		else{
			var action = 0;
		}
		
		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}
		var url = 'http://' + root_url + '/4/' + $('#page-control-device-content-select-pin').val() + '/' + action;
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {

			},
			failure: function() {
			
			},
		});		
	});
	
	$('#page-control-device-content-pwm-slider').change(function() {
		var value = $('#page-control-device-content-pwm-slider').val();
		var pin = $('#page-control-device-content-select-pin').val();

		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}
		var url = 'http://' + root_url + '/4/' + pin + '/' + value;
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {

			},
			failure: function() {
			
			},
		});				
	});
	
	$("input:radio[name=page-control-device-content-control-pcf8574-pin]").change(function(){
		var action = $("input:radio[name=page-control-device-content-control-pcf8574-pin]:checked").val();
		var device = $('#page-control-device-content-select-pcf8574-device').val();
		var pin = $('#page-control-device-content-select-pcf8574-pin').val();
		
		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}
		var url = 'http://' + root_url + '/6/' + device + '/' + pin + '/' + action;
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {

			},
			failure: function() {
			
			},
		});		
	});	

});