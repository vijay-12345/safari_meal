//save data in local storage for refresh all tabs after logout

function storageChange (event) {
	if(event.key === 'logged_in') {
        location.reload(true);
    }
}
window.addEventListener('storage', storageChange, false);

if ( ( isLogin ) && ( window.localStorage.getItem('logged_in') != 'true' ) ) {
    window.localStorage.setItem('logged_in', true);
}
if ( ! ( isLogin ) && ( window.localStorage.getItem('logged_in') == 'true' ) ) {
	document.cookie = "new_token=;expires=Thu, 01 Jan 1970 00:00:01 GMT;domain="+location.hostname+";path=/"; 
	window.localStorage.setItem('logged_in', false);
}