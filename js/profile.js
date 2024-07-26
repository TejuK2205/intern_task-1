$(document).ready(function() {
    function getAuthToken() {
        return localStorage.getItem('authToken');
    }

    function fetchProfile() {
        var authToken = getAuthToken();

        if (!authToken) {
            alert('User not logged in. Please log in.');
            window.location.href = 'login.html';
            return;
        }

        $.ajax({
            type: 'GET',
            url: 'php/profile.php',
            data: { token: authToken },
            dataType: 'json',
            encode: true
        })
        .done(function(response) {
            if (response.success) {
                
                $('#profile-name').text(response.name);
                $('#profile-age').text(response.age);
                $('#profile-contact').text(response.contact);
                $('#profile-username').text(response.username);

                
                if (response.source === 'redis') {
                    alert('Profile data fetched from Redis.');
                } else {
                    alert('Profile data fetched from MongoDB.');
                }
            } else {
                alert('Failed to fetch profile. ' + response.message);
                window.location.href = 'login.html';
            }
        })
        .fail(function(error) {
            console.error('Error:', error.responseText);
            alert('Error fetching profile. Please try again.');
        });
    }

    fetchProfile();

    $('#update-btn').click(function() {
        window.location.href = 'edit_profile.html';
    });

    $('#logout-btn').click(function() {
        localStorage.removeItem('authToken');
        window.location.href = 'login.html';
    });
});
