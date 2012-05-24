$(document).ready(function() {
	//run stuff on DOM load

	update_devices_list();

});


function update_devices_list() {
	if(localStorage.devices) {
		var device_list = JSON.parse(localStorage.devices);
	}
	
	var newOptions = {};
	for(var key in device_list) {
		if(device_list[key].ip) {
			var ip = device_list[key].ip;
			var name = device_list[key].name;
			newOptions[ip] = name;
		}
	}
	
	var selectedOption = localStorage.currentDeviceIP;
	
	var select = $('#dashboard #select-current-device');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});
	select.val(selectedOption);	
	
	$("#dashboard .ui-select .ui-btn-text").text(localStorage.currentDeviceName);	
}

function populate_pin_mode_select() {
	//populate pin mode select list
	if(localStorage.currentDeviceType) {
		var device_type = localStorage.currentDeviceType;
	}
	if(localStorage.devices) {
		var device_list = JSON.parse(localStorage.devices);
	}	
	
	if(device_type == 0) {
		var num_pins = 13;
	} else {
		var num_pins = 54;
	}
	
	for(var key in device_list) {
		if(device_list[key].ip == localStorage.currentDeviceIP) {	
			var device_key = key;
		}
	}
		
	var newOptions = {};
	if(device_list) {
		for(var i=0; i<=num_pins; i++) {
			newOptions[i] = device_list[device_key].digital_pins[i].name;
		}
	}
	
	//populate pins on configure device select list	
	var select = $('#page-configure-device-select-set-pin-mode');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});
	//also populate pin mode select on control device page	
	select = $('#page-control-device-content-select-pin');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	//also populate pin mode select INPUT on create digital pin rule
	select = $('#page-create-rules-content-watch-digital-pin-select-pin-input');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	//pins for take reading digital pin dropdown
	select = $('#page-take-reading-content-digital-pin-type-select-pin');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});
	//also populate pin mode select OUTPUT on create digital pin rule
	select = $('#page-create-rules-content-watch-digital-pin-select-pin-output');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	//add in pcf8574 option
	newOptions[254] = "PCF8574";	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	// also populate create rule analog output dropdown
	select = $('#page-create-rules-content-watch-analog-pin-select-pin-output');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});		
	//create rules ds1307 output pins
	select = $('#page-create-rules-content-watch-ds1307-select-pin-output');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	//add in pcf8574 option
	newOptions[254] = "PCF8574";	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});
	// digital outputs for temperature rule
	select = $('#page-create-rules-content-watch-ds18b20-select-pin-output');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	//add in pcf8574 option
	newOptions[254] = "PCF8574";	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});
}

function populate_analog_pin_dropdowns() {
	//populate pin mode select list
	if(localStorage.currentDeviceType) {
		var device_type = localStorage.currentDeviceType;
	}
	if(localStorage.devices) {
		var device_list = JSON.parse(localStorage.devices);
	}	
	
	if(device_type == 0) {
		var num_pins = 6;
	} else {
		var num_pins = 16;
	}
	
	for(var key in device_list) {
		if(device_list[key].ip == localStorage.currentDeviceIP) {	
			var device_key = key;
		}
	}
		
	var newOptions = {};
	if(device_list) {
		for(var i=0; i<=num_pins; i++) {
			newOptions[i] = device_list[device_key].analog_pins[i].name;
		}
	}
	
	//populate pins on create rule analog value rule	
	var select = $('#page-create-rules-content-watch-analog-pin-select-pin-input');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});
	//configure device analog dropdown list
	select = $('#page-configure-device-select-analog-set-pin-mode');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	//configure take readings analog pins
	select = $('#page-take-reading-content-analog-pin-type-select-pin');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});		
}

