$('#logout-form').on('click', function () {
    localStorage.removeItem('access_token');
    console.log('access_token removed');
});


$('#login-button').on('click', function () {
    localStorage.setItem('access_token', "{{ session('access_token') }}");
    console.log('access_token saved');
});


function isAccessTokenExpired(token) {
    const [, payload] = token.split('.');
    const decoded = JSON.parse(atob(payload));
    const currentTime = Math.floor(Date.now() / 1000);
    return decoded.exp < currentTime;
}
