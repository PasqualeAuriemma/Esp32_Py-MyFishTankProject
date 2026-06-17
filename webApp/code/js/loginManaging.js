$(document).ready(function () {

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Mostra un messaggio inline dentro un form/contenitore.
     * @param {string} containerId  ID dell'elemento in cui appendere il messaggio
     * @param {string} msg          Testo del messaggio
     * @param {string} type         'success' | 'danger' | 'warning'
     */
    function showMessage(containerId, msg, type) {
        var id = containerId + '-feedback';
        $('#' + id).remove();
        var $div = $(
            '<div id="' + id + '" class="alert alert-' + type + ' mt-3" role="alert">' +
            msg +
            '</div>'
        );
        $('#' + containerId).append($div);
    }

    function clearMessage(containerId) {
        $('#' + containerId + '-feedback').remove();
    }

    // ── LOGIN ──────────────────────────────────────────────────────────────────

    $('#login').on('click', function () {
        var email    = $('#email').val().trim();
        var password = $('#password').val();

        clearMessage('loginForm');

        if (!email || !password) {
            showMessage('loginForm', 'Please enter email and password.', 'warning');
            return;
        }

        $.ajax({
            url: 'php/Login/login_db.php',
            method: 'POST',
            data: { login: 1, emailPHP: email, passwordPHP: password },
            dataType: 'text',
            success: function (response) {
                if (response.trim() === 'success') {
                    $('#loginModal').modal('hide');
                    location.reload(true);
                } else {
                    showMessage('loginForm', 'Wrong email or password.', 'danger');
                }
            },
            error: function () {
                showMessage('loginForm', 'Network error. Please try again.', 'danger');
            }
        });
    });

    // Invio con tasto Enter nel campo password
    $('#password').on('keydown', function (e) {
        if (e.key === 'Enter') { $('#login').trigger('click'); }
    });

    // ── LOGOUT ─────────────────────────────────────────────────────────────────

    $('#logout').on('click', function () {
        $.ajax({
            url: 'php/Login/logout_db.php',
            method: 'POST',
            data: { login: 0 },
            dataType: 'text',
            success: function () { location.reload(true); }
        });
    });

    // ── SIGN UP ────────────────────────────────────────────────────────────────

    $('#signupBtn').on('click', function () {
        var firstName       = $('#firstNameSignUp').val().trim();
        var email           = $('#emailSignUp').val().trim();
        var password        = $('#passwordSignUp').val();
        var passwordConfirm = $('#passwordConfirmSignUp').val();
        var inviteCode      = $('#inviteCode').val().trim();

        clearMessage('signupForm');

        // Validazione lato client
        if (!firstName || !email || !password || !passwordConfirm || !inviteCode) {
            showMessage('signupForm', 'Please fill in all fields.', 'warning');
            return;
        }
        if (password !== passwordConfirm) {
            showMessage('signupForm', 'Passwords do not match.', 'danger');
            return;
        }
        if (password.length < 8) {
            showMessage('signupForm', 'Password must be at least 8 characters.', 'danger');
            return;
        }

        // Disabilita il bottone durante la richiesta
        var $btn = $(this).prop('disabled', true).text('Registering…');

        $.ajax({
            url: 'php/Login/sign_up_db.php',
            method: 'POST',
            data: {
                signup:                1,
                firstNameSignUp:       firstName,
                emailSignUp:           email,
                passwordSignUp:        password,
                passwordConfirmSignUp: passwordConfirm,
                inviteCode:            inviteCode
            },
            dataType: 'text',
            success: function (response) {
                var result;
                try { result = response; }
                catch (e) {
                    showMessage('signupForm', 'Unexpected server response.', 'danger');
                    $btn.prop('disabled', false).text('Register');
                    return;
                }

                if (result.status === 'success') {
                    // Messaggio di benvenuto, poi chiude il modal e ricarica
                    showMessage('signupForm', '✓ Registration successful! Welcome aboard.', 'success');
                    setTimeout(function () {
                        $('#signupModal').modal('hide');
                        location.reload(true);
                    }, 1200);
                } else {
                    showMessage('signupForm', result.message || 'Registration failed.', 'danger');
                    $btn.prop('disabled', false).text('Register');
                }
            },
            error: function () {
                showMessage('signupForm', 'Network error. Please try again.', 'danger');
                $btn.prop('disabled', false).text('Register');
            }
        });
    });

    // ── FORGOT PASSWORD ────────────────────────────────────────────────────────

    $('#forgotPasswordBtn').on('click', function () {
        var email = $('#emailForgot').val().trim();

        clearMessage('forgotPasswordForm');

        if (!email) {
            showMessage('forgotPasswordForm', 'Please enter your email.', 'warning');
            return;
        }

        $.ajax({
            url: 'php/Login/forgot_password_db.php',
            method: 'POST',
            data: { forgotPassword: 1, emailForgot: email },
            dataType: 'text',
            success: function (response) {
                var result;
                try { result = response; }
                catch (e) { showMessage('forgotPasswordForm', 'Unexpected server response.', 'danger'); return; }

                if (result.status === 'success') {
                    showMessage('forgotPasswordForm', 'If the email exists, a reset link has been sent.', 'success');
                    // Nota: result.link NON viene più mostrato/loggato
                    // In produzione il link arriva solo via email
                } else {
                    showMessage('forgotPasswordForm', result.message || 'Request failed.', 'danger');
                }
            },
            error: function () {
                showMessage('forgotPasswordForm', 'Network error. Please try again.', 'danger');
            }
        });
    });

    // ── RESET PASSWORD ─────────────────────────────────────────────────────────

    $('#resetPasswordBtn').on('click', function () {
        var token           = $('#resetToken').val();
        var password        = $('#resetNewPassword').val();
        var passwordConfirm = $('#resetNewPasswordConfirm').val();

        clearMessage('resetForm');

        if (!token) {
            showMessage('resetForm', 'Invalid reset link.', 'danger');
            return;
        }
        if (password !== passwordConfirm) {
            showMessage('resetForm', 'Passwords do not match.', 'danger');
            return;
        }

        $.ajax({
            url: 'php/Login/reset_password_db.php',
            method: 'POST',
            data: {
                resetPassword:       1,
                resetToken:          token,
                newPassword:         password,
                newPasswordConfirm:  passwordConfirm
            },
            dataType: 'text',
            success: function (response) {
                var result;
                try { result = response; }
                catch (e) { showMessage('resetForm', 'Unexpected server response.', 'danger'); return; }

                if (result.status === 'success') {
                    showMessage('resetForm', '✓ Password changed! Redirecting to login…', 'success');
                    setTimeout(function () { window.location.href = 'index.php'; }, 1800);
                } else {
                    showMessage('resetForm', result.message || 'Reset failed.', 'danger');
                }
            },
            error: function () {
                showMessage('resetForm', 'Network error. Please try again.', 'danger');
            }
        });
    });

    // ── Navigazione tra modali ─────────────────────────────────────────────────

    $('#signupLink').on('click', function (e) {
        e.preventDefault();
        clearMessage('loginForm');
        $('#loginModal').modal('hide');
        $('#signupModal').modal('show');
    });

    $('#forgotPasswordLink').on('click', function (e) {
        e.preventDefault();
        clearMessage('loginForm');
        $('#loginModal').modal('hide');
        $('#forgotPasswordModal').modal('show');
    });

    $('.backToLogin').on('click', function (e) {
        e.preventDefault();
        clearMessage('signupForm');
        clearMessage('forgotPasswordForm');
        $('#signupModal').modal('hide');
        $('#forgotPasswordModal').modal('hide');
        $('#loginModal').modal('show');
    });

    // Pulizia messaggi alla chiusura dei modali
    $('#signupModal').on('hidden.bs.modal',        function () { clearMessage('signupForm'); });
    $('#loginModal').on('hidden.bs.modal',          function () { clearMessage('loginForm'); });
    $('#forgotPasswordModal').on('hidden.bs.modal', function () { clearMessage('forgotPasswordForm'); });

});
