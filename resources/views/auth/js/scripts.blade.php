<script type="text/javascript">
	$(document).ready(function(){
		$("#problem").on("change", function(){
			$(".problem").val($(this).val());
		});
		$("button").on('click', function(){
			$(this).attr("disabled",true);
			if($('form')[0].checkValidity())
				$('form')[0].submit();
			else
				$(this).attr("disabled",false);
		});
		var dialog = $( "#dialog" ).dialog({
			autoOpen: false,
			height: 500,
			width: 450,
			modal: true,
			buttons: {
			"Submit": submitSupport,
				Cancel: function() {
					dialog.dialog( "close" );
				}
			},
			close: function() {
				$("#support-form")[0].reset();
				$("#problem").prop('disabled', false);
			}
		});
		function reloadCaptcha(){
			$.ajax({
				url: "{{ route('support-captcha') }}",
				success: function(data){
					$(".recaptcha-wrapper").html(data);
				}
			});
		}
		function submitSupport(){
			$.ajax({
				url: "{{ route('support') }}",
				type: "POST",
				data: $("#dialog form").serialize(),
				success: function(data){
					data = JSON.parse(data);
					if('error' in data){
						reloadCaptcha();
						alert(data.error);
					}else if('result' in data){
						alert(data.result);
						dialog.dialog("close");
					}
				}
			})
		}
		dialog.find("form").on("submit", function(event) {
			event.preventDefault();
			submitSupport();
		});
		$(".support").on("click", function(){
			if($(this).hasClass("req-acc")){
				$("#problem").val('1');
				$(".problem").val('1');
				$("#problem").prop('disabled', true);
			}else if($(this).hasClass("req-pass")){
				$("#problem").val('2');
				$(".problem").val('2');
				$("#problem").prop('disabled', true);
			}
			reloadCaptcha();
			dialog.dialog("open");
		});
	});
</script>