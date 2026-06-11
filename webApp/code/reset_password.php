<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reset Password - MyFishTank</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/salmon.png" />
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth auth-bg-1 px-2 py-5">
                <div class="row w-100">
                    <div class="col-lg-4 mx-auto">
                        <div class="card">
                            <div class="card-body px-5 py-5">
                                <h3 class="card-title text-left mb-3">Reset Password</h3>
                                <form id="resetForm">
                                    <div class="form-group">
                                        <label>New Password</label>
                                        <input type="password" class="form-control p_input" placeholder="New Password" id="resetNewPassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" class="form-control p_input" placeholder="Confirm Password" id="resetNewPasswordConfirm" required>
                                    </div>
                                    <input type="hidden" id="resetToken" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                                    <div class="text-center mt-4">
                                        <button type="button" class="btn btn-primary btn-block enter-btn" id="resetPasswordBtn">Reset Password</button>
                                    </div>
                                </form>
                                <div id="resetMessage" class="mt-3 alert" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script>
        $(document).ready(function() {
            $("#resetPasswordBtn").on('click', function() {
                var token = $("#resetToken").val();
                var password = $("#resetNewPassword").val();
                var passwordConfirm = $("#resetNewPasswordConfirm").val();

                if(!token) {
                    showMessage('Invalid reset link', 'error');
                    return;
                }

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
                        showMessage(result.message, result.status);
                        if(result.status === 'success') {
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 2000);
                        }
                    },
                    error: function() {
                        showMessage('An error occurred', 'error');
                    }
                });
            });

            function showMessage(message, status) {
                var msgDiv = $("#resetMessage");
                msgDiv.removeClass('alert-danger alert-success');
                msgDiv.addClass(status === 'success' ? 'alert-success' : 'alert-danger');
                msgDiv.html(message).show();
            }
        });
    </script>
</body>
</html>
