<script type="text/javascript">
	window.polygonFlag = false;
	window.overlay_markers = [];
	window.overlay_markers_index = [];
	window.search_flag = false;
	window.oldZoomLevel = null;
	window.oldCenter = null
	window.overlays = [];
	window.searchcounter = 0;
	window.overlaycounter = 0;
	function createData(custom_cond){
		var all_data = {};
		var counter = 0;
		$(".filters .autocomplete").each(function(k,i){
			if($(i).val().length > 0 && (!custom_cond || (custom_cond && $(i).closest('.section-wrapper').find('.accordion-section-header-checkbox:checked').length))){
				if($.isEmptyObject(all_data[$(i).attr('name')]))
					all_data[$(i).attr('name')] = {};
				all_data[$(i).attr('name')]['input_type'] = 'autocomplete';
				all_data[$(i).attr('name')][counter] = $(i).val();
				counter++;
			}
		});
		counter = 0;
		$(".filters select :selected").each(function(k,i){
			if($(i).val() != 'default' && (!custom_cond || (custom_cond && $(i).closest('.section-wrapper').find('.accordion-section-header-checkbox:checked').length))){
				if($.isEmptyObject(all_data[$(i).parent('select').attr('name')]))
					all_data[$(i).parent('select').attr('name')] = {};
				all_data[$(i).parent('select').attr('name')]['input_type'] = 'dropdown';
				all_data[$(i).parent('select').attr('name')][counter] = $(i).val();
				counter++;
			}
		});
		counter = 0;
		$(".filters .range.min").each(function(k,i){
			if($(i).val().length > 0 && (!custom_cond || (custom_cond && $(i).closest('.section-wrapper').find('.accordion-section-header-checkbox:checked').length))){
				if($.isEmptyObject(all_data[$(i).attr('name')]))
					all_data[$(i).attr('name')] = {};
				all_data[$(i).attr('name')]['input_type'] = 'range';
				all_data[$(i).attr('name')][counter] = {};
				all_data[$(i).attr('name')][counter]['min'] = $(i).val();
				counter++;
			}
		});
		counter = 0;
		$(".filters .range.max").each(function(k,i){
			if($(i).val().length > 0 && (!custom_cond || (custom_cond && $(i).closest('.section-wrapper').find('.accordion-section-header-checkbox:checked').length)) && ($(i).attr('name') in all_data)){
				all_data[$(i).attr('name')]['input_type'] = 'range';
				all_data[$(i).attr('name')][counter]['max'] = $(i).val();
				counter++;
			}
		});
		counter = 0;
		$(".filters .number").each(function(k,i){
			if($(i).val().length > 0 && (!custom_cond || (custom_cond && $(i).closest('.section-wrapper').find('.accordion-section-header-checkbox:checked').length))){
				if($.isEmptyObject(all_data[$(i).attr('name')]))
					all_data[$(i).attr('name')] = {};
				all_data[$(i).attr('name')]['input_type'] = 'number';
				all_data[$(i).attr('name')][counter] = $(i).val();
				counter++;
			}
		});
		counter = 0;
		$(".filters .rangemultiselect :selected").each(function(k,i){
			if($(i).val() != 'default' && (!custom_cond || (custom_cond && $(i).closest('.section-wrapper').find('.accordion-section-header-checkbox:checked').length))){
				if($.isEmptyObject(all_data[$(i).parent('select').attr('name')]))
					all_data[$(i).parent('select').attr('name')] = {};
				all_data[$(i).parent('select').attr('name')]['input_type'] = 'rangemultiselect';
				all_data[$(i).parent('select').attr('name')][counter] = $(i).val();
				counter++;
			}
		});
		if($(".filters .array").val().length > 0){
			all_data['MainData.MND_ID'] = {};
			all_data['MainData.MND_ID']['input_type'] = 'array';
			all_data['MainData.MND_ID']['0'] = $(".filters .array").val();
		}
		if($(".filters .infoWindowSearch").val().length > 0){
			all_data['MainData.MND_ID'] = {};
			all_data['MainData.MND_ID']['0'] = $(".filters .infoWindowSearch").val();
			all_data['MainData.MND_ID']['input_type'] = 'exact';
		}
		return all_data;
	}
	var init = function(){
		var infoWindow = null, marker = "", first_flag = true;
		$(".autocomplete").autocomplete({
			source: function(request, response){
				var elem = this.element;
				$.ajax({
					url: "{{ route('map.autocomplete') }}",
					data: {
						name:$(elem).attr('name'),
						val:request.term
					},
					success: function(data){
						response(data);
					}
				});
			},
			minLength: 3,
			select: function(event, ui) {
				$(this.element).val(ui.item.value);
			},
			change: function( event, ui ){
				if(ui.item == null){
					$(this).val('');
				}
			},
			response: function(event, ui) {
				if(ui.content.length === 0){
					var noResult = { value:"",label:"No results found" };
                	ui.content.push(noResult);
				}
	        }
		});

		function _isNumber(data){
			if(data.length > 0 && data.match(/^[0-9]+$/) == null)
				return true;
			else return false;
		}
		function validations(data){
			if($.isEmptyObject(data))
				return true;
			var error_flag = false, error_flag_2 = false;
			$(".filters .range.min").each(function(k,i){
				var min_val = $(i).val();
				var max_val = $(i).siblings('.filters .range.max').val();
				if(min_val.length > 0 && max_val.length == 0){
					alert('Please fill up the max range.');
					error_flag = true;
					return false;
				}else if(max_val.length > 0 && min_val.length == 0){
					alert('Please fill up the max range.');
					error_flag = true;
					return false;
				}else if(error_flag = _isNumber(min_val)){
					alert('Max range should be a number.');
					return false;
				}else if(error_flag = _isNumber(max_val)){
					alert('Max range should be a number.');
					return false;
				}else if(parseInt(min_val) > parseInt(max_val)){
					alert('Min range should be lesser than Max range.');
					error_flag = true;
					return false;
				}
			});
			$(".filters .number").each(function(k,i){
				if(error_flag_2 = _isNumber($(i).val())){
					alert($(i).attr('field-name')+' should be a number.');
					return false;
				}
			});
			return error_flag || error_flag_2;
		}
		function createDataWithToken(){
			var all_data = createData(true);
			all_data["_token"] = "{{ csrf_token() }}";
			return all_data;
		}
		function searchEventsTasks(elem){
			var s_name = $(elem).closest('.section-item-wrapper').attr("section-name")+$(elem).closest('.section-item-wrapper').attr("unique-name"),
			data = createData(false), found_flagger = false;
			for(var i in data){
				if(i.indexOf(".") !== -1){
					var s_name_data = i.substring(0, i.indexOf("."))+(i.indexOf(".") != i.lastIndexOf(".")?i.substring(i.lastIndexOf(".")+1):'');
					if(s_name_data == s_name && !$.isEmptyObject(data[i])){
						$(elem).closest('.section-wrapper').find(".accordion-section-header-checkbox").prop("checked", true);
						found_flagger = true;
					}
				}
			}
			if(!found_flagger)
				$(elem).closest('.section-wrapper').find(".accordion-section-header-checkbox").prop("checked", false);
		}
		function initSearchEvents(){
			$("input").on({
				keyup: function(e){
					var keyCode = e.keyCode || e.which;
	  				if(keyCode == 9 && $(e.currentTarget).is('.range.max')){
	  					e.preventDefault();
	  					return false;
	  				}
					if($(e.currentTarget).is('.range.max') && $(e.currentTarget).val().length > 0 && $(e.currentTarget).siblings('.min').val().length == 0)
						$(e.currentTarget).siblings('.min').val('0');
					if($(e.currentTarget).is('.range.max') && $(e.currentTarget).val().length == 0)
						$(e.currentTarget).siblings('.min').val('');
					searchEventsTasks(e.currentTarget);
				}, change: function(e){
					if($(e.currentTarget).is('.range.max') && $(e.currentTarget).val().length > 0 && $(e.currentTarget).siblings('.min').val().length == 0)
						$(e.currentTarget).siblings('.min').val('0');
					if($(e.currentTarget).is('.range.max') && $(e.currentTarget).val().length == 0)
						$(e.currentTarget).siblings('.min').val('');
					searchEventsTasks(e.currentTarget);
				}
			});
			$("select").on("change", function(e){
				if(typeof $(e.currentTarget).attr("data-combined-fid") !== "undefined"){
					$(e.currentTarget).closest('.combined-fields-parent-pilot').siblings('.combined-fields-parent-sub').each(function(k,i){
						if($(i).find('.combined-fields-sub').attr("data-combined-fid") == $(e.currentTarget).attr("data-combined-fid"))
							$(i).find('.combined-fields-sub').val($(e.currentTarget).val());
					});
				}
				searchEventsTasks(e.currentTarget);
			});
		}
		function uploadButtonEvents(){
			if($(".image-upload input[type='submit']").length){
				$(".image-upload input[type='file']").off();
				$(".image-upload input[type='file']").on("change", function(){
					$(this).closest('.image-upload').find('input[type="submit"]').prop('disabled', false);
				});
				$(".image-upload input[type='submit']").off();
				$(".image-upload input[type='submit']").on("click", function(e){
					e.preventDefault();
					if($(e.currentTarget).closest(".image-upload").find("input[type='file']").val().length){
						var form_data = new FormData(), files = $(e.currentTarget).closest('.image-upload').find('input[type="file"]')[0].files;
						for(index in files){
							if($.isNumeric(index))
								form_data.append(index, files[index]);
						}
						form_data.append('_token', $("._token").val());
						form_data.append('MND_ID', $('#listings-table').dataTable().api().data()[$(e.currentTarget).closest('tr').index()]['MND_ID']);
						$.ajax({
							url: "{{ route('map.search.upload') }}",
							type: 'POST',
							data: form_data,
							processData: false,
							contentType: false,
							success: function(data){
								$(e.currentTarget).closest(".image-upload").html(data);
								$('#listings-table').DataTable().columns.adjust();
								uploadButtonEvents();
							},
							fail: function(err){
								alert("Failed to upload file.");
								console.log(err);
							},
							error: function(err){
								alert("Error upon uploading the file.")
								console.log(err);
							}
						});
					}
				});
			}
		}
		function fieldNames(fields){
			var to_return = '';
			switch(fields){
				case "Community": to_return = 'Community'; break;
				case "LastUpdated": to_return = 'Last Updated'; break;
				case "Status": to_return = 'ACTIVE'; break;
				case "Management": to_return = 'Management Company'; break;
				case "Address": to_return = 'Apartment Address'; break;
				case "City": to_return = 'City'; break;
				case "Zip": to_return = 'Zip'; break;
				case "PhoneNo": to_return = 'Phone #'; break;
				case "FaxNo": to_return = 'Fax #'; break;
				case "FelonyCase": to_return = 'Felony Case'; break;
				case "FelonyDUIMonths": to_return = 'Felony DUI (Months)'; break;
				case "FelonyDrugMonths": to_return = 'Felony Drug (Months)'; break;
				case "FelonyMarijuanaMonths": to_return = 'Felony Marijuana (Months)'; break;
				case "FelonyTheftMonths": to_return = 'Felony Theft (Months)'; break;
				case "FelonyWeaponMonths": to_return = 'Felony Weapon (Months)'; break;
				case "FelonyVCAPMonths": to_return = 'Felony VCAP (Months)'; break;
				case "FelonyNotes": to_return = 'Felony (Notes)'; break;
				case "MisdemeanorCase": to_return = 'Misdemeanor Case'; break;
				case "MisdemeanorDUIMonths": to_return = 'Misdemeanor DUI (Months)'; break;
				case "MisdemeanorDrugMonths": to_return = 'Misdemeanor Drug (Months)'; break;
				case "MisdemeanorMarijuanaMonths": to_return = 'Misdemeanor Marijuana (Months)'; break;
				case "MisdemeanorTheftMonths": to_return = 'Misdemeanor Theft (Months)'; break;
				case "MisdemeanorWeaponMonths": to_return = 'Misdemeanor Weapon (Months)'; break;
				case "MisdemeanorVCAPMonths": to_return = 'Misdemeanor VCAP (Months)'; break;
				case "MisdemeanorNotes": to_return = 'Misdemeanor (Notes)'; break;
				case "RentalIssueAgeMonths": to_return = 'Rental Issue Age Months'; break;
				case "RentalIssueMax": to_return = 'Rental Issue Max'; break;
				case "RentalIssueAmount": to_return = 'Rental Issue Amount ($)'; break;
				case "CreditScore": to_return = 'Credit Score'; break;
				case "CreditFriendly": to_return = 'Credit Friendly'; break;
				case "CreditBureau": to_return = 'Credit Bureau (TU,EQ,EX,3)'; break;
				case "CreditSystem": to_return = 'Credit System'; break;
				case "OpenBankruptcy": to_return = 'Open Bankruptcy'; break;
				case "DisBankruptcyAgeMonths": to_return = 'Dis Bankruptcy Age (Months)'; break;
				case "IncomeRequirement": to_return = 'Income Requirement'; break;
				case "FoodStampsYesNo": to_return = 'Food Stamps Yes/No'; break;
				case "CompanyLetterHeadYesNo": to_return = 'Company Letterhead Yes/No'; break;
				case "NonLetterHeadLetterYesNo": to_return = 'Non-Letterhead Letter Yes/No'; break;
				case "LengthofJobMonths": to_return = 'Length of Job (Months)'; break;
				case "Section8": to_return = 'Section 8'; break;
				case "HOMINC": to_return = 'HOM INC'; break;
				case "BiltmoreProperties": to_return = 'Biltmore Properties'; break;
				case "RapidRehousing": to_return = 'Rapid Rehousing'; break;
				case "HUDVASH": to_return = 'HUD VASH'; break;
				case "Visa": to_return = 'VISA'; break;
				case "NoSSNo": to_return = 'No SS#'; break;
				case "MexID": to_return = 'Mex ID'; break;
				case "ITINNo": to_return = 'ITIN#'; break;
				case "WD": to_return = 'W/D'; break;
				case "SXSIncluded": to_return = 'SXS Included'; break;
				case "StackIncluded": to_return = 'Stack Included'; break;
				case "SXSHookup": to_return = 'SXS Hookup'; break;
				case "StackHookUp": to_return = 'Stack Hookup'; break;
				case "OnSiteFacility": to_return = 'On Site Facility'; break;
				case "LaundryNotes": to_return = 'Laundry Notes'; break;
				case "PetWeightLimit": to_return = 'Pet Weight Limit'; break;
				case "RestrictedBreed": to_return = 'Restricted Breed?'; break;
				case "NumberOfPetMax": to_return = 'Number of Pets Max'; break;
				case "Utilities": to_return = 'Utilitities'; break;
				case "APS": to_return = 'APS'; break;
				case "SRP": to_return = 'SRP'; break;
				case "INCL": to_return = 'INCL'; break;
				case "GAS": to_return = 'GAS'; break;
				case "SPriceRANGE": to_return = 'S - Price RANGE'; break;
				case "SPriceLOW": to_return = 'S - Price LOW'; break;
				case "SPriceHIGH": to_return = 'S - Price HIGH'; break;
				case "1X1PriceRANGE": to_return = '1X1 - Price RANGE'; break;
				case "1X1PriceLOW": to_return = '1X1 Price LOW'; break;
				case "1X1PriceHIGH": to_return = '1X1 Price HIGH'; break;
				case "1X1DENPrice": to_return = '1X1 + DEN - Price'; break;
				case "2X1PriceRANGE": to_return = '2X1 - Price RANGE'; break;
				case "2X1PriceLOW": to_return = '2X1 - Price LOW'; break;
				case "2X1PriceHIGH": to_return = '2X1 - Price HIGH'; break;
				case "2X2PriceRANGE": to_return = '2X2 - Price RANGE'; break;
				case "2X2PriceLOW": to_return = '2X2 - Price LOW'; break;
				case "2X2PriceHIGH": to_return = '2X2 - Price HIGH'; break;
				case "2BRDENPrice": to_return = '2BR + DEN - Price'; break;
				case "3X1Price": to_return = '3X1 - Price'; break;
				case "3X2PriceRANGE": to_return = '3X2 - Price RANGE'; break;
				case "3X2PriceLOW": to_return = '3X2 - Price LOW'; break;
				case "3X2PriceHIGH": to_return = '3X2 - Price HIGH'; break;
				case "4X2Price": to_return = '4X2 - Price'; break;
				case "SqS": to_return = "S - Sq'"; break;
				case "Sq1X1": to_return = "1X1 - Sq'"; break;
				case "Sq1X1DEN": to_return = "1X1 + DEN - Sq'"; break;
				case "Sq2X1": to_return = "2X1 - Sq'"; break;
				case "Sq2X2": to_return = "2X2 - Sq'"; break;
				case "Sq2BRDEN": to_return = "2BR + DEN - Sq'"; break;
				case "Sq3X1": to_return = "3X1 - sq'"; break;
				case "Sq3X2": to_return = "3X2 - Sq'"; break;
				case "Sq4X2": to_return = "4X2 - Sq'"; break;
				case "Garage": to_return = 'Garage'; break;
				case "Fitness": to_return = 'Fitness'; break;
				case "Handicap": to_return = 'Handicap'; break;
				case "Gated": to_return = 'Gated'; break;
				case "Furnished": to_return = 'Furnished'; break;
				case "CableIncl": to_return = 'Cable Incl'; break;
				case "Sublevel": to_return = 'Sublevel'; break;
				case "Occupant": to_return = 'Occupant'; break;
				case "ShortestTerm": to_return = 'Shortest Term (months)'; break;
				default: to_return = ''; break;
			}
			return to_return;
		}
		function initialTable(){
			$('#listings-table').DataTable({
				columns: [
	            	{"data": "MND_ID", "name": "MainData.MND_ID", "className": "MND_ID", "visible": false},
	            	{
	            		width: '50%',
	            		sortable: false,
	            	},
	            	{
	            		width: '50%',
	            		sortable: false,
	            	}
	            ]
			});
		}
		function destroyTable(){
			$('#listings-table').DataTable().destroy();
		}
		function initDataTable(){
			$('#listings-table').DataTable({
	            processing: true,
	            serverSide: true,
	            //scrollX: true,
	            scrollY: true,
	            pageLength: 10,
	            ajax: {
	            	"url": "{{ route('map.search.completeSearch') }}",
	            	"type": "POST",
	            	"data": function(data){
	            		return $.extend(createDataWithToken(), data);
	            	}
	            },
	            columns: [
	            	{"data": "MND_ID", "name": "MainData.MND_ID", "className": "MND_ID", "visible": false},
	            	{
	            		width: '50%',
	            		sortable: false,
	            		"render": function( data, type, full, meta ){
	            			var to_return = "";
	            			if(!$.isEmptyObject(full)){
		            			var img_src = JSON.parse($("<div/>").html(full.IMG_SRC).text());
		            			to_return = '<div class="search-item-infos">';
								to_return += '<div class="col-md-12">';
								if(!$.isEmptyObject(img_src)){
									@if(Auth::user()->is_admin)
									to_return += "<div class='image-upload'><img src='"+img_src['img_0']['img_thumb'].replace(/\\\//g, "/")+"' /><form enctype='multipart/form-data' method='POST' action=''><input type='file' class='form-control' name='image' accept='image/*' /><input type='submit' value='Change' disabled/></form></div>";
									@else
										to_return += "<div class='image-upload'><img src='"+img_src['img_0']['img_thumb'].replace(/\\\//g, "/")+"' /></div>";
									@endif
								}else{
									@if(Auth::user()->is_admin)
									to_return += "<div class='image-upload'><img src='images/Small.jpg' /><form enctype='multipart/form-data' method='POST' action='' accept='image/*'><input type='file' class='form-control'  name='image' accept='image/*' /><input type='submit' value='Upload' disabled /></form></div>";
									@else
										to_return += "<div class='image-upload'><img src='images/Small.jpg' /></div>";
									@endif
								}
								to_return += '</div>';
								to_return += '<div class="col-md-6 text-right">Apartment Name:</div><div class="col-md-6 text-left">'+full.MND_Community+'</div>';
								to_return += '<div class="col-md-6 text-right">Apartment Address:</div><div class="col-md-6 text-left">'+full.MND_Address+'</div>';
								to_return += '<div class="col-md-6 text-right">Phone #:</div><div class="col-md-6 text-left">'+full.MND_PhoneNo+'</div>';
								to_return += '<div class="col-md-6 text-right">Fax #:</div><div class="col-md-6 text-left">'+full.MND_FaxNo+'</div>';
								to_return += '<div class="col-md-6 text-right">Management Company:</div><div class="col-md-6 text-left">'+full.MND_Management+'</div>';
								to_return += '</div>';
							}else
								to_return = "";
							return to_return;
						}
					},
					{
						width: '50%',
						sortable: false,
						"render": function( data, type, full, meta ){
							var search_data = createData(true), to_return = '<div class="search-item-infos">', table_field = [], table_abbrv = '', field_name = '', filter_val = '', field_val = '', felony_flag = false, range_flag = false, msd_flag = false;//, mnd_flag = false;
							/*if(!$.isEmptyObject(search_data)){
								for(items in search_data){
									range_flag = false;
									table_field = items.split(".");
									table_abbrv = table_field[1].split("_")[0];
									field_val = full[table_field[1]];
									if(search_data[items]['input_type'] == 'range'){
										for(search_items in search_data[items]){
											var field_val_range = field_val.split('-');
											if(field_val_range.length == 1){
												for(var i = search_data[items][search_items]['min']; i <= search_data[items][search_items]['max']; i++){
													if($.isNumeric(field_val) && parseInt(field_val) <= i){
														range_flag = true;
														break;
													}
												}
											}else if(field_val_range.length == 2){
												if($.isNumeric(field_val_range[0]) && $.isNumeric(field_val_range[1])){
													var temp_val = [];
													for(key in search_data[items]){
														if(!isNaN(key))
															temp_val.push(search_data[items][key]);
													}
													field_val_range[0] = parseInt(field_val_range[0]);
													field_val_range[1] = parseInt(field_val_range[1]);
													if((field_val_range[0] <= temp_val[0].max && field_val_range[1] >= temp_val[0].max) || (field_val_range[1] <= temp_val[0].max && field_val_range[1] >= temp_val[0].min))
														range_flag = true;
												}
											}
											if(range_flag)
												break;
										}
									}
									var search_val = [], field_flag = false;
									field_name = table_field[1].split("_")[1];
									if(!range_flag){
										for(key in search_data[items]){
											if(!isNaN(key))
												search_val.push(search_data[items][key]);
										}
										field_val = field_val == 'CBC'?'0':field_val;
										if($.inArray(field_val, search_val) !== -1 || (search_data[items]['input_type'] == 'number' && parseInt(field_val) <= parseInt(search_val[0])))
											field_flag = true;
										if(!felony_flag && table_abbrv == 'FLN' && field_flag)
											felony_flag = true;
										if(!msd_flag && table_abbrv == 'MSD' && field_flag)
											msd_flag = true;
										if(table_field[1] != 'MND_ID' && field_flag){
											field_name = fieldNames(field_name);
											to_return += '<div class="col-md-6 fields-container"><div class="col-md-6 text-right fields-wrapper">'+field_name+':</div><div class="col-md-6 text-left fields-wrapper">'+field_val+'</div></div>';
										}
									}else if(range_flag){
										for(key in search_data[items]){
											if(!isNaN(key))
												search_val.push(search_data[items][key]);
										}
										field_val = field_val == 'CBC'?'0':field_val;
										if($.inArray(field_val, search_val) !== -1)
											field_flag = true;
										if(!felony_flag && table_abbrv == 'FLN' && field_flag)
											felony_flag = true;
										if(range_flag || field_flag){
											field_name = table_field[1].split("_")[1];
											field_name = fieldNames(field_name);
											to_return += '<div class="col-md-6 fields-container"><div class="col-md-6 text-right fields-wrapper">'+field_name+':</div><div class="col-md-6 text-left fields-wrapper">'+field_val+'</div></div>';
										}
									}
								}
								if(felony_flag && full.FLN_FelonyNotes.length > 0)
									to_return += '<div class="col-md-6"><div class="col-md-6 text-right">Felony Notes:</div><div class="col-md-6 text-left">'+full.FLN_FelonyNotes+'</div></div>';
								if(msd_flag && full.MSD_MisdemeanorNotes.length > 0)
									to_return += '<div class="col-md-6"><div class="col-md-6 text-right">Misdemeanor Notes:</div><div class="col-md-6 text-left">'+full.MSD_MisdemeanorNotes+'</div></div>';
								//if(mnd_flag && full.MND_LaundryNotes.length > 0)
									//to_return += '<div class="col-md-6"><div class="col-md-6 text-right">Laundry Notes:</div><div class="col-md-6 text-left">'+full.MND_LaundryNotes+'</div></div>';
							}else
								to_return = "";*/

							var exclude = ['MND_Community','MND_Address','MND_PhoneNo','MND_FaxNo','MND_Management','MND_ID','IMG_SRC'];
							var field_name = '';
							for(items in full){
								field_name = items.split("_")[1];
								field_name = fieldNames(field_name);
								if($.inArray(items, exclude) === -1 && full[items].length > 0){
									to_return += '<div class="col-md-6"><div class="col-md-6 text-right">'+field_name+':</div><div class="col-md-6 text-left">'+full[items]+'</div></div>';
								}
							}
							return to_return + "</div>";
							//return to_return;
						}
	                }
				],
				drawCallback: function(settings) {
					if(!$.isEmptyObject(createData(true))){
						$('#listings-table').DataTable().columns.adjust();
						uploadButtonEvents();
					}
					$('.dataTables_filter label > input').attr('disabled',true);
				}
	        });
		}
		function mapSetCenter(){
			if(window.oldZoomLevel !== null && window.oldCenter !== null){
				window.map_0.setZoom(window.oldZoomLevel);
				window.map_0.setCenter(window.oldCenter);
			}
		}
		function rangeMultiselectUpdate(elem, exclude){
			var all_data = createDataWithToken();
			if(exclude != null){
				delete all_data[exclude];
			}
			all_data['range_with_multiple'] = {};
			var __counter = 0;
			for(item in elem){
				all_data['range_with_multiple'][__counter] = {name:$(elem[item]).attr('name'), val:$(elem[item]).val()};
				__counter++;
			}
			$.ajax({
				url: "{{ route('map.rangemax') }}",
				type: "POST",
				data: all_data,
				success: function(data){
					if(typeof data === 'object'){
						for(items in data){
							var elem_rangemultiselect = $("."+items);
							var selected_val = $(elem_rangemultiselect).val();
							$(elem_rangemultiselect).empty();
							for(item in data[items]){
								$(elem_rangemultiselect).append("<option value='"+data[items][item]+"' "+($.inArray(data[items][item],selected_val)!==-1?'selected':'')+">"+(data[items][item].length==0?"&nbsp;":data[items][item])+"</option>");
							}
							$(elem_rangemultiselect).selectMultiple('refresh');
						}
					}
					//$('.filters .rangemultiselect').selectMultiple('refresh');
				}
			});
		}
		function rangeMultiselectAllUpdate(exclude){
			var elements = [];
			$('.filters .rangemax').each(function(k,i){
				elements[k] = $(i);
			});
			rangeMultiselectUpdate(elements, exclude);
		}
		initSearchEvents();
		initialTable();

		$(".filters .rangemax").autocomplete({
			source: function(request, response){
				var elem = this.element;
				rangeMultiselectUpdate([elem], request.term);
			},
			minLength: 0
		});

		$("#filters-search-b").on('click', function(e){
			if(first_flag){
				first_flag = false;
				destroyTable();
			}
			if(!window.polygonFlag)
				$(".array").val('');
			$(".infoWindowSearch").val('');
			if(window.searchcounter > 0 || window.overlays.length > 0){
				window.oldZoomLevel = window.map_0.getZoom();
				window.oldCenter = window.map_0.getCenter();
			}
			window.searchcounter++;
			var all_data = createData(true);
			//if($.isEmptyObject(all_data)){
			//	$("#filters-reset-b").trigger('click');
			//	return false;
			//}
			if(!validations(all_data)){
				$.ajax({
					url: "{{ route('map.search') }}",
					data: all_data,
					success: function(data){
						$(".map-wrapper .map").html(data);
						window.search_flag = false;
					}
				});
				if(!$.fn.DataTable.isDataTable('#listings-table')){
					initDataTable();
				}else{
					$("#listings-table").DataTable().search('').columns().search('').draw();
					$('#listings-table').DataTable().ajax.reload();
				}
				all_data = createDataWithToken();
				$.ajax({
					url: "{{ route('map.search.filterswithsearch') }}",
					type: "POST",
					data: all_data,
					success: function(data){
						for(items in data){
							var selected_val = $("."+items).val();
							$("."+items).empty();
							for(item in data[items]){
								$("."+items).append("<option value='"+item+"' "+($.inArray(item,selected_val)!==-1?'selected':'')+">"+(data[items][item].length==0?"&nbsp;":data[items][item])+"</option>");
							}
							if($("."+items+" option").length == 0)
								$("."+items).closest(".section-item-wrapper").hide();
							else
								$("."+items).closest(".section-item-wrapper").show();
							$("."+items).selectMultiple('refresh');
						}
					}
				});
			}else
				alert('Please fill in the search.');
		});
		$("#filters-reset-b").on('click', function(){
			window.oldZoomLevel = window.map_0.getZoom();
			window.oldCenter = window.map_0.getCenter();
			$("#search-form")[0].reset();
			$('.filters .selectmultioption').selectMultiple('deselect_all');
			$('.filters .rangemultiselect').selectMultiple('deselect_all');
			$('.filters .rangemultiselect').empty();
			$('.filters .rangemultiselect').selectMultiple('refresh');
			$(".infoWindowSearch").val('');
			$(".array").val('');
			$("#polygonsWrapper .polygon-items span").each(function(k,i){
				$(i).trigger('click');
			});
			first_flag = true;
			$.ajax({
				url: "{{ route('map.search.index') }}",
				success: function(data){
					$(".map-wrapper .map").html(data);
					mapSetCenter();
					if($("#listings-table_wrapper").length){
						$("#listings-table_wrapper").remove();
						$("#details").html('<table id="listings-table" class="display nowrap cell-border stripe hover" style="width:100%"><thead><tr><th>ID</th><th>Property Info</th><th>Searched Infos</th></tr></thead></table>');
						initialTable();
					}
				}
			});
			$.ajax({
				url: "{{ route('map.search.clearfilters') }}",
				type: "POST",
				data: {'_token': "{{ csrf_token() }}"},
				success: function(data){
					for(items in data){
						$("."+items).empty();
						for(item in data[items]){
							$("."+items).append("<option value='"+item+"'>"+(data[items][item].length==0?"&nbsp;":data[items][item])+"</option>");
						}
						$(".section-item-wrapper:not(.combined-fields-parent-sub)").show();
						$("."+items).selectMultiple('refresh');
					}
				}
			});

		});
		$('a[href="#search"]').on('shown.bs.tab', function(e){
			$(".filters .infoWindowSearch").val('');
			if(window.search_flag){
				if(typeof google !== 'undefined'){
					google.maps.event.trigger(map_0, 'resize');
					if(typeof window.markers !== 'undefined' && window.markers.length > 0){
						//marker = window.markers[0];
						//map_0.setCenter(new google.maps.LatLng( marker.getPosition().lat(), marker.getPosition().lng() ) );
						mapSetCenter();
					}
				}
				$("#listings-table_filter input").val('');
				if(window.polygonFlag)
					$("#listings-table").DataTable().columns(0).search(window.overlay_markers.join('|'), true).draw();
				else{
					$(".array").val('');
					$("#listings-table").DataTable().search('').columns().search('').draw();
				}
				window.search_flag = false;
			}
			if(typeof google !== 'undefined'){
				google.maps.event.trigger(map_0, 'resize');
			}
		});
		$('a[href="#details"]').on('shown.bs.tab', function(e){
			//$("#listings-table").DataTable().search('').columns().search('').draw();
			$('#listings-table').DataTable().columns.adjust();
		});
		/*window.onerror = function(message, url, lineNumber) {
			console.log(message);
			console.log(url);
			console.log(lineNumber);
			return true;
		};*/
		/*for(var i = 0; i < 10; i++){
			if($("#accordion-group-"+i).length){
				$("#accordion-group-"+i).accordion({
					collapsible: true,
					active: false,
					header: 'h3'
				});
			}
		}*/
		for(var i = 0; i <= 11; i++){
			if($("#accordion-"+i).length){
				$("#accordion-"+i).accordion({
					collapsible: true,
					active: false,
					header: 'h4',
					heightStyle: "content"
				});
			}
		}
		$('.nav-tabs').scrollingTabs({
			disableScrollArrowsOnFullyScrolled: true  
		});
		/*$('.filters .range.max').on('keyup', function(e){
			if($(this).val().length > 0 && $(this).siblings('.min').val().length == 0)
				$(this).siblings('.min').val('0');
		});
		$('.filters .range.max').on('keyup', function(e){
			if($(this).val().length == 0)
				$(this).siblings('.min').val('');
		});*/
		$('.filters .selectmultioption').selectMultiple({
			afterSelect: function(data){
				rangeMultiselectAllUpdate(null);
			},
			afterDeselect: function(data){
				rangeMultiselectAllUpdate(null);
			}
		});
		$('.filters .rangemultiselect').selectMultiple({
			afterSelect: function(data){
				var elements = [], current_id = $(this.$element).attr('id');
				$('.filters .rangemultiselect').each(function(k,i){
					if($(i).attr('id') != current_id){
						elements[k] = $(i).siblings('.rangemax');
					}
				});
				rangeMultiselectUpdate(elements);
			},
			afterDeselect: function(data){
				rangeMultiselectAllUpdate($(this.$element).attr('name'));
			}
		});
		$('.dataTables_filter label > input').attr('disabled',true);
	}
	$(document).ready(function(){
		init();
	});
</script>