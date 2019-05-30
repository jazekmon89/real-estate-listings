<script type="text/javascript">
	$(function() {
        $('#listings-table').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
            	"url": "{{ route('listings.list') }}",
            	"type": "POST",
            	"data": {
            		"_token": "{{ csrf_token() }}"
            	}
            },
            columns: [
            	{
            		bSearchable: false,
	                sortable: false,
	                "render": function ( data, type, full, meta ) {
						return '<input type="checkbox" name="recordChecklists" id="'+full.MND_ID+'" class="MultipleRecordsChecklist"><input type="hidden" name="'+full.MND_ID+'" class="id" value="'+full.MND_ID+'">';
	                 }
	            },
				{"data": "MND_ID", "name": "MainData.MND_ID", "className": "MND_ID"},
				{"data": "MND_Community", "name": "MainData.MND_Community"},
				{"data": "MND_LastUpdated", "name": "MainData.MND_LastUpdated"},
				{"data": "MND_Status", "name": "MainData.MND_Status"},
				{"data": "MND_Management", "name": "MainData.MND_Management"},
				{"data": "MND_Address", "name": "MainData.MND_Address"},
				{"data": "MND_City", "name": "MainData.MND_City"},
				{"data": "MND_Zip", "name": "MainData.MND_Zip"},
				{"data": "MND_PhoneNo", "name": "MainData.MND_PhoneNo"},
				{"data": "MND_FaxNo", "name": "MainData.MND_FaxNo"},
				{"data": "FLN_FelonyCase", "name": "Felony.FLN_FelonyCase"},
				{"data": "FLN_FelonyDUIMonths", "name": "Felony.FLN_FelonyDUIMonths"},
				{"data": "FLN_FelonyDrugMonths", "name": "Felony.FLN_FelonyDrugMonths"},
				{"data": "FLN_FelonyMarijuanaMonths", "name": "Felony.FLN_FelonyMarijuanaMonths"},
				{"data": "FLN_FelonyTheftMonths", "name": "Felony.FLN_FelonyTheftMonths"},
				{"data": "FLN_FelonyWeaponMonths", "name": "Felony.FLN_FelonyWeaponMonths"},
				{"data": "FLN_FelonyVCAPMonths", "name": "Felony.FLN_FelonyVCAPMonths"},
				{"data": "FLN_FelonyNotes", "name": "Felony.FLN_FelonyNotes"},
				{"data": "MSD_MisdemeanorCase", "name": "Misdemeanor.MSD_MisdemeanorCase"},
				{"data": "MSD_MisdemeanorDUIMonths", "name": "Misdemeanor.MSD_MisdemeanorDUIMonths"},
				{"data": "MSD_MisdemeanorDrugMonths", "name": "Misdemeanor.MSD_MisdemeanorDrugMonths"},
				{"data": "MSD_MisdemeanorMarijuanaMonths", "name": "Misdemeanor.MSD_MisdemeanorMarijuanaMonths"},
				{"data": "MSD_MisdemeanorTheftMonths", "name": "Misdemeanor.MSD_MisdemeanorTheftMonths"},
				{"data": "MSD_MisdemeanorWeaponMonths", "name": "Misdemeanor.MSD_MisdemeanorWeaponMonths"},
				{"data": "MSD_MisdemeanorVCAPMonths", "name": "Misdemeanor.MSD_MisdemeanorVCAPMonths"},
				{"data": "MSD_MisdemeanorNotes", "name": "Misdemeanor.MSD_MisdemeanorNotes"},
				{"data": "RNT_RentalIssueAgeMonths", "name": "RentalIssue.RNT_RentalIssueAgeMonths"},
				{"data": "RNT_RentalIssueMax", "name": "RentalIssue.RNT_RentalIssueMax"},
				{"data": "RNT_RentalIssueAmount", "name": "RentalIssue.RNT_RentalIssueAmount"},
				{"data": "CRD_CreditScore", "name": "Credit.CRD_CreditScore"},
				{"data": "CRD_CreditFriendly", "name": "Credit.CRD_CreditFriendly"},
				{"data": "CRD_CreditBureau", "name": "Credit.CRD_CreditBureau"},
				{"data": "CRD_CreditSystem", "name": "Credit.CRD_CreditSystem"},
				{"data": "MND_OpenBankruptcy", "name": "MainData.MND_OpenBankruptcy"},
				{"data": "MND_DisBankruptcyAgeMonths", "name": "MainData.MND_DisBankruptcyAgeMonths"},
				{"data": "MND_IncomeRequirement", "name": "MainData.MND_IncomeRequirement"},
				{"data": "MND_FoodStampsYesNo", "name": "MainData.MND_FoodStampsYesNo"},
				{"data": "MND_CompanyLetterHeadYesNo", "name": "MainData.MND_CompanyLetterHeadYesNo"},
				{"data": "MND_NonLetterHeadLetterYesNo", "name": "MainData.MND_NonLetterHeadLetterYesNo"},
				{"data": "MND_LengthofJobMonths", "name": "MainData.MND_LengthofJobMonths"},
				{"data": "MND_Section8", "name": "MainData.MND_Section8"},
				{"data": "MND_HOMINC", "name": "MainData.MND_HOMINC"},
				{"data": "MND_BiltmoreProperties", "name": "MainData.MND_BiltmoreProperties"},
				{"data": "MND_RapidRehousing", "name": "MainData.MND_RapidRehousing"},
				{"data": "MND_HUDVASH", "name": "MainData.MND_HUDVASH"},
				{"data": "MND_Visa", "name": "MainData.MND_Visa"},
				{"data": "MND_NoSSNo", "name": "MainData.MND_NoSSNo"},
				{"data": "MND_MexID", "name": "MainData.MND_MexID"},
				{"data": "MND_ITINNo", "name": "MainData.MND_ITINNo"},
				{"data": "MND_WD", "name": "MainData.MND_WD"},
				{"data": "MND_SXSIncluded", "name": "MainData.MND_SXSIncluded"},
				{"data": "MND_StackIncluded", "name": "MainData.MND_StackIncluded"},
				{"data": "MND_SXSHookup", "name": "MainData.MND_SXSHookup"},
				{"data": "MND_StackHookUp", "name": "MainData.MND_StackHookUp"},
				{"data": "MND_OnSiteFacility", "name": "MainData.MND_OnSiteFacility"},
				{"data": "MND_LaundryNotes", "name": "MainData.MND_LaundryNotes"},
				{"data": "MND_PetWeightLimit", "name": "MainData.MND_PetWeightLimit"},
				{"data": "MND_RestrictedBreed", "name": "MainData.MND_RestrictedBreed"},
				{"data": "MND_NumberOfPetMax", "name": "MainData.MND_NumberOfPetMax"},
				{"data": "MND_Utilities", "name": "MainData.MND_Utilities"},
				{"data": "MND_APS", "name": "MainData.MND_APS"},
				{"data": "MND_SRP", "name": "MainData.MND_SRP"},
				{"data": "MND_INCL", "name": "MainData.MND_INCL"},
				{"data": "MND_GAS", "name": "MainData.MND_GAS"},
				{"data": "PRC_SPriceRANGE", "name": "Price.PRC_SPriceRANGE"},
				{"data": "PRC_SPriceLOW", "name": "Price.PRC_SPriceLOW"},
				{"data": "PRC_SPriceHIGH", "name": "Price.PRC_SPriceHIGH"},
				{"data": "PRC_1X1PriceRANGE", "name": "Price.PRC_1X1PriceRANGE"},
				{"data": "PRC_1X1PriceLOW", "name": "Price.PRC_1X1PriceLOW"},
				{"data": "PRC_1X1PriceHIGH", "name": "Price.PRC_1X1PriceHIGH"},
				{"data": "PRC_1X1DENPrice", "name": "Price.PRC_1X1DENPrice"},
				{"data": "PRC_2X1PriceRANGE", "name": "Price.PRC_2X1PriceRANGE"},
				{"data": "PRC_2X1PriceLOW", "name": "Price.PRC_2X1PriceLOW"},
				{"data": "PRC_2X1PriceHIGH", "name": "Price.PRC_2X1PriceHIGH"},
				{"data": "PRC_2X2PriceRANGE", "name": "Price.PRC_2X2PriceRANGE"},
				{"data": "PRC_2X2PriceLOW", "name": "Price.PRC_2X2PriceLOW"},
				{"data": "PRC_2X2PriceHIGH", "name": "Price.PRC_2X2PriceHIGH"},
				{"data": "PRC_2BRDENPrice", "name": "Price.PRC_2BRDENPrice"},
				{"data": "PRC_3X1Price", "name": "Price.PRC_3X1Price"},
				{"data": "PRC_3X2PriceRANGE", "name": "Price.PRC_3X2PriceRANGE"},
				{"data": "PRC_3X2PriceLOW", "name": "Price.PRC_3X2PriceLOW"},
				{"data": "PRC_3X2PriceHIGH", "name": "Price.PRC_3X2PriceHIGH"},
				{"data": "PRC_4X2Price", "name": "Price.PRC_4X2Price"},
				{"data": "SQ_SqS", "name": "Sq.SQ_SqS"},
				{"data": "SQ_Sq1X1", "name": "Sq.SQ_Sq1X1"},
				{"data": "SQ_Sq1X1DEN", "name": "Sq.SQ_Sq1X1DEN"},
				{"data": "SQ_Sq2X1", "name": "Sq.SQ_Sq2X1"},
				{"data": "SQ_Sq2X2", "name": "Sq.SQ_Sq2X2"},
				{"data": "SQ_Sq2BRDEN", "name": "Sq.SQ_Sq2BRDEN"},
				{"data": "SQ_Sq3X1", "name": "Sq.SQ_Sq3X1"},
				{"data": "SQ_Sq3X2", "name": "Sq.SQ_Sq3X2"},
				{"data": "SQ_Sq4X2", "name": "Sq.SQ_Sq4X2"},
				{"data": "MND_Garage", "name": "MainData.MND_Garage"},
				{"data": "MND_Fitness", "name": "MainData.MND_Fitness"},
				{"data": "MND_Handicap", "name": "MainData.MND_Handicap"},
				{"data": "MND_Gated", "name": "MainData.MND_Gated"},
				{"data": "MND_Furnished", "name": "MainData.MND_Furnished"},
				{"data": "MND_CableIncl", "name": "MainData.MND_CableIncl"},
				{"data": "MND_Sublevel", "name": "MainData.MND_Sublevel"},
				{"data": "MND_Occupant", "name": "MainData.MND_Occupant"},
				{"data": "MND_ShortestTerm", "name": "MainData.MND_ShortestTerm"},
				{"data": "LOC_Latitude", "name": "Location.LOC_Latitude"},
				{"data": "LOC_Longitude", "name": "Location.LOC_Longitude"},
				{
					data: "LOC_Confidence",
					"render": function ( data, type, full, meta ){
						if(full.LOC_Confidence < 1)
							return '<h4 class="text-danger">'+full.LOC_Confidence+'</h4>';
						return full.LOC_Confidence;
					}
				},
				{
	                sortable: false,
	                "render": function ( data, type, full, meta ) {
						return '<a id='+full.MND_ID+' class="btn btn-primary editor_edit" role="button">Edit</a>';
					}
	            },
			], 
			scrollY: "500px",
			scrollX: true,
			scrollCollapse: true,
			fixedColumns: {
				leftColumns: 1,
	            rightColumns: 2
	        },
	        order: [[104, 'asc']],
            drawCallback: function(settings) {
            	if($('h4.text-danger').length && $('.alert-danger').length == 0){
            		var _rec = "record"+($('h4.text-danger').length > 1?"s":""), _a = ($('h4.text-danger').length == 1?"a":"");
            		$.notify({icon:'glyphicon glyphicon-warning-sign', message: "You have "+_a+" "+_rec+" having invalid Latitude and Longitude (Location Confidence is -1 or 0)! This is due to the system's script failed to retrieve the location using the record's address, from google maps api. Please manually update the affected "+_rec+"."},{type: 'danger', delay: 0});
            	}
            }
        });
        $('#listings-table').on( 'click', '.editor_edit', function (e) {
        	e.preventDefault();
        	var id = $(this).attr('id');
        	var row = $('#listings-table').find("input[name^='"+id+"']").parent().parent(); 
        	var top_row = $('#listings-table .table-heading').get(0);
        	var total = $(top_row).find('th').length;
        	var html = '';
        	$('#ItemPopup .modal-title').text('Edit User id '+id);
        	html += '<form method="POST" id="records-update">';
    		$.each( $(top_row).find('th') , function( key, value ) {	
				var input_value = $(row).children('td:eq("'+key+'")').text();
				var th_id = $(this).attr('id');
				if (key >= 2 && key != total - 1) {
			  		html += '<div class="row"><label class="col-md-5 text-left">'+ $(value).text() +'</label><div class="col-md-7"><input type="text" data-flag="0" value="'+input_value+'" name="'+th_id+'" class="form-control input-listing-update"></div></div>';
			  	}
			});
			html += '<div class="row"><div class="col-md-12 text-right"><input type="hidden" name="id" value="'+id+'"><input type="submit" class="update btn btn-default" id="'+id+'" value="Update"></div></div>';
			html += '</form>';
    		$('#ItemPopup .modal-body').html(html);
			$('#ItemPopup').modal('show');
			$('input[name=MND_LastUpdated]').attr('disabled', true);
        });
        $(document).on('submit', 'form#records-update', function(e) {
			e.preventDefault();
			$('input[name=MND_LastUpdated]').attr('disabled', true); 
			$('.input-listing-update').attr('readonly', true);
			var form = $(this).serialize();
			$.ajax({
				url: "{{ route('listings.edit') }}", 
				type: "GET",
				data: form,
				success: function(result){
					if(result == 'true'){
						$('#ItemPopup').modal('hide');
						//$('#listings-table_processing').css({'display':'block', 'z-index' : 1 });
						$('#listings-table').DataTable().ajax.reload(null, false);
						$.notify({icon:'glyphicon glyphicon-ok-sign', message: 'Record has been updated!'},{type: 'info'});
					}
		    	}, error: function(e) {
		    		$('#ItemPopup').modal('hide');
		    		$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'An error occurred!'},{type: 'danger'});
		    	}
			});
		});
		$(document).on('click', '.MultipleRecordsChecklist', function() {
			var checklistCount = $('.checklistCount').val();
			if ($(this).is(':checked'))
				checklistCount = parseInt(checklistCount) + 1;
			else 
				checklistCount = parseInt(checklistCount) - 1;
			$('.checklistCount').val(checklistCount);
			if ($('.checklistCount').val() > 0) {
				$('button#deleteMultipleRecords').each(function(index) {
					$(this).prop('disabled', false);
				});
			} else {
				$('button#deleteMultipleRecords').each(function(index) {
					$(this).prop('disabled', true);
				});
			}
		});
		$(document).on('click', '#deleteMultipleRecords', function() { 
			var MND_ID_Lists = [];	
			$('#listings-table_processing').css({'display':'block', 'z-index' : 1 });
			$('button#deleteMultipleRecords').each(function(index) {
				$(this).prop('disabled', true);
			});
			$('.MultipleRecordsChecklist').each(function(index) { 
				if ($(this).is(':checked')) {
					MND_ID_Lists.push($(this).attr('id'));
				}
			});
			$.ajax({
				url: "{{ route('listings.delete') }}", 
				type: "GET",
				data: {MND_ID_Lists},
				success: function(result){
					if(result == 'true'){
						$('#listings-table').DataTable().ajax.reload(null, false);
						$.notify({icon:'glyphicon glyphicon-ok-sign', message: 'Record(s) has been deleted!'},{type: 'info'});
					}
		    	}, error: function(e) {
		    		console.log(e);
		    		$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'An error occurred!'},{type: 'danger'});
		    	}
			});
		});
		$('#addNewRecords').click(function(e) {
			e.preventDefault();
			var top_row = $('#listings-table .table-heading').get(0);
			var total = $(top_row).find('th').length;
        	var html = '';
        	var type = 'text';
        	$('#ItemPopup .modal-title').text('New Records');
        	html += '<form method="POST" id="records-new">';
    		$.each( $(top_row).find('th') , function( key, value ) {
				var th_id = $(this).attr('id');
				if (key >= 2 && key != total - 1 && key != 3) {
					var types = $('#'+th_id).data('type');
					if (types == 1) 
						type = 'number';
					else 
						type = 'text';
					html += '<div class="row"><label class="col-md-5 text-left">'+ $(value).text() +'</label><div class="col-md-7"><input type="'+type+'" data-flag="0" name="'+th_id+'" class="form-control newRecords"></div></div>';
				}
			});
			html += '<div class="row"><div class="col-md-12 text-right"><input type="submit" class="btn btn-default" value="Submit"></div></div>';
			html += '</form>';
    		$('#ItemPopup .modal-body').html(html);
			$('#ItemPopup').modal('show');
		});
		$(document).on('submit', 'form#records-new', function(e) {
			e.preventDefault();
			$(this).find('input.newRecords').attr('readonly', true);
			var form = $(this).serialize();
			$.ajax({
				url: "{{ route('listings.new') }}", 
				type: "GET",
				data: form,
				success: function(result){
					if(result == 'true'){
						$('#ItemPopup').modal('hide');
						//$('#listings-table_processing').css({'display':'block', 'z-index' : 1 });
						$('#listings-table').DataTable().ajax.reload(null, false);
						$.notify({icon:'glyphicon glyphicon-ok-sign', message: 'New record has been added!'},{type: 'info'});
					}
		    	}, error: function(e) {
		    		$('#ItemPopup').modal('hide');
		    		$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'An error occurred!'},{type: 'danger'});
		    	}
			});
		});
		$('#listings-table th#deleteColumn').removeClass('sorting_asc');
		$('#importRecordCSV').click(function(e) {
			$('#importRecordCSVModal').modal('show');
		});
		$('#browseCsvBtn').click(function(){
			var file = $(this).parent().parent().find('input[name="csvFile"]');
			file.trigger('click');
		});
		$('input[name="csvFile"]').on('change', function(){
			var f_name = $(this).val().replace(/C:\\fakepath\\/i, '');
			$(this).parent().find('#csvFileInput').val(f_name);
			if(f_name.length > 0)
				$("#submitCsvId").attr('disabled', false);
		});
		$('#importRecordCSVModal').on('hidden.bs.modal', function () {
			$("#importCsvForm")[0].reset();
			$("#submitCsvId").attr('disabled', true);
		});
		function cleanUp(filename){
			$.ajax({
				url: "{{ route('listings.importcleanup') }}",
				type: 'POST',
				data: {'filename': filename,'_token': '{{ csrf_token() }}'},
				success: function(){
					$("#submitCsvId").attr("disabled", false);
					$("#browseCsvBtn").attr("disabled", false);
					$("#submitCsvId").html('Proceed');
					$("#submitCsvId").show();
					$(".import-container").hide();
					$("#importRecordCSVModal .close").attr("data-dismiss","modal");
					$("#importRecordCSVModal").modal('toggle');
					$("#importCsvForm")[0].reset();
					$.notify({icon:'glyphicon glyphicon-ok-sign', message: 'Import successfully completed!'},{type: 'success'});
					$('#listings-table').DataTable().ajax.reload(null, false);
				},error: function(){
					$("#importRecordCSVModal .close").attr("data-dismiss","modal");
					$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'An error occurred!'},{type: 'danger'});
				}
			});
		}
		function updateProgressBar(val){
			$(".progress-bar").css({"width":val+'%'}).attr("aria-valuenow",val);
			$(".progress-bar .sr-only").text(val+"%");
		}
		function importProcess(start_line, filename, total){
			$.ajax({
				url: "{{ route('listings.importcsv') }}",
				type: 'POST',
				data: {
						'start':start_line,
						'filename':filename,
						'total':total,
						'_token': '{{ csrf_token() }}'
				},success: function(data){
					if(data.ongoing == 'true'){
						importProcess(data.current, filename, total);
						updateProgressBar(Math.round((parseInt(data.current)/parseInt(total))*100));
					}
					else if(data.ongoing == 'false')
						cleanUp(filename);
				},error: function(data){
					console.log(data);
					$("#importRecordCSVModal .close").attr("data-dismiss","modal");
					$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'An error occurred!'},{type: 'danger'});
				}
			});
		}
		$("#importCsvForm").on('submit', function(e){
			e.preventDefault();
			$(this).ajaxSubmit({
				url: '{{ route("listings.uploadcsv") }}',
				type: 'POST',
				beforeSend: function(){
					$("#submitCsvId").attr("disabled", true);
					$("#browseCsvBtn").attr("disabled", true);
					$("#submitCsvId").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Uploading...');
				},success: function(data){
					if(data.response == 'success'){
						$("#submitCsvId").hide();
						$(".import-container").removeClass('hidden');
						$(".import-container").show();
						importProcess(0, $("#csvFileInput").val(), data.total);
						$("#importRecordCSVModal .close").removeAttr("data-dismiss");
					}else
						alert("The file is invalid or it has failed in our system's header check. Please upload the 'CSV' file and use the correct headers!");
				},error: function(data){
					console.log(data);
					$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'An error occurred!'},{type: 'danger'});
				}
			});
		});
    });
</script>