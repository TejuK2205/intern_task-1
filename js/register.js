$(document).ready(function() {
    $('#registerForm').submit(function(event) {
        event.preventDefault();

        var formData = {
            name: $('#name').val(),
            age: $('#age').val(),
            contact: $('#contact').val(),
            email: $('#email').val(),
            username: $('#username').val(),
            password: $('#password').val()
        };

        $.ajax({
            type: 'POST',
            url: 'php/register.php',
            data: formData,
            dataType: 'json',
            encode: true
        })
        .done(function(response) {
            console.log(response);
            if (response.success) {
                alert('Registration successful. You can now login.');
                window.location.href = 'login.html';
            } else {
                alert('Registration failed. ' + response.message);
            }
        })
        .fail(function(error) {
            console.error(error.responseText);
            alert('Registration failed. Please try again.');
        });
    });
});
