/*

Quick Flickr Widget (javascript helper)
Author: Konstantin Kovshenin (http://kovshenin.com)
Version: 1.2.9-js

Please do not include this file directly. It's done for you within the Quick Flickr Widget the right way.
For more info visit the Quick Flickr Widget page: http://kovshenin.com/wordpress/plugins/quick-flickr-widget/

/* License

    Quick Flickr Widget (javascript)
    Copyright (C) 2009 Konstantin Kovshenin (kovshenin@live.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
*/

function jsonFlickrFeed(response)
{
	// TODO: some validation here perhaps?
	
	var striptags = new RegExp("<([^<>\s]*)(\s[^<>]*)?>", "g");
	var output = "";
	
	if (FlickrOptions.random == "checked") shuffle(response.items);
	
	// Loop thru the photos
	for (var i in response.items)
	{
		if (--FlickrOptions.items < 0) break; // exit loop
		var medium_url = response.items[i].media.m;
		var photo_title = response.items[i].title;
		var photo_description = response.items[i].description.replace(striptags, "");
		var photo_url = medium_url.replace("_m.jpg", FlickrOptions.view + ".jpg");
		var preview_url = medium_url.replace("_m.jpg", ".jpg");
		var thickbox_attrib = (FlickrOptions.thickbox == "checked") ? "class=\"thickbox\" rel=\"flickr-gallery\" title=\"" + photo_title + ": " + photo_description + "\"" : "";
		var href = (FlickrOptions.thickbox == "checked") ? preview_url : response.items[i].link;
		var target = (FlickrOptions.target == "checked") ? "target=\"_blank\"" : "";
		
		photo_title = (FlickrOptions.show_titles == "checked") ? "<div class=\"qflickr-title\">" + photo_title + "</div>" : "";

		output += htmlspecialchars_decode(FlickrOptions.before_item, "ENT_NOQUOTES") + "<a " + thickbox_attrib + " " + target + " href=\"" + href + "\"><img class=\"flickr_photo\" alt=\"" + photo_description + "\" title=\"" + photo_description + "\" src=\"" + photo_url + "\" /></a>" + photo_title + "" + htmlspecialchars_decode(FlickrOptions.after_item, "ENT_NOQUOTES");
	}
	
	if (FlickrOptions.more_title.length > 0) output += "<a href=\"" + response.link + "\">" + FlickrOptions.more_title + "</a>";
	
	document.write(output);
	return;
}

