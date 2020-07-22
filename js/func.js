function wall_like_users_five(rec_id, type, rc){
    $('.mrt_'+rc+'_'+rec_id).hide();
    if(type == 'uPages'){
        var req = $('.like_button').attr("data-request");
        $.post('/like_view.php', {rid: rec_id, mr: rc, type: req}, function(data){
            $('#'+rc+'_users'+rec_id).html(data);
            $('#public_'+rc+'_user_block'+rec_id).show();
        });
    } else {
        $('#public_'+rc+'_user_block'+rec_id).show();
    }
};

function wall_like_users_five_hide(rec_id, rc){
    $('.mrt_'+rc+'_'+rec_id).hide();
};

                    soundcloud_query = '';
$('.soundcloud-input').bind('input propertychange', function() {
    searchSoundcloud($(this).val());
});

function searchSoundcloud(query) {
    if (query != soundcloud_query) {
        main_wrapper = $('.story-publisher-box');
        soundcloud_wrapper = main_wrapper.find('.soundcloud-search-wrapper');
        result_wrapper = soundcloud_wrapper.find('.input-result-wrapper');
        soundcloud_query = query;
        
        if (query.length == 0) {
            result_wrapper.slideUp(function(){
                $(this).html('');
            });
        } else {
            result_wrapper.html('<div class="loading-wrapper"><i class="fa fa-spinner fa-spin"></i> Đang tìm......</div>').slideDown();
            setTimeout(function () {
                if (soundcloud_query == query) {
                    getSoundcloud(query);
                }
            }, 1500);
        }
    }
}

function getSoundcloud(query) {
    main_wrapper = $('.story-publisher-box');
    soundcloud_wrapper = main_wrapper.find('.soundcloud-search-wrapper');
    result_wrapper = soundcloud_wrapper.find('.input-result-wrapper');
    
    if (query.length == 0) {
        result_wrapper.slideUp(function () {
            $(this).html('');
        });
    } else {
        query = query.replace("http://", "").replace("https://", "");
        result_wrapper.html('<div class="loading-wrapper"><i class="fa fa-spinner fa-spin"></i> Đang tìm......</div>').slideDown();
        
        $.get('/request.php', {t: 'addon', a: 'soundcloud_search', q: query}, function(data) {
            
            if (data.status == 200) {
                
                if (data.type == "embed") {
                    soundcloud_wrapper
                    .append('<span class="remove-btn" onclick="removeSoundcloudData();"><i class="fa fa-times"></i></span>')
                    .find('input.soundcloud-input')
                    .hide()
                    .after('<div class="result-container"><span class="title">https://' + query + '</span><i class="fa fa-thumb-tack"></i><input type="hidden" name="soundcloud_title" value="Embedded"><input type="hidden" name="soundcloud_uri" value="' + data.sc_uri + '"></div>')
                    .val('');
                    result_wrapper.slideUp(function () {
                        $(this).html('');
                    });
                } else if (data.type == "api") {
                    result_wrapper.html(data.html);
                }
            }
            else {
                result_wrapper.html('<div class="no-wrapper">Không có kết quả!</div>');
            }
        });
    }
}

function addSoundcloudData(title,uri) {
    $('.story-publisher-box').find('.soundcloud-search-wrapper')
        .append('<span class="remove-btn" onclick="removeSoundcloudData();"><i class="fa fa-times"></i></span>')
        
        .find('input.soundcloud-input')
            .hide()
            .after('<div class="result-container"><span class="title">' + title.substr(0,70) + '</span><i class="fa fa-thumb-tack"></i><input type="hidden" name="soundcloud_title" value="' + title + '"><input type="hidden" name="soundcloud_uri" value="' + uri + '"></div>')
            .val('')
            
        .end().find('.input-result-wrapper')
            .slideUp(function () {
                $(this).html('');
            });
}

function removeSoundcloudData() {
    $('.story-publisher-box').find('.soundcloud-search-wrapper')
        .find('.result-container')
            .remove()
        .end().find('input.soundcloud-input')
            .show()
            .focus()
        .end().find('.remove-btn')
            .remove();
}

youtube_query = '';
$('.youtube-input').bind('input propertychange', function() {
    searchYoutube($(this).val());
});

function searchYoutube(query) {
    if (query != youtube_query) {
        main_wrapper = $('.story-publisher-box');
        youtube_wrapper = main_wrapper.find('.youtube-search-wrapper');
        result_wrapper = youtube_wrapper.find('.input-result-wrapper');
        youtube_query = query;
        
        if (query.length == 0) {
            result_wrapper.slideUp(function(){
                $(this).html('');
            });
        } else {
            result_wrapper.html('<div class="loading-wrapper"><i class="fa fa-spinner fa-spin"></i> Đang tìm......</div>').slideDown();
            setTimeout(function () {
                if (youtube_query == query) {
                    getYoutube(query);
                }
            }, 1500);
        }
    }
}

