<script>
	$(function() {
		
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: "{{ route('users.list') }}",
            "columns": [
				{ "data": "Id" }, 
				{ "data": "FirstName" },
				{ "data": "LastName" },
				{ "data": "Email" },
				//{ "data": "Password" },
				{ "data": "LastLoginDate" },
				{ "data": "RememberToken" },
				{ "data": "RoleName" },
				{ "data": "Activated" },
				{
	                data: null,
	                bSearchable: false,
	                className: "center",
	                defaultContent: '<a class="editor_edit">Edit</a> / <a class="editor_change_pass" title="Send a Password Change email.">PW request</a> / <a class="editor_remove">Delete</a>'
	            }
			]
        });

        $('#users-table').on( 'click', '.editor_change_pass', function (e) {
        	e.preventDefault();
        	var elem = $(this);
        	var row = $(elem).closest("tr").get(0);
        	var id = $(row).children('td:eq(0)').text();
        	if($(elem).html() == 'PW request'){
        		$(elem).html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Sending...');
        		$.ajax({
        			url: "{{ route('users.change.pass') }}",
	        		data: {'UserId': id},
        			success: function(result){
        				if(result == 'true'){
        					$.notify({icon:'glyphicon glyphicon-ok-sign', message: 'Change Password request has been sent to user.'},{type: 'info'});
        				}else
        					$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'An error occurred!'},{type: 'danger'});
        				$(elem).html('PW request');
	        		},
        			error: function(e){
        				$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'An error occurred!'},{type: 'danger'});
        				$(elem).html('PW request');
        				console.log(e);
	        		}
        		});
		}
        });

        $('#users-table').on( 'click', '.editor_edit', function (e) {
        	e.preventDefault();
        	 var row = $(this).closest("tr").get(0);
        	 var id = $(row).children('td:eq(0)').text();
        	 var first_name = $(row).children('td:eq(1)').text();
        	 var last_name = $(row).children('td:eq(2)').text();
        	 var email = $(row).children('td:eq(3)').text();
        	 //var password = $(row).children('td:eq(4)').text();
        	 var role = $(row).children('td:eq(6)').text();
        	 var activated = $(row).children('td:eq(7)').text();
        	 var html = '';
        	 $('#ItemPopup .modal-title').text('Edit User id '+id);

        	 html = '<form method="POST" id="users-update">'
	        	 html += '<div class="row"><label class="col-md-3 text-left">FirstName</label><div class="col-md-9"><input type="text" value="'+first_name+'" name="firstname" class="form-control editUser"></div></div>';
	        	 html += '<div class="row"><label class="col-md-3 text-left">Lastname</label><div class="col-md-9"><input type="text" value="'+last_name+'" name="lastname" class="form-control editUser"></div></div>';
	        	 html += '<div class="row"><label class="col-md-3 text-left">Email</label><div class="col-md-9"><input type="text" value="'+email+'" name="email" class="form-control editUser"></div></div>';
	        	 //html += '<div class="row"><label class="col-md-3 text-left">Password</label><div class="col-md-9"><input type="text" value="'+password+'" name="password" class="form-control editUser"></div></div>';
	        	 html += '<div class="row"><label class="col-md-3 text-left">Role</label><div class="col-md-9"><select name="role" class="form-control editUser">';
	        	 @foreach($roles as $k=>$i)
	        	 html += '<option value="{{ $i->Id }}" '+(role == '{{ $i->RoleName }}'?'selected':'')+'>{{ $i->RoleName }}</option>';
	        	 @endforeach
	        	 html += '</select></div></div>';
	        	 html += '<div class="row"><label class="col-md-3 text-left">Activated</label><div class="col-md-9"><select name="activated" class="form-control editUser">';

	        	 if (activated == 'Yes') {
	        	 	html += '<option value="1" selected>Yes</option><option value="0">No</option>';
	        	 } else {
	        	 	html += '<option value="1">Yes</option><option value="0" selected>No</option>';
	        	 }

	        	 html += '</select></div></div>';
	        	 html += '<div class="row"><div class="col-md-12 text-right"><input type="hidden" name="id" value="'+id+'"><input type="submit" class="update btn btn-default" id="'+id+'" value="Update"></div></div>';
			 html +='</form>';
        	 $('#ItemPopup .modal-body').html(html);
			 $('#ItemPopup').modal('show');
		});

        $(document).on('submit', 'form#users-update', function(e) {
			e.preventDefault();

			$(this).find('input.editUser').attr('readonly', true);
			$(this).find('select.editUser').attr('readonly', true);

			var form = $(this).serialize();
			$.ajax({
				url: "{{ route('users.edit') }}", 
				type: "GET",
				data: form,
				success: function(result){
					if(result == 'true'){
						$('#ItemPopup').modal('hide');
						$('#users-table').DataTable().ajax.reload(null, false);
						$.notify({icon:'glyphicon glyphicon-ok-sign', message: 'Update successful!'},{type: 'info'});
					}else if(result != 'false'){
						$('#ItemPopup').modal('hide');
						$.notify({icon:'glyphicon glyphicon-warning-sign', message: result},{type: 'danger'});
					}else{
						$('#ItemPopup').modal('hide');
						$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'Failed to update.'},{type: 'danger'});
					}
		    	}, error: function(e) {
		    		$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'An error occurred!'},{type: 'danger'});
		    		console.log(e); 
		    	}
			});
		});

		$('#addNewUser').click(function(e) {
			e.preventDefault();
			var html = '';

			$('#ItemPopup .modal-title').text('Add New User');

			html = '<form method="POST" id="users-add">'
	        	 html += '<div class="row"><label class="col-md-3 text-left">FirstName</label><div class="col-md-9"><input type="text" name="firstname" class="form-control newUser" required></div></div>';
	        	 html += '<div class="row"><label class="col-md-3 text-left">Lastname</label><div class="col-md-9"><input type="text" name="lastname" class="form-control newUser" required></div></div>';
	        	 html += '<div class="row"><label class="col-md-3 text-left">Email</label><div class="col-md-9"><input type="email" name="email" class="form-control newUser" required></div></div>';
	        	 html += '<div class="row"><label class="col-md-3 text-left">Role</label><div class="col-md-9"><select name="role" class="form-control newUser">';
	        	 @foreach($roles as $k=>$i)
	        	 html += '<option value="{{ $i->Id }}">{{ $i->RoleName }}</option>';
	        	 @endforeach
	        	 html += '</select></div></div>';
	        	 //html += '<div class="row"><label class="col-md-3 text-left">Password</label><div class="col-md-9"><input type="text" name="password" class="form-control newUser" required></div></div>';
	        	 //html += '<div class="row"><label class="col-md-3 text-left">Activated</label><div class="col-md-9"><select name="activated" class="form-control">';
	        		 //html += '<option value="1" selected>Yes</option><option value="0">No</option>';
	        	 //html += '</select></div></div>';
	        	 html += '<div class="row"><div class="col-md-12 text-right"><input type="submit" class="btn btn-default" value="Submit"></div></div>';
			 html +='</form>';

        	 $('#ItemPopup .modal-body').html(html);
			 $('#ItemPopup').modal('show');

		});

		$(document).on('submit', 'form#users-add', function(e) {
			e.preventDefault();
			$(this).find('input.newUser').attr('readonly', true);
			
			var form = $(this).serialize();
			$.ajax({
				url: "{{ route('users.add') }}", 
				type: "GET",
				data: form,
				success: function(result){
					if(result == 'true'){
						$('#ItemPopup').modal('hide');
						$('#users-table').DataTable().ajax.reload(null, false);
						$.notify({icon:'glyphicon glyphicon-ok-sign', message: 'New user has been added!'},{type: 'info'});
					} else {
						$('#ItemPopup').modal('hide');
						$.notify({icon:'glyphicon glyphicon-warning-sign', message: result},{type: 'danger'});
						console.log(result); 
					}
		    	}, error: function(xhr, status, error) {
  					//console.log(xhr.statusText);
  					alert('Error! Email address must be unique');
  					$('form#users-add').find('input.newUser').attr('readonly', false);
		    	}
			});
		});

		$('#users-table').on( 'click', '.editor_remove', function (e) {
			e.preventDefault();  
			var row = $(this).closest("tr").get(0);
        	var id = $(row).children('td:eq(0)').text();
        	var first_name = $(row).children('td:eq(1)').text();

			if (confirm('Are you sure you want to delete '+ first_name +'?')) {

			    $.ajax({
					url: "{{ route('users.delete') }}", 
					type: "GET",
					data: {
						id : id
					},
					success: function(result){
						if(result == 'true'){
							$('#users-table').DataTable().ajax.reload(null, false);
							$.notify({icon:'glyphicon glyphicon-ok-sign', message: 'User has been deleted!'},{type: 'info'});
						}
			    	}, error: function(e) {
			    		$.notify({icon:'glyphicon glyphicon-warning-sign', message: 'An error occurred!'},{type: 'danger'});
			    		console.log(e); 
			    	}
				});    
			} 

		});

    });
</script>