var base64 = {
	charmap : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
	encode : function(str) {
		var ret = "";
		var c, i, acc = 0;
		var div = 1;
		for (i = 0, c = 0; i < str.length; i++, c++) {
			acc = acc * 256 + str.charCodeAt(i);
			div = div * 4;
			ret = ret + base64.charmap.charAt(parseInt(acc / div));
			acc = acc % div;
			if (div == 64)
				ret = ret + base64.charmap.charAt(parseInt(acc)), acc = 0,
						div = 1, c++;
			if (c >= 75)
				c = -1, ret = ret + "\n";
		}
		if (i % 3) {
			ret = ret
					+ base64.charmap.charAt(parseInt(acc
							* ((i % 3 == 1) ? 16 : 4)));
			ret = ret + ((i % 3) == 1 ? "==" : "=");
		}
		return ret;
	},
	decode : function(str) {
		var ret = "";
		var i, acc = 0;
		var div = 1;
		for (i = 0; i < str.length; i++) {
			if (str.charAt(i) == "=" || str.charAt(i) == '\n')
				break;
			acc = acc * 64 + base64.charmap.indexOf(str.charAt(i));
			div = (div == 1 ? 64 : div / 4);
			if (div != 64) {
				ret = ret + String.fromCharCode(parseInt(acc / div));
				acc = acc % div;
			}
		}
		return ret;
	}
}
