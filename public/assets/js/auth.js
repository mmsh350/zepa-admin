$(document).ready(function() {
     
	$('#btnlogin').on('click', function() {

        $('#btnlogin').prop("disabled", true); 
        $("#spinner").show();

		var email = $('#email').val();
		var password = $('#password').val();

		if(email !="" && password !="" ){

            var data = new FormData(this.form);
			$.ajax({
				url: "login",
				type: "POST",
                dataType: "json",
                data,
                processData: false,
                contentType: false,
				cache: false,
				success: function(data)
                {
							$('#btnlogin').html("Loading dashboard ...");
							setTimeout(function(){  	
								$('#btnlogin').prop("disabled", false); 
								location.href = data.redirect_url; 
							}, 3000);
				},
                error:function(data) 
                {
						$.each(data.responseJSON.errors, function (key, value) {
						$("#error").empty();
						$('#error').append('<strong>'+value+'</strong>');
						$("#alert-danger").fadeIn();
						});
					
						setTimeout(function(){  
							$('#btnlogin').prop("disabled", false);
							$("#spinner").hide();  
						}, 2000);
                }
			});
		}else
		{	
						$("#error").empty();
						$('#error').append('<strong>All fields are required!</strong>');
                        $("#alert-danger").fadeIn();
						setTimeout(function(){  	
							$("#spinner").hide();
							$('#btnlogin').prop("disabled", false);
						}, 2000); 
		}
	});
});

		//Trigger Login on Enter key
     
		// Target password field
		var input = document.getElementById("password");

		// Execute a function when the user releases a key on the keyboard
		input.addEventListener("keyup", function(event) {
		  // Number 13 is the "Enter" key on the keyboard
		  if (event.keyCode === 13) {
			// Cancel the default action, if needed
			event.preventDefault();
			// Trigger the button element with a click
			document.getElementById("btnlogin").click();
		  }
		});
		
			// Target email field
		var input = document.getElementById("email");

		// Execute a function when the user releases a key on the keyboard
		input.addEventListener("keyup", function(event) {
		  // Number 13 is the "Enter" key on the keyboard
		  if (event.keyCode === 13) {
			// Cancel the default action, if needed
			event.preventDefault();
			// Trigger the button element with a click
			document.getElementById("btnlogin").click();
		  }
		});



