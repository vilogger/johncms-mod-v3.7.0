/**
 * 
 * HTML5 Responsive Video Player
 * For jQuery 1.9.1 and above
 * 
 * @author  Rik de Vos
 * @link    http://rikdevos.com/
 * @version 1.0.1
 *
 * This is not free software. Visit http://codecanyon.net/user/RikdeVos to purchase a license
 * 
 */

(function($){
	$.html5_video = function(el, options){
		// To avoid scope issues, use 'base' instead of 'this'
		// to reference this class from internal events and functions.
		var base = this;
		
		// Access to jQuery and DOM versions of element
		base.$el = $(el);
		base.el = el;
		
		// Add a reverse reference to the DOM object
		base.$el.data("html5_video", base);
		
		base.init = function(){
			base.options = $.extend({},$.html5_video.defaultOptions, options);

			base.$el.addClass('resp');

			// Globals
			base.$controls = [];
			base.$title = null;
			base.$video = null;
			base.info = {
				'duration': 0,
				'volume': base.options.volume,
				'state': 'stop', // 'play', 'pause', 'stop'
				'time_drag': false,
				'volume_drag': false,
				'ie': base.detect_ie(),
				'ie_previous_time': 0,
				'touch': base.detect_touch(),
				'first_load': false,
				'ios': ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false ),
				'ratio': false,
			};

			base.completeloaded = false;
			
			// Add <video> tag
			base.create_video_element();

			// Add video controls
			base.create_controls();
			base.bind_controls();

			// Add title
			base.create_title();

			// Add overlays such as sharing and loading icons
			base.create_overlays();

			base.$el.hover(function() {
				base.show_controls();
			}, function() {
				if(base.options.show_controls_on_pause && base.$video[0].paused) {

				}else {
					base.hide_controls();
				}
				base.hide_share();
			});

			base.init_time_slider();
			base.init_volume_slider();

			base.$video.on('canplay', base.hide_buffer);

			base.$video.on('click', base.play_pause);
			if(base.options.dblclick_fullscreen) {
				base.$video.on('dblclick', base.fullscreen);
			}
			
			base.$video.on('loadedmetadata', base.init_video);

			base.$video.on('timeupdate', base.video_time_update);

			base.$video.on('canplaythrough', function() {
				base.completeloaded = true;
			});

			base.$video.on('ended', function() {
				base.$video[0].load();
				base.stop();
			});

			base.$video.on('seeking', function() {
				if(!base.completeloaded) { 
					base.show_buffer();
				}
			});

			base.$video.on('waiting', function() {
				base.show_buffer();
			});

			if(base.options.autoplay) {
				// Hide controls
				setTimeout(function() {
					base.hide_controls();
				}, 1000);
			}else {
				// Show big play button
				//base.$big_play.show();
			}

			if(base.options.show_controls_on_load || base.options.show_controls_on_pause) {
				base.show_controls();
			}

			if(base.info.ios) {
				base.$el.find('.resp-controls').remove();
				base.$title.css('opacity', 1);

				// Fix scaling issue
				if(base.options.poster !== false) {
					var img = new Image();
					img.src = base.options.poster;
					img.onload = function() {
						base.info.ratio = this.width/this.height;
						//alert(base.info.ratio)
						base.resize();
						// base.$video.attr('width', width).css('width', width);
						// base.$video.attr('height', width/ratio).css('height', width/ratio);
					}
				}
			}

			if(base.options.width !== false) {
				base.$el.css('width', base.options.width);
			}

			if(base.info.ie !== false) {
				// this is ie :(
				base.ie_fix();
			}
			
			$(window).on('resize', base.resize);
			base.resize();
			
			base.update_volume(0, base.options.volume);

		};
		
		base.create_video_element = function() {
			this.$video = $('<video width="100%"><p>Sorry, your browser does not support HTML5 video.</p></video>');

			if(base.options.autoplay) {
				this.$video.attr('autoplay', 'autoplay');
			}

			if(this.options.poster !== false) {
				this.$video.attr('poster', this.options.poster);
				this.$el.css({
					'background': 'url('+this.options.poster+') top left no-repeat',
					'background-size': '100%',
				});
			}
			for(source in this.options.source) {
				$('<source />')
					.attr('src', this.options.source[source])
					.attr('type', source)
					.appendTo(this.$video);
			}
			this.$video.appendTo(this.$el);
		};

		base.create_controls = function() {
			var $controls = $('<div class="resp-controls"></div>');

			$controls.html('<div class="resp-controls-wrapper"><a href="#" class="resp-play fa fa-play"></a><div class="resp-time">00:00</div><div class="resp-bar"><div class="resp-bar-buffer"></div><div class="resp-bar-time"></div></div><div class="resp-volume"><a href="#" class="resp-volume-icon fa fa-volume-up" title="Toggle Mute"></a><div class="resp-volume-bar"><div class="resp-volume-amount"></div></div></div><a href="#" class="resp-share fa fa-share-square-o" title="Share"></a><a href="#" class="resp-fullscreen fa fa-expand" title="Toggle Fullscreen"></a></div>');

			base.$controls['play'] = $controls.find('.resp-play');
			base.$controls['time'] = $controls.find('.resp-time');
			base.$controls['time_bar'] = $controls.find('.resp-bar');
			base.$controls['time_bar_buffer'] = $controls.find('.resp-bar-buffer');
			base.$controls['time_bar_time'] = $controls.find('.resp-bar-time');
			base.$controls['volume'] = $controls.find('.resp-volume');
			base.$controls['volume_icon'] = $controls.find('.resp-volume-icon');
			base.$controls['volume_bar'] = $controls.find('.resp-volume-bar');
			base.$controls['volume_amount'] = $controls.find('.resp-volume-amount');
			base.$controls['share'] = $controls.find('.resp-share');
			base.$controls['fullscreen'] = $controls.find('.resp-fullscreen');

			if(!base.options.play_control) {
				base.$controls['play'].hide();
			}

			if(!base.options.time_indicator) {
				base.$controls['time'].hide();
			}

			if(!base.options.volume_control) {
				base.$controls['volume'].hide();
			}

			if(!base.options.share_control) {
				base.$controls['share'].hide();
			}

			if(!base.options.fullscreen_control) {
				base.$controls['fullscreen'].hide();
			}

			$controls.css({
				background: base.options.color
			});

			$controls.appendTo(this.$el);
		};

		base.create_title = function() {
			this.$title = $('<div class="resp-title"></div>');
			this.$title.html('<div class="resp-title-wrapper">'+this.options.title+'</div>');

			this.$title.appendTo(this.$el);

			if(base.options.title == '' || base.options.title == false) {
				base.$title.hide();
			}
		};

		base.create_overlays = function() {
			base.$loading = $('<div class="resp-loading resp-center">'+base.options.buffering_text+'</div>');
			base.$loading.appendTo(base.$el);
			if(!base.info.ios) {
				base.$loading.css('display', 'inline-block');
			}

			base.$big_play = $('<a href="#" class="resp-big-play resp-center fa fa-play"></a>')
				.click(function(e) {
					e.preventDefault();
					base.play();
				})
				.appendTo(base.$el);

			base.$big_replay = $('<a href="#" class="resp-big-replay resp-center fa fa-undo"></a>')
				.click(function(e) {
					e.preventDefault();
					base.play();
				})
				.appendTo(base.$el);

			base.$social = $('<div class="resp-social" data-show="0"><a href="#" class="resp-social-button resp-social-google fa fa-google-plus"></a><a href="#" class="resp-social-button resp-social-twitter fa fa-twitter"></a><a href="#" class="resp-social-button resp-social-facebook fa fa-facebook"></a></div>')
				.appendTo(base.$el);

			base.$social.find('.resp-social-facebook').click(function(e) {
				e.preventDefault();
				base.share_facebook();
			});
			base.$social.find('.resp-social-twitter').click(function(e) {
				e.preventDefault();
				base.share_twitter();
			});
			base.$social.find('.resp-social-google').click(function(e) {
				e.preventDefault();
				base.share_google();
			});

		};

		base.share_link = function() {

		},

		base.share_facebook = function() {
			window.open('https://www.facebook.com/sharer/sharer.php?u='+base.share_url(), 'Share on Facebook', "height=300,width=600");
		},

		base.share_twitter = function() {
			window.open('https://twitter.com/home?status='+base.share_url(), 'Share on Twitter', "height=300,width=600");
		},

		base.share_google = function() {
			window.open('https://plus.google.com/share?url='+base.share_url(), 'Share on Google+', "height=300,width=600");
		},

		base.bind_controls = function() {

			base.$controls['play'].click(function(e) {
				e.preventDefault();
				base.play_pause();
			});

			base.$controls['fullscreen'].click(function(e) {
				e.preventDefault();
				base.fullscreen();
			});

			base.$controls['volume_icon'].click(function(e) {
				e.preventDefault();
				if(base.$video[0].volume == 0) {
					// unmute
					if(base.info.volume == 0) {
						base.info.volume = base.options.volume;
					}

					base.update_volume(0, base.info.volume);
				}else {
					// mute
					var previous_vol = base.$video[0].volume;
					base.update_volume(0, 0);
					base.info.volume = previous_vol;
				}
			});

			base.$controls['share'].click(function(e) {
				e.preventDefault();
				base.toggle_share();
			});

		};

		base.show_controls = function() {
			base.$el.find('.resp-controls').stop().animate({
				'bottom': 0
			}, 250);

			base.$title.stop().animate({
				'opacity': 1
			}, 250);
		};

		base.hide_controls = function() {
			base.$el.find('.resp-controls').stop().animate({
				'bottom': -50
			}, 250);

			base.$title.stop().animate({
				'opacity': 0
			}, 250);
		};

		base.init_video = function() {

			base.$video.removeAttr('controls');

			//var video = base.$video[0];

			base.info.duration = base.$video[0].duration;

			// Get video buffer data
			setTimeout(base.start_buffer, 150);

			base.resize();

		};

		base.video_time_update = function() {
			var currentPos = base.$video[0].currentTime;
			var maxduration = base.$video[0].duration;
			var perc = 100 * currentPos / maxduration;
			base.$controls['time_bar_time'].css('width',perc+'%');	
			base.$controls['time'].html(base.format_time(currentPos));	
			base.options.on_time_update(base.$video[0].currentTime);
		};

		base.start_buffer = function() {

			var buffered = base.$video[0].buffered.end(0),
				perc = 100 * buffered / base.info.duration;
			if(perc > 100) { perc = 100; }
			base.$controls['time_bar_buffer'].css('width',perc+'%');
				
			if(buffered < base.info.duration) {
				setTimeout(base.start_buffer, 500);
			}

		};

		base.hide_buffer = function() {
			if(base.info.first_load == false && base.options.autoplay == false) {
				if(!base.info.ios) {
					base.$big_play.show();
				}
				base.info.first_load = true;
			}
			base.$loading.hide();
		};

		base.show_buffer = function() {
			if(base.info.ios) {
				return;
			}
			base.$loading.show();
		};

		base.play_pause = function() {
			if(base.$video[0].paused || base.$video[0].ended) {
				base.play();
			}else {
				base.pause();
			}
		};

		base.play = function() {
			base.$video[0].play();
			base.info.state = 'play';
			base.$controls['play'].removeClass('fa-play').addClass('fa-pause').removeClass('fa-undo');
			base.$big_play.hide();
			base.$big_replay.hide();
			base.hide_share();
			base.options.on_play();
		};

		base.pause = function() {
			base.$video[0].pause();
			base.info.state = 'pause';
			base.$controls['play'].addClass('fa-play').removeClass('fa-pause').removeClass('fa-undo');
			base.$big_play.hide();
			base.$big_replay.hide();
			base.options.on_pause();
		};

		base.stop = function() {
			base.$video[0].pause();
			base.info.state = 'stop';
			base.$controls['play'].removeClass('fa-play').removeClass('fa-pause').addClass('fa-undo');
			base.show_controls();
			base.$big_play.hide();
			if(!base.info.ios) {
				base.$big_replay.show();
			}
			if(base.options.share_control) {
				base.show_share();
			}
			base.options.on_stop();
		};

		base.fullscreen = function() {
			var i = base.$video[0];

			if (i.requestFullscreen) {
			    i.requestFullscreen();
			} else if (i.webkitRequestFullscreen) {
			    i.webkitRequestFullscreen();
			} else if (i.mozRequestFullScreen) {
			    i.mozRequestFullScreen();
			} else if (i.msRequestFullscreen) {
			    i.msRequestFullscreen();
			}else {
				alert('Your browser doesn\'t support fullscreen');
			}
		};

		base.resize = function() {
			var width = base.$el.innerWidth(),
				height = base.$el.innerHeight();

			var bar_width = width - 20; // Minus the padding
			
			if(base.options.play_control) {
				bar_width -= 30;
			}

			if(base.options.time_indicator) {
				bar_width -= 58;
			}

			if(base.options.volume_control) {
				bar_width -= 110;
			}

			if(base.options.share_control) {
				bar_width -= 30;
			}

			if(base.options.fullscreen_control) {
				bar_width -= 30;
			}

			bar_width -= 18; // Minus the bar's margin;

			base.$controls['time_bar'].css('width', bar_width);

			base.$el.find('.resp-center').each(function() {
				var el_width = $(this).width(),
					el_height = $(this).height();
				$(this).css({
					left: (width/2)-(el_width/2),
					top: (height/2)-(el_height/2),
				});
			});

			if(base.info.ios && base.info.ratio !== false) {
				base.$video.attr('height', width/base.info.ratio).css('height', width/base.info.ratio);
				base.$el.css('height', width/base.info.ratio);
			}

		};

		base.init_time_slider = function() {
			base.$controls['time_bar'].on('mousedown', function(e) {
				base.info.time_drag = true;
				base.update_time_slider(e.pageX);
			});
			$(document).on('mouseup', function(e) {
				if(base.info.time_drag) {
					base.info.time_drag = false;
					base.update_time_slider(e.pageX);
				}
			});
			$(document).on('mousemove', function(e) {
				if(base.info.time_drag) {
					base.update_time_slider(e.pageX);
				}
			});
		};

		base.update_time_slider = function(x) {

			var maxduration = base.info.duration;
			var position = x - base.$controls['time_bar'].offset().left;
			var percentage = 100 * position / base.$controls['time_bar'].width();

			if(percentage > 100) {
				percentage = 100;
			}
			if(percentage < 0) {
				percentage = 0;
			}
			base.$controls['time_bar_time'].css('width',percentage+'%');	
			base.$video[0].currentTime = maxduration * percentage / 100;
			base.options.on_seek(base.$video[0].currentTime);
		};

		base.init_volume_slider = function() {
			base.$controls['volume_bar'].on('mousedown', function(e) {
				base.info.volume_drag = true;
				base.$video[0].muted = false;
				base.$controls['volume_icon'].removeClass('fa-volume-off').addClass('fa-volume-up');
				base.update_volume(e.pageX);
			});
			$(document).on('mouseup', function(e) {
				if(base.info.volume_drag) {
					base.info.volume_drag = false;
					base.update_volume(e.pageX);
				}
			});
			$(document).on('mousemove', function(e) {
				if(base.info.volume_drag) {
					base.update_volume(e.pageX);
				}
			});
		};

		base.update_volume = function(x, vol) {
			var percentage;

			if(vol) {
				percentage = vol * 100;
			}
			else {
				var position = x - base.$controls['volume_bar'].offset().left;
				percentage = 100 * position / base.$controls['volume_bar'].width();
			}
			
			if(percentage > 100) {
				percentage = 100;
			}
			if(percentage < 0) {
				percentage = 0;
			}
			
			base.$controls['volume_amount'].css('width',percentage+'%');	
			base.$video[0].volume = percentage / 100;
			base.info.volume = base.$video[0].volume;
			
			if(base.$video[0].volume == 0){
				base.$controls['volume_icon'].addClass('fa-volume-off').removeClass('fa-volume-up');
			}else {
				base.$controls['volume_icon'].removeClass('fa-volume-off').addClass('fa-volume-up');
			}

			base.options.on_volume(base.info.volume);
			
		};

		base.toggle_share = function() {

			if(base.$social.attr('show') == '1') {
				base.hide_share();
			}else {
				base.show_share();
			}
		};

		base.show_share = function() {
			if(base.info.ios) {
				return;
			}
			base
				.$social.attr('show', '1')
				.stop()
				.animate({
					right: 10
				}, 200);
		};

		base.hide_share = function() {
			base
				.$social.attr('show', '0')
				.stop()
				.animate({
					right: -140
				}, 200);
		};

		base.format_time = function(seconds) {
			var m = Math.floor(seconds / 60) < 10 ? "0" + Math.floor(seconds / 60) : Math.floor(seconds / 60),
				s = Math.floor(seconds - (m * 60)) < 10 ? "0" + Math.floor(seconds - (m * 60)) : Math.floor(seconds - (m * 60));
			return m + ":" + s;
		};

		base.share_url = function() {
			return window.location.href;
		};

		base.detect_ie = function() {
		    var ua = window.navigator.userAgent;
		    var msie = ua.indexOf('MSIE ');
		    var trident = ua.indexOf('Trident/');

		    if (msie > 0) {
		        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
		    }

		    if (trident > 0) {
		        var rv = ua.indexOf('rv:');
		        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
		    }

		    // other browser
		    return false;
		};

		base.detect_touch = function() {
			return !!('ontouchstart' in window) || !!('onmsgesturechange' in window);
		};

		base.ie_fix = function() {
			setInterval(function() {
				var time = base.$video[0].currentTime;
				if(time-base.info.ie_previous_time == time) {
					// not playing
					
				}else {
					// playing
					base.hide_buffer();
				}
				base.info.ie_previous_time = time;
			}, 50);
		};
		
		// Run initializer
		base.init();
	};
	
	$.html5_video.defaultOptions = {

		// Options
		source: 				[],
		title: 					'',
		color: 					'#e6bc57',
		width: 					false,
		poster: 				false,
		buffering_text: 		'Buffering',
		autoplay: 				false,
		play_control: 			true,
		time_indicator: 		true,
		volume_control: 		true,
		share_control: 			true,
		fullscreen_control: 	true,
		dblclick_fullscreen: 	true,
		
		volume: 				0.7,
		
		show_controls_on_load: 	true,
		show_controls_on_pause: true,

		// Callbacks
		on_play: 		function() {},
		on_pause: 		function() {},
		on_stop: 		function() {},
		on_seek: 		function(seconds) {},
		on_volume: 		function(volume) {},
		on_time_update: function(seconds) {},

	};


	$.fn.html5_video = function(options){
		return this.each(function(){
			(new $.html5_video(this, options));
		});
	};

	// Plugin API

	$.fn.html5_video_play = function(){
		return this.each(function(){
			(new $.html5_video_play(this));
		});
	};

	$.html5_video_play = function(el){
		var $el = $(el),
			base = $el.data("html5_video");
		base.play();
	};


	$.fn.html5_video_pause = function(){
		return this.each(function(){
			(new $.html5_video_pause(this));
		});
	};

	$.html5_video_pause = function(el){
		var $el = $(el),
			base = $el.data("html5_video");
		base.pause();
	};


	$.fn.html5_video_stop = function(){
		return this.each(function(){
			(new $.html5_video_stop(this));
		});
	};

	$.html5_video_stop = function(el){
		var $el = $(el),
			base = $el.data("html5_video");
		$el.html5_video_seek(base.info.duration);
	};


	$.fn.html5_video_seek = function(seconds){
		return this.each(function(){
			(new $.html5_video_seek(this, seconds));
		});
	};

	$.html5_video_seek = function(el, seconds){
		var $el = $(el),
			base = $el.data("html5_video");

		var maxduration = base.info.duration,
			percentage = seconds / maxduration * 100;

		base.$controls['time_bar_time'].css('width',percentage+'%');	
		base.$video[0].currentTime = seconds;
		base.options.on_seek(seconds);
	};


	$.fn.html5_video_volume = function(volume){
		return this.each(function(){
			(new $.html5_video_volume(this, volume));
		});
	};

	$.html5_video_volume = function(el, volume){
		var $el = $(el),
			base = $el.data("html5_video");

		base.update_volume(0, volume);
	};
	
})(jQuery);