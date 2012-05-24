$(document).ready(function() {
	//set the current device ip to show

	$('#dashboard #select-current-device').change(function() {
		$("#dashboard #select-current-device .ui-btn-text").text($('#dashboard #select-current-device option:selected').val());
	});
	
	$('#page-dashboard-content-configure-device').click(function() {
		//page-configure-device-content-
		if(localStorage.devices) {
			var device_list = JSON.parse(localStorage.devices);
		}
		
		var newOptions = {};
		for(var key in device_list) {
			if(device_list[key].ip == localStorage.currentDeviceIP) {
				$('#page-configure-device-content-name').val(device_list[key].name);
				$('#page-configure-device-content-ip').val(device_list[key].ip);
				$('#page-configure-device-content-subnet').val(device_list[key].subnet);
				$('#page-configure-device-content-gateway').val(device_list[key].gateway);
				$('#page-configure-device-content-address').val(device_list[key].address);																
				$('#page-configure-device-content-remote-access-url').val(device_list[key].remote_access_url);				
			}
		}
		if(localStorage.currentDeviceType == 1) {
			$('#page-configure-device-content-is-mega-yes').prop('checked', true).checkboxradio("refresh");
			$('#page-configure-device-content-is-mega-no').prop('checked', false).checkboxradio("refresh");		
		} else {
			$('#page-configure-device-content-is-mega-no').prop('checked', true).checkboxradio("refresh");	
			$('#page-configure-device-content-is-mega-yes').prop('checked', false).checkboxradio("refresh");	
		}			
	});
	
	$('#page-dashboard-content-edit-rules').click(function() {
		update_rules_list();
	});

});