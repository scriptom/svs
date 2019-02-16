function CookieHandler() {
	this.init = function() {
		let cookies = decodeURIComponent(document.cookie).split(';');
		let cookiesObj = {};
		for (let cookie of cookies) {
			while (cookie.charAt(0) == ' ')
				cookie = cookie.substring(1);
			cookiesObj[cookie.substring(0, cookie.indexOf('='))] = cookie.substring(cookie.indexOf('=') + 1);
		}
		return cookiesObj;
	};
	this.getCookie = function(cookie) {
		if ( typeof this.cookies[cookie] != "undefined" )
			return this.cookies[cookie];
		else return false;
	};
	this.setCookie = function (cookieName, cookieValue, expireFactor, expireTime = 'days') {
		const formats = {
			'miliseconds': 1,
			'seconds': 1000,
			'minutes': 60000,
			'hours': 3600000,
			'days': 86400000,
			'months': 2592000000,
			'years': 31104000000
		};
		if (formats[expireTime] == "undefined")
			throw "Invalid expire format for cookie. Format must be one of: 'miliseconds', 'seconds', 'minutes', 'hours', 'days', 'months', 'years'.";
		const date = new Date();
		date.setTime(date.getTime() + ( expireFactor * formats[expireTime] ));
		const expireDateStr = "expires=" + date.toUTCString();
		document.cookie = cookieName + '=' + cookieValue + ";" + expireDateStr + ';path=/';
		this.addCookie(cookieName, cookieValue);
		return true;
	};

	this.addCookie = function(cookieName, cookieValue) {
		this.cookies[cookieName] = cookieValue;
	};
	this.cookies = this.init();
}