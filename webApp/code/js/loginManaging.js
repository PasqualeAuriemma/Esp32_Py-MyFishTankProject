$(document).ready(function () {
            $("#login").on('click', function() {
            	var email = $("#email").val();
                var password = $("#password").val();
                
                $.ajax(	
                {
                    url: 'php/Login/login_db.php',
                    method: 'POST',
                    data: {
                        login: 1, 
                        emailPHP: email,
                        passwordPHP: password
                          },
                    success: function(response) {
                    	console.log(response);
                         $('#loginModal').modal('hide');
                         location.reload(true);
                    },
                    dataType: 'text'
                }
              );    
            });
            $("#logout").on('click', function() {
                $.ajax(	
                {
                    url: 'php/Login/logout_db.php',
                    method: 'POST',
                    data: {
                        login: 0, 
                          },
                    success: function(response) {
                    	//console.log(response);
                        location.reload(true);
                    },
                    dataType: 'text'
                }
              );    
            });
        });
