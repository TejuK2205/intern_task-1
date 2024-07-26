$(document).ready(function() {
    var authToken = localStorage.getItem('authToken');

    if (authToken) {
    
        $.ajax({
            url: 'http://localhost/Login_registerpage/php/profile.php',
            type: 'GET',
            dataType: 'json',
            data: { token: authToken },
            success: function(response) {
                if (response.success) {
                    $('#name').val(response.name);
                    $('#age').val(response.age);
                    $('#contact').val(response.contact);
                    $('#username').val(response.username);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching profile:', error);
                alert('Error fetching profile. Please try again.');
            }
        });

        
        $('#editProfileForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            formData.push({ name: 'token', value: authToken });

            $.ajax({
                url: 'http://localhost/Login_registerpage/php/edit_profile.php',
                type: 'POST',
                dataType: 'json',
                data: $.param(formData),
                success: function(response) {
                    if (response.success) {
                        alert('Profile updated successfully!');
                        window.location.href = 'profile.html';
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating profile:', error);
                    alert('Error updating profile. Please try again.');
                }
            });
        });

        
        $('#back-btn').click(function() {
            window.location.href = 'profile.html';
        });
    } else {
        alert('Auth token not found in local storage.');
        window.location.href = 'login.html';
    }
});
