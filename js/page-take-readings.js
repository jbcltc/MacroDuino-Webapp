$(document).ready(function() {
	var interval;

	//show initially on page load
	$('#page-take-reading-content-digital-pin-type').show();

	// show other divs depending on what's selected
	$('#page-take-reading-content-select-type-of-reading').change(function() {
		var type = $('#page-take-reading-content-select-type-of-reading').val();
		
		if(type == 'digital-pin') {
			$('#page-take-reading-content-digital-pin-type').show();
			$('#page-take-reading-content-analog-pin-type').hide();
			$('#page-take-reading-content-ds1307').hide();
			$('#page-take-reading-content-ds18b20').hide();			
		} else if(type == 'analog-pin') {
			populate_analog_pin_dropdowns();
			$('#page-take-reading-content-digital-pin-type').hide();
			$('#page-take-reading-content-analog-pin-type').show();		
			$('#page-take-reading-content-ds1307').hide();			
			$('#page-take-reading-content-ds18b20').hide();			
		} else if(type == 'ds1307') {
			$('#page-take-reading-content-digital-pin-type').hide();
			$('#page-take-reading-content-analog-pin-type').hide();		
			$('#page-take-reading-content-ds1307').show();
			$('#page-take-reading-content-ds18b20').hide();					
		} else if(type == 'ds18b20') {
			populate_onewire_device_dropdowns();
			$('#page-take-reading-content-digital-pin-type').hide();
			$('#page-take-reading-content-analog-pin-type').hide();		
			$('#page-take-reading-content-ds1307').hide();
			$('#page-take-reading-content-ds18b20').show();			
		}
	});
	
	// start getting digital pin readings
	$('#page-take-reading-content-digital-pin-start').click(function() {
		interval = setInterval(function() {
			if(localStorage.currentDeviceUseRemoteURL == 0) {
				var root_url = localStorage.currentDeviceIP;
			} else {
				var root_url = localStorage.currentRemoteAccessURL;
			}		
			var url = 'http://' + root_url + '/7/' + $('#page-take-reading-content-digital-pin-type-select-pin').val();
			$.ajax({
				dataType: "jsonp",
				url: url, 
				success: function(data) {
					$('#page-take-reading-content-reading-div').html('<h2>Reading: ' + data.VALUE + '</h2>');
				},
				failure: function() {
				
				},
			});		
		},
		($('#page-take-reading-content-digital-pin-type-select-interval').val() * 1000));
	});
	
	$('#page-take-reading-content-digital-pin-stop').click(function() {
		interval = window.clearInterval(interval);
		$('#page-take-reading-content-reading-div').html("");
	});
	
	// start getting analog pin readings
	$('#page-take-reading-content-analog-pin-start').click(function() {
		interval = setInterval(function() {
			if(localStorage.currentDeviceUseRemoteURL == 0) {
				var root_url = localStorage.currentDeviceIP;
			} else {
				var root_url = localStorage.currentRemoteAccessURL;
			}		
			var url = 'http://' + root_url + '/8/' + $('#page-take-reading-content-analog-pin-type-select-pin').val();
			$.ajax({
				dataType: "jsonp",
				url: url, 
				success: function(data) {
					$('#page-take-reading-content-reading-div').html('<h2>Reading: ' + data.VALUE + '</h2>');
				},
				failure: function() {
				
				},
			});		
		},
		($('#page-take-reading-content-analog-pin-type-select-interval').val() * 1000));
	});
	
	$('#page-take-reading-content-analog-pin-stop').click(function() {
		interval = window.clearInterval(interval);
		$('#page-take-reading-content-reading-div').html("");
	});	

	//ds1307 readings
	$('#page-take-reading-content-ds1307-start').click(function() {
		interval = setInterval(function() {
			if(localStorage.currentDeviceUseRemoteURL == 0) {
				var root_url = localStorage.currentDeviceIP;
			} else {
				var root_url = localStorage.currentRemoteAccessURL;
			}		
			var url = 'http://' + root_url + '/10';
			$.ajax({
				dataType: "jsonp",
				url: url, 
				success: function(data) {
					var dow = {
						1:'Monday',
						2:'Tuesday',
						3:'Wednesday',
						4:'Thursday',
						5:'Friday',
						6:'Saturday',
						7:'Sunday',
					};
					
					var month = {
						1:'January',
						2:'February',
						3:'March',
						4:'April',
						5:'May',
						6:'June',
						7:'July',
						8:'August',
						9:'September',
						10:'October',
						11:'November',
						12:'December',
					}
					
					var hour = data.H;
					var minute = data.M;
					var second = data.S;
					var day = data.D;
					var year = data.Y;
					var out_dow = dow[data.DOW];
					var out_month = month[data.MONTH];
					
					if(minute < 10) {
						minute = '0' + minute;
					}
					if(second < 10) {
						second = '0' + second;
					}
					
					var output = '<h2>' + hour + ':' + minute + ':' + second + ' ' + out_dow + ', ' + out_month + ' ' + day + ', ' + year  + '</h2>';			
					$('#page-take-reading-content-reading-div').html(output);
				},
				failure: function() {
				
				},
			});		
		},
		($('#page-take-reading-content-ds1307-select-interval').val() * 1000));
	});
	
	$('#page-take-reading-content-ds1307-stop').click(function() {
		interval = window.clearInterval(interval);
		$('#page-take-reading-content-reading-div').html("");
	});	
	
	//temperature readings
	$('#page-take-reading-content-ds18b20-start').click(function() {
		interval = setInterval(function() {
			if(localStorage.currentDeviceUseRemoteURL == 0) {
				var root_url = localStorage.currentDeviceIP;
			} else {
				var root_url = localStorage.currentRemoteAccessURL;
			}		
			var url = 'http://' + root_url + '/15/' + $('#page-take-reading-content-ds18b20-select-device').val();
			$.ajax({
				dataType: "jsonp",
				url: url, 
				success: function(data) {
					$('#page-take-reading-content-reading-div').html('<h2>Reading: ' + (data.VALUE/100) + 'F</h2>');
				},
				failure: function() {
				
				},
			});		
		},
		($('#page-take-reading-content-ds18b20-select-interval').val() * 1000));
	});
	
	$('#page-take-reading-content-ds18b20-stop').click(function() {
		interval = window.clearInterval(interval);
		$('#page-take-reading-content-reading-div').html("");
	});		

});