function get_html_translation_table(table, quote_style) {
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: noname
    // +   bugfixed by: Alex
    // +   bugfixed by: Marco
    // +   bugfixed by: madipta
    // %          note: It has been decided that we're not going to add global
    // %          note: dependencies to php.js. Meaning the constants are not
    // %          note: real constants, but strings instead. integers are also supported if someone
    // %          note: chooses to create the constants themselves.
    // %          note: Table from http://www.the-art-of-web.com/html/character-codes/
    // *     example 1: get_html_translation_table('HTML_SPECIALCHARS');
    // *     returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}
    
    var entities = {}, histogram = {}, decimal = 0, symbol = '';
    var constMappingTable = {}, constMappingQuoteStyle = {};
    var useTable = {}, useQuoteStyle = {};
    
    useTable      = (table ? table.toUpperCase() : 'HTML_SPECIALCHARS');
    useQuoteStyle = (quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT');
    
    // Translate arguments
    constMappingTable[0]      = 'HTML_SPECIALCHARS';
    constMappingTable[1]      = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';
    
    // Map numbers to strings for compatibilty with PHP constants
    if (!isNaN(useTable)) {
        useTable = constMappingTable[useTable];
    }
    if (!isNaN(useQuoteStyle)) {
        useQuoteStyle = constMappingQuoteStyle[useQuoteStyle];
    }
 
    if (useTable == 'HTML_SPECIALCHARS') {
        // ascii decimals for better compatibility
        entities['38'] = '&amp;';
        if (useQuoteStyle != 'ENT_NOQUOTES') {
            entities['34'] = '&quot;';
        }
        if (useQuoteStyle == 'ENT_QUOTES') {
            entities['39'] = '&#039;';
        }
        entities['60'] = '&lt;';
        entities['62'] = '&gt;';
    } else if (useTable == 'HTML_ENTITIES') {
        // ascii decimals for better compatibility
      entities['38']  = '&amp;';
        if (useQuoteStyle != 'ENT_NOQUOTES') {
            entities['34'] = '&quot;';
        }
        if (useQuoteStyle == 'ENT_QUOTES') {
            entities['39'] = '&#039;';
        }
      entities['60']  = '&lt;';
      entities['62']  = '&gt;';
      entities['160'] = '&nbsp;';
      entities['161'] = '&iexcl;';
      entities['162'] = '&cent;';
      entities['163'] = '&pound;';
      entities['164'] = '&curren;';
      entities['165'] = '&yen;';
      entities['166'] = '&brvbar;';
      entities['167'] = '&sect;';
      entities['168'] = '&uml;';
      entities['169'] = '&copy;';
      entities['170'] = '&ordf;';
      entities['171'] = '&laquo;';
      entities['172'] = '&not;';
      entities['173'] = '&shy;';
      entities['174'] = '&reg;';
      entities['175'] = '&macr;';
      entities['176'] = '&deg;';
      entities['177'] = '&plusmn;';
      entities['178'] = '&sup2;';
      entities['179'] = '&sup3;';
      entities['180'] = '&acute;';
      entities['181'] = '&micro;';
      entities['182'] = '&para;';
      entities['183'] = '&middot;';
      entities['184'] = '&cedil;';
      entities['185'] = '&sup1;';
      entities['186'] = '&ordm;';
      entities['187'] = '&raquo;';
      entities['188'] = '&frac14;';
      entities['189'] = '&frac12;';
      entities['190'] = '&frac34;';
      entities['191'] = '&iquest;';
      entities['192'] = '&Agrave;';
      entities['193'] = '&Aacute;';
      entities['194'] = '&Acirc;';
      entities['195'] = '&Atilde;';
      entities['196'] = '&Auml;';
      entities['197'] = '&Aring;';
      entities['198'] = '&AElig;';
      entities['199'] = '&Ccedil;';
      entities['200'] = '&Egrave;';
      entities['201'] = '&Eacute;';
      entities['202'] = '&Ecirc;';
      entities['203'] = '&Euml;';
      entities['204'] = '&Igrave;';
      entities['205'] = '&Iacute;';
      entities['206'] = '&Icirc;';
      entities['207'] = '&Iuml;';
      entities['208'] = '&ETH;';
      entities['209'] = '&Ntilde;';
      entities['210'] = '&Ograve;';
      entities['211'] = '&Oacute;';
      entities['212'] = '&Ocirc;';
      entities['213'] = '&Otilde;';
      entities['214'] = '&Ouml;';
      entities['215'] = '&times;';
      entities['216'] = '&Oslash;';
      entities['217'] = '&Ugrave;';
      entities['218'] = '&Uacute;';
      entities['219'] = '&Ucirc;';
      entities['220'] = '&Uuml;';
      entities['221'] = '&Yacute;';
      entities['222'] = '&THORN;';
      entities['223'] = '&szlig;';
      entities['224'] = '&agrave;';
      entities['225'] = '&aacute;';
      entities['226'] = '&acirc;';
      entities['227'] = '&atilde;';
      entities['228'] = '&auml;';
      entities['229'] = '&aring;';
      entities['230'] = '&aelig;';
      entities['231'] = '&ccedil;';
      entities['232'] = '&egrave;';
      entities['233'] = '&eacute;';
      entities['234'] = '&ecirc;';
      entities['235'] = '&euml;';
      entities['236'] = '&igrave;';
      entities['237'] = '&iacute;';
      entities['238'] = '&icirc;';
      entities['239'] = '&iuml;';
      entities['240'] = '&eth;';
      entities['241'] = '&ntilde;';
      entities['242'] = '&ograve;';
      entities['243'] = '&oacute;';
      entities['244'] = '&ocirc;';
      entities['245'] = '&otilde;';
      entities['246'] = '&ouml;';
      entities['247'] = '&divide;';
      entities['248'] = '&oslash;';
      entities['249'] = '&ugrave;';
      entities['250'] = '&uacute;';
      entities['251'] = '&ucirc;';
      entities['252'] = '&uuml;';
      entities['253'] = '&yacute;';
      entities['254'] = '&thorn;';
      entities['255'] = '&yuml;';
    } else {
        throw Error("Table: "+useTable+' not supported');
        return false;
    }
    
    // ascii decimals to real symbols
    for (decimal in entities) {
        symbol = String.fromCharCode(decimal);
        histogram[symbol] = entities[decimal];
    }
    
    return histogram;
}

function htmlspecialchars_decode(string, quote_style) {
    // http://kevin.vanzonneveld.net
    // +   original by: Mirek Slugen
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Mateusz "loonquawl" Zalega
    // +      input by: ReverseSyntax
    // +      input by: Slawomir Kaniecki
    // +      input by: Scott Cariss
    // +      input by: Francois
    // +   bugfixed by: Onno Marsman
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // -    depends on: get_html_translation_table
    // *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');
    // *     returns 1: '<p>this -> &quot;</p>'
 
    var histogram = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();
    
    if (false === (histogram = get_html_translation_table('HTML_SPECIALCHARS', quote_style))) {
        return false;
    }
 
    // &amp; must be the last character when decoding!
    delete(histogram['&']);
    histogram['&'] = '&amp;';
 
    for (symbol in histogram) {
        entity = histogram[symbol];
        tmp_str = tmp_str.split(entity).join(symbol);
    }
    
    return tmp_str;
}

function shuffle( inputArr ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    revised by: Brett Zamir (http://brettz9.blogspot.com)
    // *     example 1: shuffle({5:'a', 2:'3', 3:'c', 4:5, 'q':5});
    // *     returns 1: true
 
    var valArr = [];
    var k = '', i = 0;
 
    for (k in inputArr) { // Get key and value arrays
        valArr.push(inputArr[k]);
        delete inputArr[k] ;
    }
    valArr.sort(function() {return 0.5 - Math.random();});
 
    for (i = 0; i < valArr.length; i++) { // Repopulate the old array
        inputArr[i] = valArr[i];
    }
    
    return true;
}