function getYoutube(query) {
    main_wrapper = $('.story-publisher-box');
    youtube_wrapper = main_wrapper.find('.youtube-search-wrapper');
    result_wrapper = youtube_wrapper.find('.input-result-wrapper');
    
    if (query.length == 0) {
        result_wrapper.slideUp(function () {
            $(this).html('');
        });
    } else {
        query = query.replace("http://", "").replace("https://", "");
        result_wrapper.html('<div class="loading-wrapper"><i class="fa fa-spinner fa-spin"></i> Đang tìm......</div>').slideDown();
        
        $.get('/request.php', {t: 'addon', a: 'youtube_search', q: query}, function(data) {
            
            if (data.status == 200) {
                
                if (data.type == "embed") {
                    youtube_wrapper
                    .find('.youtube-link')
                    .remove()
                    .end()
                    .find('input.youtube-input')
                    .after('<input class="youtube-link" type="hidden" name="youtube_video_id" value="' + query + '">')
                    result_wrapper.slideUp();
                } else if (data.type == "api") {
                    result_wrapper.html(data.html);
                }
                
            } else {
                result_wrapper.html('<div class="no-wrapper">Không có kết quả!</div>');
            }
        });
    }
}

function addYoutubeData(id,title) {
    $('.story-publisher-box').find('.youtube-search-wrapper')
        .append('<span class="remove-btn" onclick="removeYoutubeData();"><i class="fa fa-times"></i></span>')
        .find('input.youtube-input')
            .hide()
            .after('<div class="result-container"><span class="title">' + title.substr(0,70) + '</span><i class="fa fa-thumb-tack"></i><input type="hidden" name="youtube_title" value="' + title + '"><input type="hidden" name="youtube_video_id" value="' + id + '"></div>')
            .val('')
        .end().find('.input-result-wrapper')
            .slideUp('fast',function(){
                $(this).html('');
            });
}

function removeYoutubeData() {
    $('.story-publisher-box').find('.youtube-search-wrapper')
        .find('.result-container')
            .remove()
        .end().find('input.youtube-input')
            .show()
            .focus()
        .end().find('.remove-btn')
            .remove();
}


function toggleMediaGroup(chosen_input_selector) {
    input_wrapper = $(chosen_input_selector);
    group_id = input_wrapper.attr('data-group');
    if (input_wrapper.css('display') == "none") {
        $('.input-wrapper[data-group=' + group_id + ']')
            .slideUp()
            .find('input').val('').show()
            .end()
            .find('.result-container').remove()
            .end()
            .find('.remove-btn').remove();
        input_wrapper.slideDown();
    } else {
        $('.input-wrapper[data-group=' + group_id + ']').slideUp();
    }
}

// Photo On-Upload Function
function SK_writeStoryPhotoUpload(input) {
    parent_wrapper = $('.story-publisher-box');
    input_wrapper = parent_wrapper.find('.photo-wrapper');
    group_id = input_wrapper.attr('data-group');
    parent_wrapper.find('.photos-container').text(input.files.length + ' photo(s) selected');
    $('.input-wrapper[data-group=' + group_id + ']').slideUp();
    input_wrapper.slideDown();
}

function SK_closeWindow() {
    $(".window-container").remove(), $(document.body).css("overflow", "auto")
}

function SK_progressIconLoader(e) {
    e.each(function() {
        return progress_icon_elem = $(this).find("i.progress-icon"), default_icon = progress_icon_elem.attr("data-icon"), hide_back = !1, 1 == progress_icon_elem.hasClass("hide") && (hide_back = !0), 1 == $(this).find("i.fa-spinner").length ? (progress_icon_elem.removeClass("fa-spinner").removeClass("fa-spin").addClass("fa-" + default_icon), 1 == hide_back && progress_icon_elem.hide()) : progress_icon_elem.removeClass("fa-" + default_icon).addClass("fa fa-spinner fa-spin").show(), !0
    })
}

function SK_progressImageLoader(e) {
    e.each(function() {
        return elm=$(this),"none"==elm.css("display")?(elm.next("i.progress-icon").remove(),elm.show()):(elm.hide(),elm.after('<i class="fa fa-spinner fa-spin"></i>'))
    })
}

// request

$(function() {

    setInterval(function() {
        
        if ($('.chatbox-wrapper .menu').length > 0)
        {
            element = $('.chatbox-container');
            request_data = new Object();
            request_data.t = 'chatbox';
            request_data.a = 'filter';
            request_data.before_id = $('.chatbox-wrapper .menu:first').attr('data-story-id');

            $.get('/request.php', request_data, function(data) {

                if (data.status == 200) {
                    $('.chatbox-wrapper').prepend(data.html);
                }
            });
        }
    }, 5000);

    setInterval(function() {
        
        if ($('.stories-wrapper .sttlist').length > 0)
        {
            element = $('.stories-container');
            request_data = new Object();
            request_data.t = 'post';
            request_data.a = 'filter';
            request_data.before_id = $('.sttlist:first').attr('data-story-id');
            
            if (typeof(element.attr('data-story-timeline') != "undefined")) {
                request_data.timeline_id = element.attr('data-story-timeline');
            }
            
            $.get('/request.php', request_data, function(data) {
                
                if (data.status == 200) {
                    $('.stories-wrapper').prepend(data.html);
                }
            });
        }
    }, 6000);
});