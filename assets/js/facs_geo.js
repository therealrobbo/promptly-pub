function geo_success(position) {
    cookieName = 'pos';
    cookieValue = position.coords.latitude + "," + position.coords.longitude;
    createCookie(cookieName,cookieValue,10);
    window.location='/find/' + cookieValue;
}

function geo_error(msg) {
    createCookie('pos','0,0',2);
}

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(geo_success, geo_error);
} else {
    geo_error('not supported');
}

function createCookie(name,value,hours) {
    if ( hours ) {
        var date = new Date();

        date.setTime(date.getTime()+(hours*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    } else var expires = "";

    document.cookie = name+"="+value+expires+"; domain=.findacomicshop.com; path=/";
}