$(document).ready(function () {
            // LOGIN
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

            // LOGOUT
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

            // SIGN UP
            $("#signupBtn").on('click', function() {
                var firstName = $("#firstNameSignUp").val();
                var email = $("#emailSignUp").val();
                var password = $("#passwordSignUp").val();
                var passwordConfirm = $("#passwordConfirmSignUp").val();

                $.ajax({
                    url: 'php/Login/sign_up_db.php',
                    method: 'POST',
                    data: {
                        signup: 1,
                        firstNameSignUp: firstName,
                        emailSignUp: email,
                        passwordSignUp: password,
                        passwordConfirmSignUp: passwordConfirm
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if(result.status === 'success') {
                            alert(result.message);
                            $('#signupModal').modal('hide');
                            location.reload(true);
                        } else {
                            alert('Error: ' + result.message);
                        }
                    },
                    dataType: 'text'
                });
            });

            // FORGOT PASSWORD
            $("#forgotPasswordBtn").on('click', function() {
                var email = $("#emailForgot").val();

                $.ajax({
                    url: 'php/Login/forgot_password_db.php',
                    method: 'POST',
                    data: {
                        forgotPassword: 1,
                        emailForgot: email
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        alert(result.message);
                        if(result.status === 'success') {
                            $('#forgotPasswordModal').modal('hide');
                            // Mostra link per testing (in produzione rimuovere)
                            if(result.link) {
                                console.log('Reset link: ' + result.link);
                            }
                        }
                    },
                    dataType: 'text'
                });
            });

            // RESET PASSWORD
            $("#resetPasswordBtn").on('click', function() {
                var token = $("#resetToken").val();
                var password = $("#resetNewPassword").val();
                var passwordConfirm = $("#resetNewPasswordConfirm").val();

                $.ajax({
                    url: 'php/Login/reset_password_db.php',
                    method: 'POST',
                    data: {
                        resetPassword: 1,
                        resetToken: token,
                        newPassword: password,
                        newPasswordConfirm: passwordConfirm
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        alert(result.message);
                        if(result.status === 'success') {
                            window.location.href = 'index.php';
                        }
                    },
                    dataType: 'text'
                });
            });

            // Show/Hide modals per Sign Up e Forgot Password
            $("#signupLink").on('click', function(e) {
                e.preventDefault();
                console.log('Sign Up clicked');
                $('#loginModal').modal('hide');
                $('#signupModal').modal('show');
            });

            $("#forgotPasswordLink").on('click', function(e) {
                e.preventDefault();
                console.log('Forgot Password clicked');
                $('#loginModal').modal('hide');
                $('#forgotPasswordModal').modal('show');
            });

            $(".backToLogin").on('click', function(e) {
                e.preventDefault();
                console.log('Back to Login clicked');
                $('#signupModal').modal('hide');
                $('#forgotPasswordModal').modal('hide');
                $('#loginModal').modal('show');
            });
        });