function populate_ds1307_dropdowns() {		
	var newOptionsDOW = {
		0: 'All',
		1:'Monday',
		2:'Tuesday',
		3:'Wednesday',
		4:'Thursday',
		5:'Friday',
		6:'Saturday',
		7:'Sunday',
	};

	
	var newOptionsHour = {};
	for(var i=0; i<=23; i++) {
		newOptionsHour[i] = i;
	}
	
	var newOptionsMinute = {};
	for(var i=0; i<=59; i++) {
		newOptionsMinute[i] = i;
	}	
	
	var newOptionsFadeTime = {};
	for(var i=0; i<=255; i++) {
		newOptionsFadeTime[i] = i;
	}	
		
	//populate hour start dropdown
	var select = $('#page-create-rules-content-watch-ds1307-select-start-hour');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsHour, function(val, text) {
	    options[options.length] = new Option(text, val);
	});
	//populate minute start dropdown
	var select = $('#page-create-rules-content-watch-ds1307-select-start-minute');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsMinute, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	//populate hour stop dropdown
	var select = $('#page-create-rules-content-watch-ds1307-select-stop-hour');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsHour, function(val, text) {
	    options[options.length] = new Option(text, val);
	});
	//populate minute stop dropdown
	var select = $('#page-create-rules-content-watch-ds1307-select-stop-minute');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsMinute, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	//populate DOW dropdown
	var select = $('#page-create-rules-content-watch-ds1307-select-dow');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsDOW, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	//populate fade time in minutes
	var select = $('#page-create-rules-content-watch-ds1307-select-fade-time');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsFadeTime, function(val, text) {
	    options[options.length] = new Option(text, val);
	});			
}


//populate 1wire device dropdowns
function populate_onewire_device_dropdowns() {

	if(localStorage.devices) {
		var device_list = JSON.parse(localStorage.devices);
	}	
	
	for(var key in device_list) {
		if(device_list[key].ip == localStorage.currentDeviceIP) {	
			var device_key = key;
		}
	}
		
	var newOptions = {};
	if(device_list) {
		for(var i=0; i<=4; i++) {
			newOptions[i] = device_list[device_key].onewire_devices[i].name;
		}
	}
	
	//populate pins on create rule analog value rule	
	var select = $('#page-create-rules-content-watch-ds18b20-select-sensor');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});
	//configure device analog dropdown list
	select = $('#page-configure-device-content-select-onewire-device');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	//take readings 1wire device dropdown
	select = $('#page-take-reading-content-ds18b20-select-device');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptions, function(val, text) {
	    options[options.length] = new Option(text, val);
	});			
}

function populate_time_dropdowns() {
	var newOptionsHour = {};
	for(var i=0; i<=23; i++) {
		newOptionsHour[i] = i;
	}
	
	var newOptionsMinute = {};
	for(var i=0; i<=59; i++) {
		newOptionsMinute[i] = i;
	}	
	
	var newOptionsSecond = {};
	for(var i=0; i<=59; i++) {
		newOptionsSecond[i] = i;
	}
	
	var newOptionsDay = {};
	for(var i=1; i<=31; i++) {
		newOptionsDay[i] = i;
	}	
	
	var newOptionsDOW = {
		1:'Monday',
		2:'Tuesday',
		3:'Wednesday',
		4:'Thursday',
		5:'Friday',
		6:'Saturday',
		7:'Sunday',
	};
	
	var newOptionsMonth = {
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
	
	var newOptionsYear = {};
	for(var i=12; i<=20; i++) {
		newOptionsYear[i] = '20' + i;
	}	
	
	//hour	
	var select = $('#page-configure-device-content-select-device-time-hour');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsHour, function(val, text) {
	    options[options.length] = new Option(text, val);
	});
	//minute
	select = $('#page-configure-device-content-select-device-time-minute');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsMinute, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	//second
	select = $('#page-configure-device-content-select-device-time-second');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsSecond, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	//day
	select = $('#page-configure-device-content-select-device-time-day');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsDay, function(val, text) {
	    options[options.length] = new Option(text, val);
	});			
	//dow
	select = $('#page-configure-device-content-select-device-time-dow');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsDOW, function(val, text) {
	    options[options.length] = new Option(text, val);
	});			
	//month
	select = $('#page-configure-device-content-select-device-time-month');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsMonth, function(val, text) {
	    options[options.length] = new Option(text, val);
	});	
	//year
	select = $('#page-configure-device-content-select-device-time-year');
	if(select.prop) {
	  var options = select.prop('options');
	}
	else {
	  var options = select.attr('options');
	}
	$('option', select).remove();
	
	$.each(newOptionsYear, function(val, text) {
	    options[options.length] = new Option(text, val);
	});			
}