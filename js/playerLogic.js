var btn;
var startSongPressed = false;
var currentPlay;
function JPlayerStart(song)
{

var myPlayer = $("#jquery_jplayer_1"),
		myPlayerData,
		fixFlash_mp4, // Flag: The m4a and m4v Flash player gives some old currentTime values when changed.
		fixFlash_mp4_id, // Timeout ID used with fixFlash_mp4
		ignore_timeupdate, // Flag used with fixFlash_mp4
		options = {
			ready: function (event) {
				// Hide the volume slider on mobile browsers. ie., They have no effect.
				if(event.jPlayer.status.noVolume) {
					// Add a class and then CSS rules deal with it.
					$(".jp-gui").addClass("jp-no-volume");
				}
				// Determine if Flash is being used and the mp4 media type is supplied. BTW, Supplying both mp3 and mp4 is pointless.
				fixFlash_mp4 = event.jPlayer.flash.used && /m4a|m4v/.test(event.jPlayer.options.supplied);
				// Setup the player with media.

				$(this).jPlayer("setMedia", {
					 mp3: song,
					
				});
			},
			timeupdate: function(event) {
			
			if(event.jPlayer.status.currentTime >= event.jPlayer.status.duration && event.jPlayer.status.currentTime > 0 && event.jPlayer.status.duration > 0)
			{
			
				$(currentPlay).parents("li").next().find(".resultsPlus").click();
			}
				if(!ignore_timeupdate) {
					myControl.progress.slider("value", event.jPlayer.status.currentPercentAbsolute);
				}
			},
			volumechange: function(event) {
				if(event.jPlayer.options.muted) {
					myControl.volume.slider("value", 0);
				} else {
					myControl.volume.slider("value", event.jPlayer.options.volume);
				}
			},
			swfPath: "../js",
			solution:"html",
			supplied: "mp3, oga",
			cssSelectorAncestor: "#jp_container_1",
			wmode: "window",
			keyEnabled: true,
			
		},
		myControl = {
			progress: $(options.cssSelectorAncestor + " .jp-progress-slider"),
			volume: $(options.cssSelectorAncestor + " .jp-volume-slider")
		};

	// Instance jPlayer
	myPlayer.jPlayer(options);
	myPlayer.jPlayer("play");
	// A pointer to the jPlayer data object
	myPlayerData = myPlayer.data("jPlayer");

	// Define hover states of the buttons
	$('.jp-gui ul li').hover(
		function() { $(this).addClass('ui-state-hover'); },
		function() { $(this).removeClass('ui-state-hover'); }
	);

	// Create the progress slider control
	myControl.progress.slider({
		animate: "fast",
		max: 100,
		range: "min",
		step: 0.1,
		value : 0,
		slide: function(event, ui) {
			var sp = myPlayerData.status.seekPercent;
			if(sp > 0) {
				// Apply a fix to mp4 formats when the Flash is used.
				if(fixFlash_mp4) {
					ignore_timeupdate = true;
					clearTimeout(fixFlash_mp4_id);
					fixFlash_mp4_id = setTimeout(function() {
						ignore_timeupdate = false;
					},1000);
				}
				// Move the play-head to the value and factor in the seek percent.
				myPlayer.jPlayer("playHead", ui.value * (100 / sp));
			} else {
				// Create a timeout to reset this slider to zero.
				setTimeout(function() {
					myControl.progress.slider("value", 0);
				}, 0);
			}
		}
	});

	$(" .jp-progress-slider").slider("value",0);
	myControl.volume.slider({
		animate: "fast",
		max: 1,
		range: "min",
		step: 0.01,
		value : $.jPlayer.prototype.options.volume,
		slide: function(event, ui) {
			myPlayer.jPlayer("option", "muted", false);
			myPlayer.jPlayer("option", "volume", ui.value);
		}
	});
	//myPlayer.jPlayer("setMedia", {mp3: "/Music/a.mp3"}).jPlayer("play");
	//$(myPlayer).jPlayer("setMedia", {mp3: song}).jPlayer("play");
	}

function StartSong(sender)
{
startSongPressed = true;
	currentPlay = sender;
	var mp3Player = $("#mp3player");
	//$(".currentPlayer").remove();
	$("#mp3player").remove();
	
	var placeholder = $(sender).parent().find(".mp3Placeholder");
	$(placeholder).html("<div id='mp3player'>"+mp3Player.html()+"</div>");
	var song = $(sender).parent().find("#norber").html();
	var playButton = $(sender).parent().find(".jp-play");
	JPlayerStart(song);
	$(".jp-progress-slider > .ui-slider-range").attr("style","overflow:hidden");
	btn = $(playButton);
	//$(playButton).click();
	setTimeout(function(){ 
	
		$(btn).click();
	
	},1000);
	startSongPressed = false;
	//$("#jquery_jplayer_1").jPlayer("play", 42)
		
}

$(document).ready(function(){
	/*$(document).on("click",".songName",function(event){ 
		while(startSongPressed){}
		if(typeof event.srcElement !== "undefined")
			StartSong(event.srcElement); 
	});
	
	
	$(document).on("click",".playBtnSmall",function(event){ 
	while(startSongPressed){}
		if(typeof event.srcElement !== "undefined")
			StartSong(event.srcElement); 
	});
	
	$(document).on("click",".playButton",function(event){ 
	while(startSongPressed){}
		if(typeof event.srcElement !== "undefined")
			StartSong(event.srcElement); 
	});

	$(document).on("click",".resultsPlus",function(event){ 
	while(startSongPressed){}
		if(typeof event.srcElement !== "undefined")
			StartSong(event.srcElement); 
	});*/

	
	
});