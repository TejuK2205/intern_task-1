$(document).ready(function() {
    $('#loginForm').submit(function(event) {
        event.preventDefault();

        var formData = {
            username: $('#username').val(),
            password: $('#password').val()
        };

        $.ajax({
            type: 'POST',
            url: 'php/login.php',
            data: formData,
            dataType: 'json',
            encode: true
        })
        .done(function(response) {
            console.log(response);
            if (response.success) {
                localStorage.setItem('authToken', response.token);
                alert('Login successful.');
                window.location.href = 'profile.html';
            } else {
                alert('Login failed. ' + response.message);
            }
        })
        .fail(function(error) {
            console.error(error.responseText);
            alert('Login failed. Please try again.');
        });
    });
});
