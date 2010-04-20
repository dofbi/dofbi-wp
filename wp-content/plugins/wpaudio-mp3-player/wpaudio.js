// WPaudio WordPress MP3 Player Plugin (http://wpaudio.com) by Todd Iceton (t@ticeton.com)
soundManager.debugMode = false;
soundManager.url = wpa_url + '/sm2/';
soundManager.nullURL = wpa_url + '/sm2/null.mp3';
soundManager.useHighPerformance = true;
soundManager.useFastPolling = false;
soundManager.waitForWindowLoad = true;
var wpa = [];
var wpa_id = 0;
// Common functions
function isset(varname) {
	return(typeof(window[varname]) != 'undefined');
}
function wpaBarWidth(id) {
	// Fix bar width for IE and make min width for sub text
	var text_width = jQuery('#wpa' + id + '_text').width();
	var bar_width = (text_width > 109) ? text_width : 110;
	jQuery('#wpa' + id + '_bar').width(bar_width);
	// Fix for IE crapping on inline-block
	if (navigator.userAgent.match(/MSIE/i) || navigator.userAgent.match(/MSIE/i))
		jQuery('#wpa' + id + '_sub').width(bar_width);
}
function wpaTimeFormat(ms) {
	var min = Math.floor(ms / 1000 / 60);
	var sec = Math.floor(ms / 1000 % 60);
	var time_string = min + ':';
	if (sec<10) time_string += '0'; // Add leading 0 to seconds if necessary
	time_string += sec;
	return(time_string);
}
function wpaButtonCheck() {
	if (!this.playState || this.paused)
		jQuery('#' + this.sID + '_play').attr('src', wpa_url + '/wpa_play.gif');
	else
		jQuery('#' + this.sID + '_play').attr('src', wpa_url + '/wpa_pause.gif');
}
// When player is ready, convert wpaudio links to players
soundManager.onready(function(oStatus) {
	if (oStatus.success) {
		var selector = 'a.wpaudio';
		if (isset('wpa_pref_link_mp3') && wpa_pref_link_mp3) selector += ', a[href$="\.mp3"]';
		// Handle the links
		jQuery(selector).each(function() {
			// Add wpaudio class
			jQuery(this).addClass('wpaudio_' + wpa_id);
			// Get url and text
			var dl = jQuery(this).attr('href');
			var url = (jQuery(this).attr('class') && jQuery(this).attr('class').match(/wpaudio_url_\d+/)) ? wpa_urls[jQuery(this).attr('class').match(/wpaudio_url_\d+/)[0].substr(12)] : dl;
			// Check for dl - change link to # or to dl param
			var dl_html = (dl == '#') ? '' : '<a id="wpa' + wpa_id + '_dl" class="wpa_dl" href="' + dl + '">Download</a>';
			// Substitute player html
			jQuery(this).wrapInner('<span id="wpa' + wpa_id + '_text" class="wpa_text"></span>');
			jQuery(this).wrap('<span id="wpa' + wpa_id + '_container" class="wpa_container"></span>');
			jQuery(this).prepend('<img id="wpa' + wpa_id + '_play" class="wpa_play" src="' + wpa_url + '/wpa_play.gif">');
			jQuery('#wpa' + wpa_id + '_container').append('<div id="wpa' + wpa_id + '_bar" class="wpa_bar"><div id="wpa' + wpa_id + '_bar_load" class="wpa_bar_load"></div><div id="wpa' + wpa_id + '_bar_position" class="wpa_bar_position"></div><div id="wpa' + wpa_id + '_bar_click" class="wpa_bar_click"></div></div><div id="wpa' + wpa_id + '_sub" class="wpa_sub"><span class="wpa_time"><span id="wpa' + wpa_id + '_position">0:00</span> / <span id="wpa' + wpa_id + '_duration">0:00</span></span>' + dl_html + '<span id="wpa' + wpa_id + '_dl_info" class="wpa_dl_info">Right-click&nbsp;and save as to download.</span></div>');
			wpaBarWidth(wpa_id);
			// Player settings object
			var wpa_settings = {
				id: 'wpa' + wpa_id,
				url: url,
				// In the following functions, 'this' refers to the player
				whileplaying: function() {
					jQuery('#' + this.sID + '_bar_position').width(this.position/wpa[this.sID.substr(3)].duration*100 + '%');
					jQuery('#' + this.sID + '_position').text(wpaTimeFormat(this.position));
				},
				whileloading: function() {
					wpa[this.sID.substr(3)].duration = (this.bytesLoaded == this.bytesTotal) ? this.duration : this.durationEstimate;
					jQuery('#' + this.sID + '_bar_load').width(this.bytesLoaded/this.bytesTotal*100 + '%');
					jQuery('#' + this.sID + '_duration').text(wpaTimeFormat(wpa[this.sID.substr(3)].duration));
				},
				onplay: wpaButtonCheck,
				onpause: wpaButtonCheck,
				onresume: wpaButtonCheck,
				onstop: wpaButtonCheck,
				onfinish: wpaButtonCheck,
				onid3: function() {
					var id = this.sID.substr(3);
					if (jQuery('.wpaudio_' + id).hasClass('wpaudio_readid3')) {
						jQuery('.wpaudio_' + id).removeClass('wpaudio_readid3');
						var text;
						if (this.id3.artist) text = this.id3.artist;
						if (this.id3.artist && this.id3.songname) text += ' - ';
						if (this.id3.songname) text += this.id3.songname;
						jQuery('#wpa' + id + '_text').text(text);
						wpaBarWidth(id);
						this.unload();
					}
				}
			};
			// Initiate player
			wpa.push(soundManager.createSound(wpa_settings));
			// Start load if no text (for id3)
			if (jQuery(this).hasClass('wpaudio_readid3')) wpa[wpa_id].load();
			// Attach click handler - do this here so only links with players get clicks intercepted
			jQuery(this).click(function() {
				// If iPhone/iPod, let the link function normally
				if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPod/i)) return true;
				var id = jQuery(this).parent('.wpa_container').attr('id').split('_',1)[0].substr(3);
				if (!wpa[id].playState || wpa[id].paused) soundManager.pauseAll();
				soundManager.togglePause(wpa[id].sID);
				jQuery('#wpa' + id + '_bar').slideDown('slow', function(){
					jQuery('#wpa' + id + '_sub').fadeIn('slow');
				});
				jQuery('.wpa_container').not('#wpa' + id + '_container').children('.wpa_bar:visible, .wpa_sub:visible').slideUp('slow');
				return false;
			});
			// Increment wpa_id
			wpa_id++;
		});
		// Allow clicks on status bar to jog track
		jQuery('.wpa_bar_click').click(function(e) {
			var id = jQuery(this).attr('id').split('_',1)[0].substr(3);
			if (e.pageX) {
				var percent = (e.pageX - jQuery(this).offset()['left']) / jQuery(this).width();
				// This order is important -- otherwise it will only play, not change position
				if (!wpa[id].playState || wpa[id].paused) {
					soundManager.pauseAll();
					wpa[id].togglePause();
				}
				wpa[id].setPosition(wpa[id].duration * percent);
			}	
		});
		// Display download info box
		/*
		jQuery('.wpa_dl').mouseover(function(){
			var id = jQuery(this).attr('id').split('_',1)[0].substr(3);
			if (jQuery('#wpa' + id + '_dl').attr('href'))
				jQuery('#wpa' + id + '_dl_info').css('display', 'inline-block');
		});
		jQuery('.wpa_dl').mouseout(function(){
			var id = jQuery(this).attr('id').split('_',1)[0].substr(3);
			if (jQuery('#wpa' + id + '_dl').attr('href'))
				jQuery('#wpa' + id + '_dl_info').css('display', 'none');
		});
		*/
		// Autoplay
		jQuery('.wpaudio_autoplay:first').click();
	}
});
// Preload pause image
var wpa_pause_img = new Image();
wpa_pause_img.src = wpa_url + '/wpa_pause.gif';