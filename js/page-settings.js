$(document).ready(function() {
	//POPULATE SETTINGS FORM
	$('#settings-text-local-ip-address').val(localStorage.localIPAddress);
	$('#page-settings-text-cosm-username').val(localStorage.cosmUsername);
	// SAVING SETTINGS
	
	// save local ip address
	$('#settings-button-save').click(function() {
		localStorage.localIPAddress = $('#settings-text-local-ip-address').val();
		localStorage.cosmUsername = $('#page-settings-text-cosm-username').val();
		alert('Saved settings');
	});
	
	//discover devices
	$('#settings-button-discover-devices').click(function() {
		
		localStorage.devices = "";
		
    	for(var i=1; i<= 255; i++) {
    		if(localStorage.localIPAddress != null) {
	    		var url = 'http://' + localStorage.localIPAddress + '.' + i + '/255';
				$.ajax({
					dataType: "jsonp",
					url: url, 
					success: function(data) {
							
							if(localStorage.devices != "") {
								var current_devices = JSON.parse(localStorage.devices);
							}
							
							var digital_pins = {};
							for(var i=0; i<=54; i++) {
								digital_pins[i] = {
									'pin': i,
									'name': i,
									'mode': 0,
								};
							}
							
							var analog_pins = {};
							for(var i=0; i<=15; i++) {
								analog_pins[i] = {
									'pin': i,
									'name': i,
								};
							}
							
							var onewire_devices = {};
							for(var i=0; i<=4; i++) {
								onewire_devices[i] = {
									'address': i,
									'device_num': i,
									'name': i,
								}
							}
						
							var device = {};
							var widgets = {};
							device[data.IP] = {
								'ip': data.IP,
								'address': data.ADR,
								'subnet': data.SUBNET,
								'gateway': data.GATEWAY,
								'found': 1,
								'name': data.IP,
								'digital_pins': digital_pins,
								'analog_pins': analog_pins,
								'type':0,
								'onewire_devices': onewire_devices,
								'widgets': widgets,
								'remote_access_url': 0,
							};
							
							if(current_devices) {
								device = device.concat(current_devices);
							}
							
							localStorage.currentDeviceName = data.IP;
							localStorage.currentDeviceIP = data.IP;
							localStorage.currentDeviceType = 0;
							localStorage.currentDeviceUseRemoteURL = 0;
							
							localStorage.devices = JSON.stringify(device);
							
							alert('Found a device at: ' + data.IP);
							
							update_devices_list();
							populate_pin_mode_select();
						},
				});	    		
	    	}
		} 	
	});
	
});