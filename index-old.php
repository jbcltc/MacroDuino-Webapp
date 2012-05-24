<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" />
<!--
        <script src="http://jsconsole.com/remote.js?"></script>
        <script src="http://192.168.1.2:8080/target/target-script-min.js"></script>
-->
        <title>MacroDuino Webapp</title>
        <style type="text/css" media="screen">@import "jqtouch/jqtouch.css";</style>
        <style type="text/css" media="screen">@import "themes/apple/theme.css";</style>
		<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.13.custom.css" rel="stylesheet" />        
		<link type="text/css" href="css/style.css" rel="stylesheet" />        
        <script src="jqtouch/jquery-1.4.2.js" type="text/javascript" charset="utf-8"></script>
        <script src="jqtouch/jqtouch.js" type="application/x-javascript" charset="utf-8"></script>
        <script src="js/jquery-ui-1.8.13.custom.min.js" type="application/x-javascript" charset="utf-8"></script>
        <script type="text/javascript" src="js/jgrowl/jquery.jgrowl.js"></script>
        <script type="text/javascript" charset="utf-8">
	        var arduino_addr;
	        var jgrowlTheme = "iphone";
	        var jgrowlDelay = 1500;
	        
	        //global variables
	        var control_screen_additem_enable_pwm_toggle_prev = 0;
	        var control_screen_item_states = new Array();
	        var control_screen_form_pwm_slider_div_id;
	        //end global variables
	        
        	var db = openDatabase('macroduino', '1.0', 'macroduino database', 2 * 1024 * 1024);
        	db.transaction(function (tx) {
				tx.executeSql('CREATE TABLE IF NOT EXISTS addresses (current TEXT, ip TEXT)');
				tx.executeSql('CREATE TABLE IF NOT EXISTS status_screen (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, type TEXT, name TEXT, value TEXT, ip TEXT)');
				tx.executeSql('CREATE TABLE IF NOT EXISTS control_screen (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, name TEXT, type TEXT, pin TEXT, devicenum TEXT, enable_pwm TEXT, status TEXT)');
			});
        
            var jQT = new $.jQTouch({
                icon: 'jqtouch.png',
                icon4: 'jqtouch4.png',
                addGlossToIcon: false,
                startupScreen: 'jqt_startup.png',
                statusBar: 'black',
                preloadImages: [
                    'themes/jqt/img/activeButton.png',
                    'themes/jqt/img/back_button.png',
                    'themes/jqt/img/back_button_clicked.png',
                    'themes/jqt/img/blueButton.png',
                    'themes/jqt/img/button.png',
                    'themes/jqt/img/button_clicked.png',
                    'themes/jqt/img/grayButton.png',
                    'themes/jqt/img/greenButton.png',
                    'themes/jqt/img/redButton.png',
                    'themes/jqt/img/whiteButton.png',
                    'themes/jqt/img/loading.gif'
                    ]
            });
            
            
            $(function(){
				db.transaction(function (tx) {
					tx.executeSql("SELECT * FROM addresses WHERE current='1'", [], function (tx, results) {
						arduino_addr = "http://" + results.rows.item(0).ip;						
					});
				});            

				//do some configuring of display elements            
				$('#control_screen_form_pcf8574_device_span').attr('hidden', true);
				$('#control_screen_form_enable_pwm_span').attr('hidden', true);
            	            	
				setInterval(function() {
					db.transaction(function (tx) {
						var sql = "SELECT * FROM status_screen WHERE ip='" + arduino_addr + "'";
						$('#statusscreendiv').html("<ul id='statusscreendivul'>");
						tx.executeSql(sql, [], function (tx, results) {
							var len = results.rows.length, i;
													
							for (i = 0; i < len; i++) {
								if(results.rows.item(i).type == 'dpin') {
									var url = arduino_addr + '/7/' + results.rows.item(i).value + '?callback=?';
									var dpinname = results.rows.item(i).name;
									function dpinGetResults(dpinname) {
										$.getJSON( url, function ( data ) {
											$("<li>" +  dpinname + ": " + data.VALUE.VALUE + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='" + dpinname + "' class='statusscreendivdelete' value='X'></li>").appendTo('#statusscreendivul');
										});
									}
									dpinGetResults(dpinname);
								}else if(results.rows.item(i).type == 'apin') {
									var url = arduino_addr + '/8/' + results.rows.item(i).value + '?callback=?';
									var apinname = results.rows.item(i).name;
									function apinGetResults(apinname) {
										$.getJSON( url, function ( data ) { 
											$("<li>" +  apinname + ": " + data.VALUE.VALUE + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='" + apinname + "' class='statusscreendivdelete' value='X'></li>").appendTo('#statusscreendivul');
										});
									}
									apinGetResults(apinname);
								}else if(results.rows.item(i).type == 'ph') {
									var url = arduino_addr + '/28/' + results.rows.item(i).value + '?callback=?';
									var phname = results.rows.item(i).name;
									function phGetResults(phname) {
										$.getJSON( url, function ( data ) { 
											$("<li>" +  phname + ": " + (parseInt(data.VALUE) / 100) + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='" + phname + "' class='statusscreendivdelete' value='X'></li>").appendTo('#statusscreendivul');
										});
									}
									phGetResults(phname);
								}else if(results.rows.item(i).type == 'ds18b20') {
									var url = arduino_addr + '/15/' + results.rows.item(i).value + '?callback=?';
									var ds18b20name = results.rows.item(i).name;
									function ds18b20GetResults(ds18b20name) {
										$.getJSON( url, function ( data ) { 
											$("<li>" +  ds18b20name + ": " + (data.VALUE.VALUE / 100) + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='" + ds18b20name + "' class='statusscreendivdelete' value='X'></li>").appendTo('#statusscreendivul');
										});
									}
									ds18b20GetResults(ds18b20name);
								}								
							}
							$("</ul>").appendTo('statusscreendivul');
						});
					});
				}, 8000);

				$('.statusscreendivdelete').live('click', function() {
					var inputname = this.name;
					db.transaction(function (tx) {
						var sql = "DELETE FROM status_screen WHERE name='" + inputname + "'";
						tx.executeSql(sql, [], function (tx, results) {

						});
					});	
				});
	/*
				* status screen add
				*/
				$('#statusscreenadditemsave').click(function() {
					db.transaction(function (tx) {
						var sql = "INSERT INTO status_screen (type, name, value, ip) VALUES ('" + $('#statusscreenadditemsstatusitem').val() + "', '" + $('#statusscreenadditenname').val() + "', '" + $('#statusscreenadditempinnum').val() + "', '" + arduino_addr + "')";
						tx.executeSql(sql, [], function (tx, results) {
						/*  var len = results.rows.length, i;
						  var towrite = "<ul>";
						  for (i = 0; i < len; i++) {
						  	towrite += "<li><input type='radio' name='arduinosavailable' value='" + results.rows.item(i).ip + "'>" + results.rows.item(i).ip + "</li>";
						  }
						  towrite += "</ul>";
						  $('#arduinolist').html(towrite);*/
						});
					});	
					$.jGrowl("<br><br><br><center>Success</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
				});
				
				/**********
				* control page
				***********/
				$('#control_screen_form_type_select').live('change', function() {
					var type = $('#control_screen_form_type_select').val();
					if(type == 'digital') {
						$('#control_screen_form_pcf8574_device_span').attr('hidden', true);
						$('#control_screen_form_enable_pwm_span').removeAttr('hidden');
					} else if(type == 'pcf8574') {
						$('#control_screen_form_pcf8574_device_span').removeAttr('hidden');
						$('#control_screen_form_enable_pwm_span').attr('hidden', true);
					}
					
				});
				
				$('#control_screen_form_save').click(function() {
					var pin = $('#control_screen_form_pin_number').val();
					var type = $('#control_screen_form_type_select').val();
					var device = $('#control_screen_form_pcf8574_device').val();
					var name = $('#control_screen_form_name').val();
					//TODO
					if(control_screen_additem_enable_pwm_toggle_prev == 1) {
						if(pin != 3 || pin != 5 || pin != 6 || pin != 9 || pin != 10 || pin != 11) {
							$.jGrowl("<br><br><br><center>The item you want to add doesn't seem to be PWM capable</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay});
						}
					}
	            	
	            	db.transaction(function (tx) {
	            		//tx.executeSql('CREATE TABLE IF NOT EXISTS control_screen (id AUTO, type TEXT, pin TEXT, device_num TEXT, status TEXT');
						var sql = "INSERT INTO control_screen (name, type, pin, devicenum, enable_pwm) VALUES ('" + name + "', '" + type + "', '" + pin + "', '" + device + "', '" + control_screen_additem_enable_pwm_toggle_prev + "')";
						tx.executeSql(sql, [], function (tx, results) {
	
						});					
						$.jGrowl("<br><br><br><center>Added control object</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay});
					});
					loadControlScreenItems();
					jQT.goTo('#control_screen');
				});
				
				$('#control_screen_home_href').click(function() {
					loadControlScreenItems();
				});
				
				function loadControlScreenItems() {
					db.transaction(function (tx) {
						var sql = "SELECT * FROM control_screen";
						$('#control_screen_div').html("<ul id='control_screen_div_ul'>");
						tx.executeSql(sql, [], function (tx, results) {
							var len = results.rows.length, i;
							var toappend;						
							for (i = 0; i < len; i++) {
								toappend = "<li>";
								toappend += "<input type='button' value='X' name='" + results.rows.item(i).id + "' class='control_screen_delete_item'>&nbsp;&nbsp;&nbsp;";
								toappend += results.rows.item(i).name + ": ";
								toappend += "<span class='toggle'><input type='checkbox' class='control_screen_form_checkbox_toggle_class' id='control_screen_form_checkbox_toggle_" + results.rows.item(i).id + "'></span>";
								if(results.rows.item(i).enable_pwm == 1) {
									toappend += "<br><br>";
									//$("#control_screen_form_pwm_slider_div").slider({ min:0, max:255 });
									toappend += "<div class=control_screen_pwm_slider_class id=control_screen_form_pwm_slider_div_id_" + results.rows.item(i).id + "></div>";
									toappend += "<br>";
								}
								toappend += "</li>";
								$(toappend).appendTo('#control_screen_div_ul');
								control_screen_form_pwm_slider_div_id = results.rows.item(i).id;
								if(results.rows.item(i).enable_pwm == 1) {
									$("#control_screen_form_pwm_slider_div_id_" + results.rows.item(i).id).slider({ 
										min:0, 
										max:255,
										slide: function(event, ui) { 
											var id = this.id;
											id = id.split("control_screen_form_pwm_slider_div_id_");
											db.transaction(function (tx) {
												var sql = "SELECT * FROM control_screen WHERE id='" + id[1] + "'";
												tx.executeSql(sql, [], function (tx, results) {
													if(results.rows.item(0).type == 'digital') {
														var url = arduino_addr + '/4/' + results.rows.item(0).pin + '/' + ui.value + '?callback=?';
													}else if(results.rows.item(0).type == 'pcf8574') {
														var url = arduino_addr + '/4/' + ui.value + '?callback=?';
													}
													
													$.getJSON( url, function ( data ) { 
														$.jGrowl("<br><br><br><center>Updated PWM value</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay});
													});
													
												});
											});
										},
									});
								}
							}
						});
					});				
				}
				
				$('.control_screen_delete_item').live('click', function() {
					var inputname = this.name;
					db.transaction(function (tx) {
						var sql = "DELETE FROM control_screen WHERE id='" + inputname + "'";
						tx.executeSql(sql, [], function (tx, results) {

						});
					});		
					loadControlScreenItems();
				});
				
				$('#control_screen_form_enable_pwm_toggle').click(function() {
	            	if($('#control_screen_form_enable_pwm_toggle').val() == 'on' && control_screen_additem_enable_pwm_toggle_prev == 0) {
	            		control_screen_additem_enable_pwm_toggle_prev = 1;
	            	}else if(control_screen_additem_enable_pwm_toggle_prev == 1) {
	            		control_screen_additem_enable_pwm_toggle_prev = 0;
	            	}				
				});
				
				$('.control_screen_form_checkbox_toggle_class').live('click', function() {
					var id = this.id;
					id = id.split('control_screen_form_checkbox_toggle_');
					db.transaction(function (tx) {
						var sql = "SELECT * FROM control_screen WHERE id='" + id[1] + "'";
						tx.executeSql(sql, [], function (tx, results) {
							if(control_screen_item_states[results.rows.item(0).id] == 0 || control_screen_item_states[results.rows.item(0).id] == null) {
								control_screen_item_states[results.rows.item(0).id] = 1;
							}else if(control_screen_item_states[results.rows.item(0).id] == 1) {
								control_screen_item_states[results.rows.item(0).id] = 0;
							}						
							
							if(results.rows.item(0).type == 'digital') {
								var url = arduino_addr + '/4/' + results.rows.item(0).pin + '/' + control_screen_item_states[results.rows.item(0).id] + '?callback=?';
							}else if(results.rows.item(0).type == 'pcf8574') {
								var url = arduino_addr + '/6/' + results.rows.item(0).devicenum + '/' + results.rows.item(0).pin + '/' + control_screen_item_states[results.rows.item(0).id] + '?callback=?';
							}
							
							$.getJSON( url, function ( data ) { 
								$.jGrowl("<br><br><br><center>Toggled value</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay});
							});
						});
					});
				});
				
				/************
				*** set macro page
				*************/
				$('#digitalmacrosetoutputpin').live('change', function() {
					if($('#digitalmacrosetoutputpin').val() == 254) {
						$('#digitalmacrosetpcf8574span').removeAttr('hidden');
					} else {
						$('#digitalmacrosetpcf8574span').attr('hidden', true);
					}
				});

				$('#analogmacrosetoutputpin').live('change', function() {
					if($('#analogmacrosetoutputpin').val() == 254) {
						$('#analogmacrosetpcf8574span').removeAttr('hidden');
					} else {
						$('#analogmacrosetpcf8574span').attr('hidden', true);
					}
				});
				
				$('#ds18b20macrosetoutputpin').live('change', function() {
					if($('#ds18b20macrosetoutputpin').val() == 254) {
						$('#ds18b20macrosetpcf8574span').removeAttr('hidden');
					} else {
						$('#ds18b20macrosetpcf8574span').attr('hidden', true);
					}
				});
				
				$('#ds1307macrosetoutputpin').live('change', function() {
					if($('#ds1307macrosetoutputpin').val() == 254) {
						$('#ds1307macrosetpcf8574span').removeAttr('hidden');
					} else {
						$('#ds1307macrosetpcf8574span').attr('hidden', true);
					}
				});					
				
				
				
				$('#macro_set_screen_macro_type').change(function() {
					var macro_type = $('#macro_set_screen_macro_type').val();
					
					if(macro_type == 1) {
						var digital_macro_html = "<ul>" +
								"<li>Macro Name:</li>" +
								"<li><input type='text' id='digitalmacrosetmacroname' placeholder='5 Character Max'></li>" +
								"<li>Watch Pin</li>" +
								"<li><input type='number' id='digitalmacrosetwatchpin' placeholder='Digital Pin #'></li>" +
								"<li>Watch State</li>" +
								"<li><input type='number' id='digitalmacrosetwatchstate' placeholder='0 = LOW, 1 = HIGH'></li>" +
								"<li>Output Pin</li>" +
								"<li><input type='number' id='digitalmacrosetoutputpin' placeholder='1-13 or 254 for PCF8574'></li>" +
								"<li>Output Action</li>" +
								"<li><input type='number' id='digitalmacrosetoutputaction' placeholder='Output Action'></li>" +
								"<span id='digitalmacrosetpcf8574span'><li>PCF8574 Device</li>" +
								"<li><input type='number' id='digitalmacrosetpcf8574device' placeholder='PCF8574 Device #'></li>" +								
								"<li>PCF8574 Pin</li>" +
								"<li><input type='number' id='digitalmacrosetpcf8574pin' placeholder='PCF8574 Pin #'></li></span>" +										
							"</ul><div><p>Output action can be a value between 0 and 255. 0 and 1 are on/off. Any other number is a PWM value.</p><p>PCF8574 device # starts at 0. Device # 0 is the one with all pins grounded. Look in the macroduino code for variable pcf8574_addresses to figure out which device # your address is.</p></div>";
						$('#macro_set_screen_div').html(digital_macro_html);
						$('#digitalmacrosetpcf8574span').attr('hidden', true);
					} else if(macro_type == 2) {
						var analog_macro_html = "<ul>" +
								"<li>Macro Name:</li>" +
								"<li><input type='text' id='analogmacrosetmacroname' placeholder='5 Character Max'></li>" +
								"<li>Watch Pin</li>" +
								"<li><input type='number' id='analogmacrosetwatchpin' placeholder='Watch Pin'></li>" +
								"<li>Watch State</li>" +
								"<li><input type='number' id='analogmacrosetwatchstate' placeholder='Watch State'></li>" +
								"<li>Greater Than, Less Than, Equal To</li>" +
								"<li><select id='analogmacrosetgreaterlessequal'><option value='--'>--</option><option value='1'>Less Than</option><option value='2'>Greater Than</option><option value='3'>Equal To</option></select></li>" +
								"<li>Output Pin</li>" +
								"<li><input type='number' id='analogmacrosetoutputpin' placeholder='1-13, or 254 for PCF8574'></li>" +
								"<li>Output Action</li>" +
								"<li><input type='number' id='analogmacrosetoutputaction' placeholder='Output Action'></li>" +
								"<span id='analogmacrosetpcf8574span'><li>PCF8574 Device</li>" +
								"<li><input type='number' id='analogmacrosetpcf8574device' placeholder='PCF8574 Device #'></li>" +
								"<li>PCF8574 Pin</li>" +
								"<li><input type='number' id='analogmacrosetpcf8574pin' placeholder='PCF8574 Pin #'></li></span>" +								
							"</ul>";
						$('#macro_set_screen_div').html(analog_macro_html);
						$('#analogmacrosetpcf8574span').attr('hidden', true);
					} else if(macro_type == 3) {
						var ds1307_macro_html = "<ul>" +
								"<li>Macro Name</li>" +
								"<li><input type='text' id='ds1307macrosetmacroname' placeholder='5 Character Max'></li>" +
								"<li>Hour Start</li>" +
								"<li><input type='number' id='ds1307macrosethourstart' placeholder='Hour Start'></li>" +
								"<li>Minute Start</li>" +
								"<li><input type='number' id='ds1307macrosetminutestart' placeholder='Minute Start'></li>" +
								"<li>Hour Stop</li>" +
								"<li><input type='number' id='ds1307macrosethourstop' placeholder='Hour Stop'></li>" +
								"<li>Minute Stop</li>" +
								"<li><input type='number' id='ds1307macrosetminutestop' placeholder='Minute Stop'></li>" +
								"<li>Output Pin</li>" +
								"<li><input type='number' id='ds1307macrosetoutputpin' placeholder='Output Pin'></li>" +
								"<li>Output Action</li>" +
								"<li><input type='number' id='ds1307macrosetoutputaction' placeholder='Output Action'></li>" +
								"<li>Fade Time:</li>" +
								"<li><input type='number' id='ds1307macrosetfadetime' placeholder='In Minutes'></li>" +								
								"<li>Day Of Week:</li>" +
								"<li><select id='ds1307macrosetdow'><option value='0'>--</option><option value='1'>Sunday</option><option value='2'>Monday</option><option value='3'>Tuesday</option><option value='4'>Wednesday</option><option value='5'>Thursday</option><option value='6'>Friday</option><option value='7'>Saturday</option></select></li>" +										
								"<span id='ds1307macrosetpcf8574span'><li>PCF8574 Device</li>" +
								"<li><input type='number' id='ds1307macrosetpcf8574device' placeholder='PCF8574 Device #'></li>" +
								"<li>PCF8574 Pin</li>" +
								"<li><input type='number' id='ds1307macrosetpcf8574pin' placeholder='PCF8574 Pin #'></li></span>" +								
							"</ul>";
						$('#macro_set_screen_div').html(ds1307_macro_html);
						$('#ds1307macrosetpcf8574span').attr('hidden', true);
					} else if(macro_type == 4) {
						var ds18b20_macro_html = "<ul>" +
								"<li>Macro Name</li>" +
								"<li><input type='text' id='ds18b20macrosetmacroname' placeholder='5 Character Max'></li>" +
								"<li>Sensor Number:</li>" +
								"<li><input type='number' id='ds18b20macrosetsensornumber' placeholder='Sensor Number'></li>" +
								"<li>Greater Than, Less Than, Equal To</li>" +
								"<li><select id='ds18b20macrosetgreatlessequal'><option value='--'>--</option><option value='1'>Less Than</option><option value='2'>Greater Than</option><option value='3'>Equal To</option></select></li>" +
								"<li>Watch State:</li>" +
								"<li><input type='number' id='ds18b20macrosetwatchstate' placeholder='Watch State'></li>" +
								"<li>Output Pin</li>" +
								"<li><input type='number' id='ds18b20macrosetoutputpin' placeholder='1-13 or 254 for PCF8574'></li>" +
								"<li>Output Action</li>" +
								"<li><input type='number' id='ds18b20macrosetoutputaction' placeholder='Output Action'></li>" +
								"<span id='ds18b20macrosetpcf8574span'><li>PCF8574 Device</li>" +
								"<li><input type='number' id='ds18b20macrosetpcf8574device' placeholder='PCF8574 Device #'></li>" +
								"<li>PCF8574 Pin</li>" +
								"<li><input type='number' id='ds18b20macrosetpcf8574pin' placeholder='PCF8574 Pin #'></li></span>" +									
							"</ul>";
						$('#macro_set_screen_div').html(ds18b20_macro_html);
						$('#ds18b20macrosetpcf8574span').attr('hidden', true);
					}
				});
				
				$('#macro_set_screen_save').click(function() {
					var macro_type = $('#macro_set_screen_macro_type').val();
					var url;
					
					if(macro_type == 1) {
						url = arduino_addr + '/5/' + $('#digitalmacrosetmacroname').val() + '/1/' + $('#digitalmacrosetwatchpin').val() + '/' + $('#digitalmacrosetwatchstate').val() + '/' + $('#digitalmacrosetoutputpin').val() + '/' + $('#digitalmacrosetoutputaction').val() + '/' + $('#digitalmacrosetpcf8574device').val() + '/' +  $('#digitalmacrosetpcf8574pin').val() + '/?callback=?';
					}else if(macro_type == 2) {
						url = arduino_addr + '/5/' + $('#analogmacrosetmacroname').val() + '/2/' + $('#analogmacrosetwatchpin').val() + '/' + $('#analogmacrosetwatchstate').val() + '/' + $('#analogmacrosetgreaterlessequal').val() + '/' + $('#analogmacrosetoutputpin').val() + '/' + $('#analogmacrosetoutputaction').val() + '/' + $('#analogmacrosetpcf8574device').val() + '/' + $('#analogmacrosetpcf8574pin').val() + '/?callback=?';
					}else if(macro_type == 3) {
						url = arduino_addr + '/5/' + $('#ds1307macrosetmacroname').val() + '/3/' + $('#ds1307macrosethourstart').val() + '/' + $('#ds1307macrosetminutestart').val() + '/' + $('#ds1307macrosethourstop').val() + '/' + $('#ds1307macrosetminutestop').val() + '/' + $('#ds1307macrosetdow').val() + '/' + $('#ds1307macrosetoutputpin').val() + '/' + $('#ds1307macrosetoutputaction').val() + '/' + $('#ds1307macrosetfadetime').val() + '/' + $('#ds1307macrosetpcf8574device').val() + '/' + $('#ds1307macrosetpcf8574pin').val() + '/?callback=?';
					}else if(macro_type == 4) {
						url = arduino_addr + '/5/' + $('#ds18b20macrosetmacroname').val() + '/4/' + $('#ds18b20macrosetsensornumber').val() + '/' + $('#ds18b20macrosetgreatlessequal').val() + '/' + parseInt($('#ds18b20macrosetwatchstate').val()) + '/' + parseInt(((parseFloat($('#ds18b20macrosetwatchstate').val()) - parseInt($('#ds18b20macrosetwatchstate').val()))) * 100) + '/' + $('#ds18b20macrosetoutputpin').val() + '/' + $('#ds18b20macrosetoutputaction').val() + '/' + $('#ds18b20macrosetpcf8574device').val() + '/' + $('#ds18b20macrosetpcf8574pin').val() + '/?callback=?';
					}
		
		
//					alert(url);
					$.getJSON(url, function(data) {
										$.jGrowl("<br><br><br><center>Successfully set macro</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay});
									});		
				});

	            
	            
	            /**** ds1307 set page
	            */
					            
	            $('#saveds1307set').click(function() {
	            	var url = arduino_addr + '/9/' + $('#ds1307sethour').val() + '/' + $('#ds1307setminute').val() + '/' + $('#ds1307setsecond').val() + '/' + $('#ds1307setday').val() + '/' + $('#ds1307setdow').val() + '/' + $('#ds1307setmonth').val() + '/' + $('#ds1307setyear').val() + '/?callback=?';
	            	
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<center>" +
												"Hour: " + data.VALUE.H + "<br>" +
												"Minute: " + data.VALUE.M + "<br>" +
												"Second: " + data.VALUE.S + "<br>" +
												"Day: " + data.VALUE.D + "<br>" +
												"Day Of Week: " + data.VALUE.DOW + "<br>" +
												"Month: " + data.VALUE.MONTH + "<br>" +
												"Year: 20" + data.VALUE.Y + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});
	            });
	            
	            /****
	            * lcd setup page
	            */
	            var lcdsetup1224hourtimeformtoggleprev = 0;
	            $('#lcdsetup1224hourtime').click(function() {
	            	var value;
	            	if($('#lcdsetup1224hourtime').val() == 'on' && lcdsetup1224hourtimeformtoggleprev == 0) {
	            		value = 1;
	            		lcdsetup1224hourtimeformtoggleprev = 1;
	            	}else if(lcdsetup1224hourtimeformtoggleprev == 1) {
	            		lcdsetup1224hourtimeformtoggleprev = 0;
	            		value = 0;
	            	}
	            	var url = arduino_addr + '/12/' + value + '/?callback=?';
	            	
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>" +
												"Display Time As: " + data.VALUE.TIMEDISPLAYAS + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});
	            });
	            
	            var lcdsetupdisplaytimeformtoggleprev = 0;
	            $('#lcdsetupdisplaytime').click(function() {
	            	var value;
	            	if($('#lcdsetupdisplaytime').val() == 'on' && lcdsetupdisplaytimeformtoggleprev == 0) {
	            		value = 1;
	            		lcdsetupdisplaytimeformtoggleprev = 1;
	            	}else if(lcdsetupdisplaytimeformtoggleprev == 1) {
	            		lcdsetupdisplaytimeformtoggleprev = 0;
	            		value = 0;
	            	}
	            	var url = arduino_addr + '/16/' + value + '/?callback=?';
	            	
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>" +
												"Display Time On LCD: " + data.VALUE.DISPLAYTIME + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});
	            });
	            
	            
	            	            
				$('#lcdsetuptimepositionbutton').click(function() {
					var url = arduino_addr + '/17/' + $('#lcdsetuptimepositionrow').val() + '/' + $('#lcdsetuptimepositioncolumn').val() + '/?callback=?';
				
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>LCD Time Position<br>" +
												"Row: " + data.VALUE.ROW + "<br>" +
												"Column: " + data.VALUE.COL + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});				
				});
				
				
			
	            var lcdsetupds18b20displaysensor1formtoggleprev = 0;
	            $('#lcdsetupds18b20displaysensor1').click(function() {
	            	var value;
	            	if($('#lcdsetupds18b20displaysensor1').val() == 'on' && lcdsetupds18b20displaysensor1formtoggleprev == 0) {
	            		value = 1;
	            		lcdsetupds18b20displaysensor1formtoggleprev = 1;
	            	}else if(lcdsetupds18b20displaysensor1formtoggleprev == 1) {
	            		lcdsetupds18b20displaysensor1formtoggleprev = 0;
	            		value = 0;
	            	}
	            	var url = arduino_addr + '/20/' + value + '/?callback=?';
	            	
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>" +
												"Display Temp 1 Temperautre: " + data.VALUE.DISPLAYTEMP1 + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});
	            });	
	            
				$('#lcdsetupds18b20sensor1sensornumbutton').click(function() {
					var url = arduino_addr + '/19/' + $('#lcdsetupds18b20sensor1sensornum').val() + '/?callback=?';
				
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>Temp 1 Sensor #<br>" +
												"Sensor #: " + data.VALUE.SENSORNUM + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});				
				});	  
				
				$('#lcdsetupds18b20sensor1button').click(function() {
					var url = arduino_addr + '/18/' + $('#lcdsetupds18b20sensor1sensorpositionrow').val() + '/' + $('#lcdsetupds18b20sensor1sensorpositioncolumn').val() + '/?callback=?';
				
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>Temp 1 Position<br>" +
												"Row: " + data.VALUE.ROW + "<br>" +
												"Column: " + data.VALUE.COL + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});				
				});	
				
	            var lcdsetupds18b20displaysensor2formtoggleprev = 0;
	            $('#lcdsetupds18b20displaysensor2').click(function() {
	            	var value;
	            	if($('#lcdsetupds18b20displaysensor2').val() == 'on' && lcdsetupds18b20displaysensor2formtoggleprev == 0) {
	            		value = 1;
	            		lcdsetupds18b20displaysensor2formtoggleprev = 1;
	            	}else if(lcdsetupds18b20displaysensor2formtoggleprev == 1) {
	            		lcdsetupds18b20displaysensor2formtoggleprev = 0;
	            		value = 0;
	            	}
	            	var url = arduino_addr + '/23/' + value + '/?callback=?';
	            	
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>" +
												"Display Temp 2 Temperautre: " + data.VALUE.DISPLAYTEMP2 + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});
	            });	
	            
				$('#lcdsetupds18b20sensor2sensornumbutton').click(function() {
					var url = arduino_addr + '/22/' + $('#lcdsetupds18b20sensor2sensornum').val() + '/?callback=?';
				
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>Temp 2 Sensor #<br>" +
												"Sensor #: " + data.VALUE.SENSORNUM + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});				
				});	  
				
				$('#lcdsetupds18b20sensor2button').click(function() {
					var url = arduino_addr + '/21/' + $('#lcdsetupds18b20sensor2sensorpositionrow').val() + '/' + $('#lcdsetupds18b20sensor2sensorpositioncolumn').val() + '/?callback=?';
				
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>Temp 2 Position<br>" +
												"Row: " + data.VALUE.ROW + "<br>" +
												"Column: " + data.VALUE.COL + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});				
				});		
				
	            var lcdsetupds18b20displaysensor3formtoggleprev = 0;
	            $('#lcdsetupds18b20displaysensor3').click(function() {
	            	var value;
	            	if($('#lcdsetupds18b20displaysensor3').val() == 'on' && lcdsetupds18b20displaysensor3formtoggleprev == 0) {
	            		value = 1;
	            		lcdsetupds18b20displaysensor3formtoggleprev = 1;
	            	}else if(lcdsetupds18b20displaysensor3formtoggleprev == 1) {
	            		lcdsetupds18b20displaysensor3formtoggleprev = 0;
	            		value = 0;
	            	}
	            	var url = arduino_addr + '/26/' + value + '/?callback=?';
	            	
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>" +
												"Display Temp 3 Temperautre: " + data.VALUE.DISPLAYTEMP2 + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});
	            });	
	            
				$('#lcdsetupds18b20sensor3sensornumbutton').click(function() {
					var url = arduino_addr + '/25/' + $('#lcdsetupds18b20sensor3sensornum').val() + '/?callback=?';
				
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>Temp 3 Sensor #<br>" +
												"Sensor #: " + data.VALUE.SENSORNUM + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});				
				});	  
				
				$('#lcdsetupds18b20sensor3button').click(function() {
					var url = arduino_addr + '/24/' + $('#lcdsetupds18b20sensor3sensorpositionrow').val() + '/' + $('#lcdsetupds18b20sensor3sensorpositioncolumn').val() + '/?callback=?';
				
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>Temp 3 Position<br>" +
												"Row: " + data.VALUE.ROW + "<br>" +
												"Column: " + data.VALUE.COL + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});				
				});

	            var lcdsetupphdisplayph1formtoggleprev = 0;
	            $('#lcdsetupphdisplayph1').click(function() {
	            	var value;
	            	if($('#lcdsetupphdisplayph1').val() == 'on' && lcdsetupphdisplayph1formtoggleprev == 0) {
	            		value = 1;
	            		lcdsetupphdisplayph1formtoggleprev = 1;
	            	}else if(lcdsetupphdisplayph1formtoggleprev == 1) {
	            		lcdsetupphdisplayph1formtoggleprev = 0;
	            		value = 0;
	            	}
	            	var url = arduino_addr + '/30/' + value + '/?callback=?';

	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>" +
												"Display pH 1: " + data.VALUE.DISPLAYPH1 + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});
	            });	
	            
