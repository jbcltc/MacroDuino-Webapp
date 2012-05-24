$(document).ready(function() {
	//run stuff on DOM load
	
	populate_pin_mode_select();
	populate_analog_pin_dropdowns();
	populate_onewire_device_dropdowns();
	populate_time_dropdowns();

	// save all the new fields when the save button is clicked. Also set on arduino
	$('#page-configure-device-content-save-network-settings').click(function() {
		var url;
		var ip_array;
		// set the device name and call update-select
		if(localStorage.devices) {
			var device_list = JSON.parse(localStorage.devices);
		}
		
		for(var key in device_list) {
			if(device_list[key].ip == localStorage.currentDeviceIP) {
				var new_key = $('#page-configure-device-content-ip').val();
				if(new_key == key) {
					device_list[key].ip = $('#page-configure-device-content-ip').val();
					device_list[key].address = $('#page-configure-device-content-address').val();
					device_list[key].subnet = $('#page-configure-device-content-subnet').val();
					device_list[key].name = $('#page-configure-device-content-name').val();
					device_list[key].gateway = $('#page-configure-device-content-gateway').val();
					device_list[key].type = $('input:radio[name=page-configure-device-content-is-mega]:checked').val();
					device_list[key].remote_access_url = $('#page-configure-device-content-remote-access-url').val();
				} else {
					device_list[new_key] = {
						'ip': $('#page-configure-device-content-ip').val(),
						'address': $('#page-configure-device-content-address').val(),
						'subnet': $('#page-configure-device-content-subnet').val(),
						'gateway': $('#page-configure-device-content-gateway').val(),
						'found': 1,
						'name': $('#page-configure-device-content-name').val(),
						'type': $('input:radio[name=page-configure-device-content-is-mega]:checked').val(),
						'remote_access_url': $('#page-configure-device-content-remote-access-url').val(),
					};				
					
					delete device_list[key];				
				}
			}
		}
		localStorage.currentDeviceType = $('input:radio[name=page-configure-device-content-is-mega]:checked').val();
		localStorage.devices = JSON.stringify(device_list);
		localStorage.currentDeviceName = $('#page-configure-device-content-name').val();
		localStorage.currentDeviceIP = $('#page-configure-device-content-ip').val();
		localStorage.currentRemoteAccessURL = $('#page-configure-device-content-remote-access-url').val();		
		update_devices_list();
		
		ip_array = $('#page-configure-device-content-ip').val().split('.');
		url = 'http://' + localStorage.currentDeviceIP + '/35/' + ip_array[0] + '/' + ip_array[1] + '/' + ip_array[2] + '/' + ip_array[3];
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {

				},
		});	
		
		ip_array = $('#page-configure-device-content-subnet').val().split('.');
		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}		
		url = 'http://' + root_url + '/36/' + ip_array[0] + '/' + ip_array[1] + '/' + ip_array[2] + '/' + ip_array[3];
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {

				},
		});	

		ip_array = $('#page-configure-device-content-gateway').val().split('.');
		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}		
		url = 'http://' + root_url + '/37/' + ip_array[0] + '/' + ip_array[1] + '/' + ip_array[2] + '/' + ip_array[3];
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {

				},
		});	

		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}
		url = 'http://' + root_url + '/1/' + $('#page-configure-device-content-address').val();
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {

				},
		});	

		populate_pin_mode_select();
			
		alert("Successfully saved configuration");
	});
	
	// reset macros button
	$('#page-configure-device-content-reset-macros').click(function() {
		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}	
		var url = 'http://' + root_url + '/2';
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {
					if(data.RESET == "Y") {
						alert('Successfully reset all macros');
					} else {
						alert('Got a response, but not the right one');
					}
			},
			failure: function(data) {
				alert('Request failed');
			},
		});	
	});
	
	//save pins info
	$('#page-configure-device-content-save-pin-name').click(function() {
		// set the device name
		if(localStorage.devices) {
			var device_list = JSON.parse(localStorage.devices);
		}
		
		for(var key in device_list) {
			if(device_list[key].ip == localStorage.currentDeviceIP) {
				var pin_num = $('#page-configure-device-select-set-pin-mode').val();
				var pin_mode = $('input:radio[name=page-configure-device-content-pin-mode]:checked').val();
				var pin_name = $('#page-configure-device-content-pin-name').val();
				device_list[key].digital_pins[pin_num].pin = pin_num;
				device_list[key].digital_pins[pin_num].name = pin_name;
				device_list[key].digital_pins[pin_num].mode = pin_mode;
			}
		}
		
		if(localStorage.currentDeviceUseRemoteURL == 0) {
			var root_url = localStorage.currentDeviceIP;
		} else {
			var root_url = localStorage.currentRemoteAccessURL;
		}		
		var url = 'http://' + root_url + '/3/' + pin_num + '/' + pin_mode;
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {
						if(data.MODE == 1) {
							var mode = "Output";
						} else {
							var mode = "Input";
						}
						alert('Set pin ' + data.PIN + ' to mode ' + mode);
			},
			failure: function(data) {
				alert('Request failed');
			},
		});			
		
		localStorage.devices = JSON.stringify(device_list);
		
		populate_pin_mode_select();		
	});
	
	//update pin field values on change
	$('#page-configure-device-select-set-pin-mode').change(function() {
		if(localStorage.devices) {
			var device_list = JSON.parse(localStorage.devices);
		}
		
		for(var key in device_list) {
			if(device_list[key].ip == localStorage.currentDeviceIP) {
				var pin_num = $('#page-configure-device-select-set-pin-mode').val();
				$('#page-configure-device-content-pin-name').val(device_list[key].digital_pins[pin_num].name);
				if(device_list[key].digital_pins[pin_num].mode == 1) {
					$('#page-configure-device-content-pin-mode-output').prop('checked', true).checkboxradio();
					$('#page-configure-device-content-pin-mode-output').prop('checked', true).checkboxradio("refresh");					
					$('#page-configure-device-content-pin-mode-input').prop('checked', false).checkboxradio();
					$('#page-configure-device-content-pin-mode-input').prop('checked', false).checkboxradio("refresh");					
				} else {
					$('#page-configure-device-content-pin-mode-output').prop('checked', false).checkboxradio();
					$('#page-configure-device-content-pin-mode-output').prop('checked', false).checkboxradio("refresh");					
					$('#page-configure-device-content-pin-mode-input').prop('checked', true).checkboxradio();
					$('#page-configure-device-content-pin-mode-input').prop('checked', true).checkboxradio("refresh");					
				}				
			}
		}		
	});
	
	//save analog pin names
	$('#page-configure-device-content-analog-save-pin-name').click(function() {
		var pin_num = $('#page-configure-device-select-analog-set-pin-mode').val();
		var pin_name = $('#page-configure-device-content-analog-pin-name').val();

		if(localStorage.devices) {
			var device_list = JSON.parse(localStorage.devices);
		}		
		
		for(var key in device_list) {
			if(device_list[key].ip == localStorage.currentDeviceIP) {
				device_list[key].analog_pins[pin_num].name = pin_name;
			}
		}
		
		localStorage.devices = JSON.stringify(device_list);
		
		populate_analog_pin_dropdowns();		
	});
	
	//populate text box with analog pin names
	$('#page-configure-device-select-analog-set-pin-mode').change(function() {
		var pin_num = $('#page-configure-device-select-analog-set-pin-mode').val();		

		if(localStorage.devices) {
			var device_list = JSON.parse(localStorage.devices);
		}		
		
		for(var key in device_list) {
			if(device_list[key].ip == localStorage.currentDeviceIP) {
				$('#page-configure-device-content-analog-pin-name').val(device_list[key].analog_pins[pin_num].name);
			}
		}		
	});

	//discover 1wire address
	$('#page-configure-device-content-discover-onewire-devices').click(function() {
		var url = 'http://' + localStorage.currentDeviceIP + '/13';
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {
				alert('Found ' + data.DEVICES + ' 1wire devices');
			},
			failure: function(data) {
				alert('Request failed');
			},
		});	
		
		url = 'http://' + localStorage.currentDeviceIP + '/14';
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {
				if(localStorage.devices) {
					var device_list = JSON.parse(localStorage.devices);
				}					
			
				for(var key in device_list) {
					if(device_list[key].ip == localStorage.currentDeviceIP) {
						device_list[key].onewire_devices[0].address = data[0].VALUE;
						device_list[key].onewire_devices[1].address = data[1].VALUE;
						device_list[key].onewire_devices[2].address = data[2].VALUE;
						device_list[key].onewire_devices[3].address = data[3].VALUE;
						device_list[key].onewire_devices[4].address = data[4].VALUE;																		
					}
				}
				localStorage.devices = JSON.stringify(device_list);	
			},
			failure: function(data) {
				alert('Request failed');
			},
		});		
					
	});
	
	//populate 1wire text box with analog pin names
	$('#page-configure-device-content-select-onewire-device').change(function() {
		var device_num = $('#page-configure-device-content-select-onewire-device').val();		

		if(localStorage.devices) {
			var device_list = JSON.parse(localStorage.devices);
		}		
		
		for(var key in device_list) {
			if(device_list[key].ip == localStorage.currentDeviceIP) {
				$('#page-configure-device-content-onewire-device-name').val(device_list[key].onewire_devices[device_num].name);
			}
		}		
	});	
	
	//save 1wire device name
	$('#page-configure-device-content-onewire-device-name-save').click(function() {
		var device_num = $('#page-configure-device-content-select-onewire-device').val();
		var device_name = $('#page-configure-device-content-onewire-device-name').val();

		if(localStorage.devices) {
			var device_list = JSON.parse(localStorage.devices);
		}		
		
		for(var key in device_list) {
			if(device_list[key].ip == localStorage.currentDeviceIP) {
				device_list[key].onewire_devices[device_num].name = device_name;
			}
		}
		
		localStorage.devices = JSON.stringify(device_list);	
		
		populate_onewire_device_dropdowns();
	});
	
	//save device time
	$('#page-configure-device-content-device-time-save').click(function() {
		var hour = $('#page-configure-device-content-select-device-time-hour').val();
		var minute = $('#page-configure-device-content-select-device-time-minute').val();
		var second = $('#page-configure-device-content-select-device-time-second').val();
		var day = $('#page-configure-device-content-select-device-time-day').val();
		var dow = $('#page-configure-device-content-select-device-time-dow').val();
		var month = $('#page-configure-device-content-select-device-time-month').val();
		var year = $('#page-configure-device-content-select-device-time-year').val();
		
		var url = 'http://' + localStorage.currentDeviceIP + '/9/' + hour + '/' + minute + '/' + second + '/' + day + '/' + dow + '/' + month + '/' + year;
		$.ajax({
			dataType: "jsonp",
			url: url, 
			success: function(data) {
				alert('Successfully set device time');
			},
			failure: function(data) {
				alert('Request failed');
			},
		});			
	});
	
	//listen for change in "Use remotely"
	$("input:radio[name=page-configure-device-content-use-remote]").change(function(){
		localStorage.currentDeviceUseRemoteURL = $("input:radio[name=page-configure-device-content-use-remote]:checked").val();
		if(localStorage.currentDeviceUseRemoteURL == 0) {
			alert("Using locally");
		} else {
			alert("Using remotely");
		}
	});
});