/*				$('#lcdsetupph1sensornumbutton').click(function() {
					var url = arduino_addr + '/25/' + $('#lcdsetupds18b20sensor3sensornum').val() + '/?callback=?';
				
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>Temp 3 Sensor #<br>" +
												"Sensor #: " + data.VALUE.SENSORNUM + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});				
				});	  */
				
				$('#lcdsetupph1positionbutton').click(function() {
					var url = arduino_addr + '/29/' + $('#lcdsetupph1positionrow').val() + '/' + $('#lcdsetupph1positioncolumn').val() + '/?callback=?';
				
	            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>pH 1 Position<br>" +
												"Row: " + data.VALUE.ROW + "<br>" +
												"Column: " + data.VALUE.COL + "<br>" +
												"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});				
				});				
			
	            /*
	            * read values page
	            */
	            $('#readvaluesdigitalbutton').click(function() {
	            	var url = arduino_addr + '/7/' + $('#readvaluesdigital').val() + '?callback=?';

	            	$.getJSON( url, function ( data ) {
	            						$.jGrowl("<br><br><br><center>digitalRead of pin: " + data.VALUE.PIN + " is: " + data.VALUE.VALUE + "</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
	            					});
	            });
	            $('#readvaluesanalogbutton').click(function() {
	            	var url = arduino_addr + '/8/' + $('#readvaluesanalog').val() + '?callback=?';

	            	$.getJSON( url, function ( data ) { 
	            						$.jGrowl("<br><br><br><center>analogRead of pin: " + data.VALUE.PIN + " is: " + data.VALUE.VALUE + "</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
	            					});
	            });	   
	            
	            $('#readvaluesds18b20button').click(function() {
	            	var url = arduino_addr + '/15/' + $('#readvaluesds18b20').val() + '?callback=?';

	            	$.getJSON( url, function ( data ) { 
	            						$.jGrowl("<br><br><br><center>Temperature for sensor " + data.VALUE.SENSOR + " is " + (data.VALUE.VALUE/100) + " degrees</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
	            					});
	            });	   	     
	            
	            $('#readvaluesdphbutton').click(function() {
	            	var url = arduino_addr + '/28/' + $('#readvaluesdph').val() + '?callback=?';

	            	$.getJSON( url, function ( data ) { 
	            						$.jGrowl("<br><br><br><center>pH Value is: " + (data.VALUE/100) + "</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
	            					});
	            });	   		            
	            /**********
	            * list macros page
	            */
	            $('#listmacroslink').click(function() {
	            	var i = 0;
	            	
	            	$('#listmacrosdiv').html("");
	            	
	            	for(var j=0; j<20; j++) {

	            		var url = arduino_addr + '/31/' + i + '/?callback=?';
	            			
						$.getJSON( url, function ( data ) { 
											if(data.VALUE == "OUTOFBOUNDS") {
												j = 200;
											} else {
												var returnData = new String(data.VALUE);
												var split = returnData.split(',');
												
												if(split[1] == 0) {
													j = 200;
												} else {
													if(split[2] == 1) {
														var macro_number = split[0];
														var watch_pin = split[3];
														var watch_state = split[4];
														var output_pin = split[5];
														var output_action = split[6];
														var output_time_on = split[7];
														var pcf8574_device = split[8];
														var pcf8574_pin = split[9];
														var name = split[16];
														
														var html = "<ul>";
														html += "<li>Macro Name: " + name + "</li>";
														if(output_pin != 254) {
															html += "<li>Watch Pin " + watch_pin + " for value " + watch_state + " if true turn Pin " + output_pin + " to " + output_action + "&nbsp;&nbsp;&nbsp;<input type='button' class='deletemacro' id='" + macro_number + "' value='X'></li>";
														} else {
															html += "<li>Watch Pin " + watch_pin + " for value " + watch_state + " if true turn Pin " + pcf8574_pin + " to " + output_action + " of PCF8574 device # " + pcf8574_device + "&nbsp;&nbsp;&nbsp;<input type='button' class='deletemacro' id='" + macro_number + "' value='X'></li>";
														}
														html += "</ul>";
														$(html).appendTo('#listmacrosdiv');
														
													} else if(split[2] == 2) {
														var macro_number = split[0];
														var watch_pin = split[3];
														var watch_state = (parseInt(split[15]) * 256) + parseInt(split[4]);
														var greater_less_equal = split[5];
														var output_pin = split[6];
														var output_action = split[7];
														var pcf8574_device = split[8];
														var pcf8574_pin = split[9]
														var name = split[16];
														
														var operator;
														if(greater_less_equal == 1) {
															operator = "Less Than";
														}else if(greater_less_equal == 2) {
															operator = "Greater Than";
														}else if(greater_less_equal == 3) {
															operator = "Equal To";
														}														
														
														var html = "<ul>";
														html += "<li>Macro Name: " + name + "</li>";
														if(output_pin != 254) {
															html += "<li>If Pin " + watch_pin + " is " + operator + " " + watch_state + ", turn Pin " + output_pin + " to " + output_action + "&nbsp;&nbsp;&nbsp;<input type='button' class='deletemacro' id='" + macro_number + "' value='X'></li>";
														} else {
															html += "<li>If Pin " + watch_pin + " is " + operator + " " + watch_state + ", turn Pin " + pcf8574_pin + " to " + output_action + " of PCF8574 Device # " + pcf8574_device + "&nbsp;&nbsp;&nbsp;<input type='button' class='deletemacro' id='" + macro_number + "' value='X'></li>";
														}
														html += "</ul>";
														$(html).appendTo('#listmacrosdiv');													
													} else if(split[2] == 3) {
														var macro_number = split[0];
														var hour_start = split[3];
														var minute_start = split[4];
														var hour_stop = split[5];
														var minute_stop = split[6];
														var dow = split[7];
														var output_pin = split[8];
														var output_action = split[9];
														var fade_time = split[10];
														var pcf8574_device = split[11];
														var pcf8574_pin = split[12];
														var name = split[16];
														
														var html = "<ul>";
														html += "<li>Macro Name: " + name + "</li>";
														if(output_pin <= 13) {
															html += "<li>At " + hour_start + ":" + minute_start + " turn pin " + output_pin + " to " + output_action + " until " + hour_stop + ":" + minute_stop + " on DOW " + dow + " with fade time of " + fade_time + " minutes&nbsp;&nbsp;&nbsp;<input type='button' class='deletemacro' id='" + macro_number + "' value='X'></li>";
														} else if(output_pin == 254){
															html += "<li>At " + hour_start + ":" + minute_start + " until " + hour_stop + ":" + minute_stop + " turn Pin " + pcf8574_pin + " to " + output_action + " of PCF8574 Device # " + pcf8574_device + "&nbsp;&nbsp;&nbsp;<input type='button' class='deletemacro' id='" + macro_number + "' value='X'></li>";
														}
														html += "</ul>";
														$(html).appendTo('#listmacrosdiv');													
													} else if(split[2] == 4) {
														var macro_number = split[0];
														var sensor_num = split[3];
														var greater_less_equal = split[4];
														var watch_state = parseFloat(split[5]) + (parseFloat(split[6]) / 100);
														var output_pin = split[7];
														var output_action = split[8];
														var pcf8574_device = split[9];
														var pcf8574_pin = split[10];
														var name = split[16];
														
														var operator;
														if(greater_less_equal == 1) {
															operator = "Less Than";
														}else if(greater_less_equal == 2) {
															operator = "Greater Than";
														}else if(greater_less_equal == 3) {
															operator = "Equal To";
														}
														
														var html = "<ul>";
														html += "<li>Macro Name: " + name + "</li>";
														if(output_pin <= 13) {
															html += "<li>If sensor #" + sensor_num + " is " + operator + " " + watch_state + " than turn pin " + output_pin + " to " + output_action + "&nbsp;&nbsp;&nbsp;<input type='button' class='deletemacro' id='" + macro_number + "' value='X'></li>";
														} else if(output_pin == 254) {
															html += "<li>If sensor #" + sensor_num + " is " + operator + " " + watch_state + " than turn pin " + pcf8574_pin + " to " + output_action + " of PCF8574 device #" + pcf8574_device + "&nbsp;&nbsp;&nbsp;<input type='button' class='deletemacro' id='" + macro_number + "' value='X'></li>";
														}
														$(html).appendTo('#listmacrosdiv');													
													}; 
												}
											}
										});
						i++;
	            	}
	            });
	            
	            $('.deletemacro').live('click', function() {
	            	var url = arduino_addr + '/32/' + this.id + '?callback=?';

	            	$.getJSON( url, function ( data ) { 
	            						$.jGrowl("<br><br><br><center>Successfully Deleted Macro</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
	            					});
	            });
	            
	            
	            
	            /**************
	            * reset page
	            **************/
	            $('#resetmacrobutton').click(function() {
					var url = arduino_addr + '/2?callback=?';
					
		            $.getJSON(url, function(data) {
		            					if(data.VALUE = "RESETMACROS") {
											$.jGrowl("<br><br><br><center>Successfully reset all macros</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 		            					
										} else {
											$.jGrowl("<br><br><br><center>Problem resetting macros</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
										}
		            });            	
	            });
	            
	            
	            /*******
	            * settings page
	            ********/
	            $('#savesettings').click(function() {
	            	db.transaction(function (tx) {
						var sql = "INSERT INTO addresses (ip) VALUES ('" + $('#settingsaddaddress').val() + "')";
						tx.executeSql(sql, [], function (tx, results) {
	
						});					
						alert("Settings Saved");
					});
					db.transaction(function (tx) {
						tx.executeSql('SELECT * FROM addresses', [], function (tx, results) {
						  var len = results.rows.length, i;
						  var towrite = "<ul>";
						  for (i = 0; i < len; i++) {
						  	towrite += "<li><input type='radio' name='arduinosavailable' value='" + results.rows.item(i).ip + "'>" + results.rows.item(i).ip + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='" + results.rows.item(i).ip + "' value='X'></li>";
						  }
						  towrite += "</ul>";
						  $('#arduinolist').html(towrite);
						});
					});					
	            });
	            $('#settingsbutton').click(function() {
	            	
					db.transaction(function (tx) {
						tx.executeSql('SELECT * FROM addresses', [], function (tx, results) {
						  var len = results.rows.length, i;
						  var towrite = "<ul>";
						  for (i = 0; i < len; i++) {
						  	towrite += "<li><input type='radio' name='arduinosavailable' value='" + results.rows.item(i).ip + "'>" + results.rows.item(i).ip + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='settingsbuttondeleteip' type='button' name='" + results.rows.item(i).ip + "' value='X'></li>";						  }
						  towrite += "</ul>";
						  $('#arduinolist').html(towrite);
						});
					});
					
	            	var url = arduino_addr + '/255?callback=?';
	            	
					$.getJSON( url, function ( data ) {
										$('#settingssetarduinoaddress').val(data.VALUE);
									});
									
	            	var url = arduino_addr + '/10?callback=?';
	            	
	            	$.getJSON(url, function(data) {
	            						var dow;
	            						if(data.VALUE.DOW == 1) {
	            							dow = "Sunday,";
	            						}else if(data.VALUE.DOW == 2) {
	            							dow = "Monday,";
	            						}else if(data.VALUE.DOW == 3) {
	            							dow = "Tuesday,";
	            						}else if(data.VALUE.DOW == 4) {
	            							dow = "Wednesday,";
	            						}else if(data.VALUE.DOW == 5) {
	            							dow = "Thursday,";
	            						}else if(data.VALUE.DOW == 6) {
	            							dow = "Friday,";
	            						}else if(data.VALUE.DOW == 7) {
	            							dow = "Saturday,";
	            						}
	            						var html = dow + " " + data.VALUE.H + ":" + data.VALUE.M + ":" + data.VALUE.S + " " + data.VALUE.MONTH + "/" + data.VALUE.D + "/" + data.VALUE.Y;
	            						$('#settingsdisplaycurrenttime').html(html);
									});									
									
									
									
	            });
	            
	            $('.settingsbuttondeleteip').live('click', function() {
	            	var inputname = this.name;
					db.transaction(function (tx) {
						var sql = "DELETE FROM addresses WHERE ip='" + inputname + "'";
						tx.executeSql(sql, [], function (tx, results) { });
						tx.executeSql('SELECT * FROM addresses', [], function (tx, results) {
						  var len = results.rows.length, i;
						  var towrite = "<ul>";
						  for (i = 0; i < len; i++) {
						  	towrite += "<li><input type='radio' name='arduinosavailable' value='" + results.rows.item(i).ip + "'>" + results.rows.item(i).ip + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='settingsbuttondeleteip' type='button' name='" + results.rows.item(i).ip + "' value='X'></li>";						  }
						  towrite += "</ul>";
						  $('#arduinolist').html(towrite);
						});						

					});
	            });
	            
	            $('#arduinolist').click(function() {
					db.transaction(function (tx) {
						var sql = "UPDATE addresses SET current='0'";
						var sql1 = "UPDATE addresses SET current='1' WHERE ip='" + $('input:radio[name=arduinosavailable]:checked').val() + "'";
						tx.executeSql(sql, [], function (tx, results) {});
						tx.executeSql(sql1, [], function (tx, results) {});						
					});	            
	            	arduino_addr = "http://" + $('input:radio[name=arduinosavailable]:checked').val();
	            });
	            
	            $('#settingsdiscover1wire').click(function() {
					var url = arduino_addr + '/13?callback=?';
					
		            $.getJSON(url, function ( data ) {
										$.jGrowl("<br><br><br><center>Discovered " + data.VALUE.DEVICES + " 1Wire devices</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});            
	            });
	            
	            $('#settings1wireaddress').click(function() {
					var url = arduino_addr + '/14?callback=?';
					
		            $.getJSON(url, function ( data ) {
										$.jGrowl("<center>1Wire Addresses<br>" + 
													data.VALUE[0].VALUE + "<br>" +
													data.VALUE[1].VALUE + "<br>" +
													data.VALUE[2].VALUE + "<br>" +
													data.VALUE[3].VALUE + "<br>" +
													data.VALUE[4].VALUE + "<br>" +
													"</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay + 5000}); 
									});            
	            });	            
	            
	            
	            
	            $('#settingssetarduinoaddressbutton').click(function() {
	            	var url = arduino_addr + '/1/' + $('#settingssetarduinoaddress').val() + '?callback=?';
	            	
					$.getJSON( url, function ( data ) {
										$.jGrowl("<br><br><br><center>Arduino address has been set to: " + data.VALUE.ADDRSET + "</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});
	            });
	            
	            $('#settings_form_digital_pin_mode_input').click(function() {
	            	if($('#settings_form_digital_pin_mode_pin_number').val() >= 0) {
		            	var url = arduino_addr + '/3/' + $('#settings_form_digital_pin_mode_pin_number').val() + '/0?callback=?';
	            	
		            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>Set pin: " + data.VALUE.PIN + " to mode: " + data.VALUE.MODE + "</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});
	            	}
	            });
	            $('#settings_form_digital_pin_mode_output').click(function() {
	            	if($('#settings_form_digital_pin_mode_pin_number').val() >= 0) {
		            	var url = arduino_addr + '/3/' + $('#settings_form_digital_pin_mode_pin_number').val() + '/1?callback=?';
	            	
		            	$.getJSON(url, function(data) { 
										$.jGrowl("<br><br><br><center>Set pin: " + data.VALUE.PIN + " to mode: " + data.VALUE.MODE + "</center>", { header: '   ', theme: jgrowlTheme, life: jgrowlDelay}); 
									});
	            	}
	            });	            
	        });
        </script>
    </head>
<body>

	<div id="jqt">
		<div id="home" class="current">
                <div class="toolbar">
                    <h1>MacroDuino</h1>
                    <a class="button slideup" id="settingsbutton" href="#settings">Settings</a>
                </div>
                <ul class="rounded">
                	<li class="forward"><a href="#statusscreen">Status</a></li>
                	<li class="forward"><a href="#control_screen" id="control_screen_home_href">Control</a></li>
                	<li class="forward"><a href="#macro_set_screen" id="macro_set_screen_home_href">Set Macros</a></li>
					<li class="forward"><a href="#readvalues">Read Values</a></li>
					<li class="forward"><a href="#listmacros" id="listmacroslink">List Macros</a></li>
                </ul>


                <div class="info">
                    <p>Add this page to your home screen to view the custom icon, startup screen, and full screen mode.</p>
                </div>
            </div>
            
            <div id="statusscreen">
                <div class="toolbar">
                    <h1>Status</h1>
                    <a class="back" href="#home">Home</a>
                    <a class="button" id="statusscreensave" href="#"></a>
                </div>
                
				<span id="statusscreendiv"></span>
				<ul><li><center><a href="#statusscreenadditem">Add Item</a></center></li></ul>
            </div>            
            
            <div id="statusscreenadditem">
                <div class="toolbar">
                    <h1>Add Item</h1>
                    <a class="back" href="#statusscreen">Back</a>
                    <a class="button" id="statusscreenadditemsave" href="#">Save</a>
                </div>
                
                <ul>
					<li>
							Status Item: <select id="statusscreenadditemsstatusitem">
								<option value="">--</option>
								<option value="dpin">Digital Pin</option>
								<option value="apin">Analog Pin</option>
								<option value="ph">pH Reading</option>
								<option value="ds18b20">DS18B20 Temp</option>
							</select>
					</li>
					<li>
						<input type="text" id="statusscreenadditempinnum" placeholder="Pin #">
					</li>
					<li>
						<input type="text" id="statusscreenadditenname" placeholder="Name">
					</li>
				</ul>		

            </div>  
            
            <div id="control_screen">
                <div class="toolbar">
                    <h1>Control</h1>
                    <a class="button" href="#home">Home</a>
                </div>
                
				<div id="control_screen_div"></div>
				<ul><li><center><a href="#control_screen_additem">Add Item</a></center></li></ul>
            </div>   
            
            <div id="control_screen_additem">
                <div class="toolbar">
                    <h1>Add Item</h1>
                    <a class="back" href="#home">Back</a>
                    <a class="button" href="#" id="control_screen_form_save">Save</a>
                </div>
                
                <ul>
                	<li>
                		Name: <input type="text" id="control_screen_form_name">
                	</li>
                	<li>
						Type: <select id="control_screen_form_type_select">
								<option value="">--</option>
								<option value="digital">Digital</option>
								<option value="pcf8574">PCF8574</option>
							  </select>
					</li>
					<li>
						Pin: <input type="number" id="control_screen_form_pin_number">
					</li>
					<span id="control_screen_form_enable_pwm_span">
						<li>
							Enable PWM: <span class='toggle'><input type='checkbox' id="control_screen_form_enable_pwm_toggle"></span>
						</li>
					</span>
					<span id="control_screen_form_pcf8574_device_span">
						<li>
							Device #: <input type="number" id="control_screen_form_pcf8574_device">
						</li>
					</span>				
				</ul>
                <div class="info">
                	<p>To add a pin you would like to control just fill out this information. That pin (as well as the name) will show up in the control screen.
                    <p>Device # is only used with PCF8574.</p>
                </div>                
            </div>   
            
            <div id="macro_set_screen">
                <div class="toolbar">
                    <h1>Macro Set</h1>
                    <a class="back" href="#home">Home</a>
                    <a class="button" href="#" id="macro_set_screen_save">Save</a>
                </div>
                
                
                <ul>
					<li>
							Macro Type: <select id="macro_set_screen_macro_type">
								<option value="">--</option>
								<option value="1">Digital Input</option>
								<option value="2">Analog Input</option>
								<option value="3">RTC Input</option>
								<option value="4">DS18B20 Input</option>
							</select>
					</li>
				</ul>
				<div id="macro_set_screen_div"></div>
				
				<div class="info">
					<p>
						Macro type refers to which type of input you would like to use.
					</p>
				</div>
            </div>              
            
            <div id="readvalues">
                <div class="toolbar">
                    <h1>Read Values</h1>
                    <a class="back" href="#home">Home</a>
                    <a class="button" id="readvaluesget" href="#">Get!</a>
                </div>
                
                <ul>
                	<li>
                		Digital Pin Value: 
                		<input type="text" id="readvaluesdigital" placeholder="Read Digital Pin">
                		<input type="button" id="readvaluesdigitalbutton" value="Get Digital Reading">
                	</li>
                	<li>
                		Analog Pin Value: 
                		<input type="text" id="readvaluesanalog" placeholder="Read Analog Pin">
                		<input type="button" id="readvaluesanalogbutton" value="Get Analog Reading">		
                	</li>
                	<li>
                		DS18B20 Value: 
                		<input type="text" id="readvaluesds18b20" placeholder="DS18B20 #">
                		<input type="button" id="readvaluesds18b20button" value="Get DS18B20 Reading">		
                	</li>   
                	<li>
                		pH Value: 
                		<input type="text" id="readvaluesdph" placeholder="pH Pin #">
                		<input type="button" id="readvaluesdphbutton" value="Get pH Reading">		
                	</li>                  	
                </ul>
            </div>            
            
            <div id="listmacros">
                <div class="toolbar">
                    <h1>Macro List</h1>
                    <a class="back" href="#home">Home</a>
                </div>
                
                <p>
                	This takes at least 30 seconds to complete so don't send any other commands during this process!
                </p>
				<div id="listmacrosdiv"></div>
            </div>             


            <div id="settings">
                <div class="toolbar">
                    <h1>Settings</h1>
                    <a class="back" href="#home">Home</a>
                    <a class="button" id="savesettings" href="#">Save</a>
                </div>
                
                <ul class="rounded">
					<li>Add Address <input type="text" id="settingsaddaddress"></li>
				</ul>
				<h1>
					Select Arduino
				</h1>
				<span id="arduinolist"></span>
				
				<ul class="rounded">
					<li><input type="button" id="settingsdiscover1wire" value="Discover 1Wire Devices"></li>
					<li><input type="button" id="settings1wireaddress" value="Stored 1Wire Addresses"></li>
				</ul>
				
				<h1>
					Set Arduino Address
				</h1>
				<ul class="rounded">
					<li><input type="number" id="settingssetarduinoaddress"></li>
					<li><input type="button" id="settingssetarduinoaddressbutton" value="Set Arduino Address"></li>
				</ul>
				<h1>
					Current Time
				</h1>
				<ul class="rounded">
					<li><div id="settingsdisplaycurrenttime"></div></li>
				</ul>
				
				<h1>
					Change Digital Pin Modes
				</h1>

				<ul class="rounded">
					<li>
						
						Select Pin <select id="settings_form_digital_pin_mode_pin_number">
							<option value="">--</option>
							<option value="0">0</option>
							<option value="1">1</option>						
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
							<option value="13">13</option>
						</select>
					</li>
					<li>
						Pinmode
					</li>
					<li>
						<input type="radio" name="settings_form_digital_pin_mode_radio" id="settings_form_digital_pin_mode_input" value="0" title="Input" />
						<br>
						<input type="radio" name="settings_form_digital_pin_mode_radio" id="settings_form_digital_pin_mode_output" value="1" title="Output" />
					</li>
				</ul>
				
				<ul>
					<li class="forward"><a href="#ds1307set">Set DS1307</a></li>
					<li class="forward"><a href="#lcdsetup">LCD Setup</a></li>
                    <li class="forward"><a href="#reset">Reset</a></li>					
				</ul>
				
                
				<div class="info">
				</div>
            </div>
            
            <div id="ds1307set">
                <div class="toolbar">
                    <h1>DS1307 Set</h1>
                    <a class="back" href="#home">Home</a>
                    <a class="button" id="saveds1307set">Save</a>
                </div>
                
                <ul>
                	<li>Hour</li>
                	<li><input type="number" id="ds1307sethour" placeholder="24 Hour Clock"></li>
                	<li>Minute</li>
                	<li><input type="number" id="ds1307setminute" placeholder="Minute"></li>
                	<li>Second</li>
                	<li><input type="number" id="ds1307setsecond" placeholder="Second"></li>
                	<li>Day</li>
                	<li><input type="number" id="ds1307setday" placeholder="Day"></li>
                	<li>Day Of Week</li>
                	<li>
                		<select id="ds1307setdow">
                			<option value="--">--</option>
                			<option value="1">Sunday</option>
                			<option value="2">Monday</option>
                			<option value="3">Tuesday</option>
                			<option value="4">Wednesday</option>
                			<option value="5">Thursday</option>
                			<option value="6">Friday</option>
                			<option value="7">Saturday</option>
                		</select>
                	</li>
                	<li>Month</li>
                	<li>
                		<select id="ds1307setmonth">
                			<option value="--">--</option>
                			<option value="1">January</option>
                			<option value="2">February</option>
                			<option value="3">March</option>
                			<option value="4">April</option>
                			<option value="5">May</option>
                			<option value="6">June</option>
                			<option value="7">July</option>
                			<option value="8">August</option>
                			<option value="9">September</option>
                			<option value="10">October</option>
                			<option value="11">November</option>
                			<option value="12">December</option>
                		</select>
                	</li>
                	<li>Year</li>
                	<li>
                		<select id="ds1307setyear">
                			<option value="--">--</option>
                			<option value="10">2010</option>
                			<option value="11">2011</option>
                			<option value="12">2012</option>
                			<option value="13">2013</option>
                			<option value="14">2014</option>
                			<option value="15">2015</option>
                		</select>
                	</li>                	
                </ul>
                
				<div class="info">
				</div>               
            </div>              
            
            <div id="lcdsetup">
                <div class="toolbar">
                    <h1>LCD Setup</h1>
                    <a class="back" href="#home">Home</a>
                </div>
                
                <h1>
                	Time Settings:
            	</h1>                
                <ul>
                	<li>Display 12H Time: <span class="toggle"><input id="lcdsetup1224hourtime" type="checkbox" /></span></li>
                	<li>Display Time On LCD: <span class="toggle"><input id="lcdsetupdisplaytime" type="checkbox" /></span></li>
					<li>Time Position</li>
            		<li>Row: <input type="number" id="lcdsetuptimepositionrow"></li>
            		<li>Column: <input type="number" id="lcdsetuptimepositioncolumn"></li>
            		<li><input type="button" id="lcdsetuptimepositionbutton" value="Set Time Position"></li>
            	</ul>
            	
            	<h1>
            		DS18B20 Temp1 Settings:
            	</h1>
            	<ul>
            		<li>Display Sensor 1: <span class="toggle"><input id="lcdsetupds18b20displaysensor1" type="checkbox" /></span></li>
            		<li>Sensor 1 Sensor #</li>
            		<li><input type="number" id="lcdsetupds18b20sensor1sensornum"></li>
            		<li><input type="button" id="lcdsetupds18b20sensor1sensornumbutton" value="Set Sensor 1 Sensor #">
            		<li>Sensor 1 Position:</li>
            		<li>Row: <input type="number" id="lcdsetupds18b20sensor1sensorpositionrow"></li>
            		<li>Column: <input type="number" id="lcdsetupds18b20sensor1sensorpositioncolumn"></li>
            		<li><input type="button" id="lcdsetupds18b20sensor1button" value="Set Sensor 1 Position"></li>
            	</ul>
            	<h1>
            		DS18B20 Temp2 Settings:
            	</h1>
            	<ul>
            		<li>Display Sensor 2: <span class="toggle"><input id="lcdsetupds18b20displaysensor2" type="checkbox" /></span></li>
            		<li>Sensor 1 Sensor #</li>
            		<li><input type="number" id="lcdsetupds18b20sensor2sensornum"></li>
            		<li><input type="button" id="lcdsetupds18b20sensor2sensornumbutton" value="Set Sensor 1 Sensor #">
            		<li>Sensor 1 Position:</li>
            		<li>Row: <input type="number" id="lcdsetupds18b20sensor2sensorpositionrow"></li>
            		<li>Column: <input type="number" id="lcdsetupds18b20sensor2sensorpositioncolumn"></li>
            		<li><input type="button" id="lcdsetupds18b20sensor2button" value="Set Sensor 2 Position"></li>
            	</ul>
            	<h1>
            		DS18B20 Temp3 Settings:
            	</h1>
            	<ul>
            		<li>Display Sensor 3: <span class="toggle"><input id="lcdsetupds18b20displaysensor3" type="checkbox" /></span></li>
            		<li>Sensor 1 Sensor #</li>
            		<li><input type="number" id="lcdsetupds18b20sensor3sensornum"></li>
            		<li><input type="button" id="lcdsetupds18b20sensor3sensornumbutton" value="Set Sensor 1 Sensor #">
            		<li>Sensor 1 Position:</li>
            		<li>Row: <input type="number" id="lcdsetupds18b20sensor3sensorpositionrow"></li>
            		<li>Column: <input type="number" id="lcdsetupds18b20sensor3sensorpositioncolumn"></li>
            		<li><input type="button" id="lcdsetupds18b20sensor3button" value="Set Sensor 3 Position"></li>
            	</ul> 
            	<h1>
            		pH 1 Settings:
            	</h1>
            	<ul>
            		<li>Display pH 1: <span class="toggle"><input id="lcdsetupphdisplayph1" type="checkbox" /></span></li>
            		<!--<li>Sensor 1 Sensor #</li>
            		<li><input type="number" id="lcdsetupph1sensornum"></li>
            		<li><input type="button" id="lcdsetupph1sensornumbutton" value="Set pH 1 Sensor #">-->
            		<li>pH 1 Position:</li>
            		<li>Row: <input type="number" id="lcdsetupph1positionrow"></li>
            		<li>Column: <input type="number" id="lcdsetupph1positioncolumn"></li>
            		<li><input type="button" id="lcdsetupph1positionbutton" value="Set pH 1 Position"></li>
            	</ul>              	
            </div>            
            
            <div id="reset">
                <div class="toolbar">
                    <h1>Reset Settings</h1>
                    <a class="back" href="#home">Home</a>
                </div>
                
                <center><input type="button" id="resetmacrobutton" value="Reset Macros"></center>
            </div>            
</body>
</html>