$(function(){
    $('.timeline-370').css('min-height', ($('.timeline-sidebar').height() + 150) + 'px');
    $('.cover-resize-wrapper').height($('.cover-resize-wrapper').width()*0.39);
    $('form.change-avatar-form').ajaxForm({
        url: '/request.php?t=avatar&a=new',
        
        beforeSend: function() {
            $('.avatar-progress-wrapper').html('0%<br>Tải lên').fadeIn('fast').removeClass('hidden');
            $('.avatar-change-wrapper').addClass('hidden');
        },
        
        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete+'%';
            $('.avatar-progress-wrapper').html(percentVal+'<br>Tải lên');
            
            if (percentComplete == 100) {
                
                setTimeout(function () {
                    $('.avatar-progress-wrapper').html('Đang xử lý...');
                    
                    setTimeout(function () {
                        $('.avatar-progress-wrapper').html('Xin chờ...');
                    }, 2000);
                }, 500);
            }
        },
        success: function(responseText) {
            
            if (responseText.status == 200) {
                $('.avatar-wrapper').find('img.avatar')
                    .attr('src', responseText.avatar_url + '?' + new Date().getTime())
                    .load(function() {
                        $('.avatar-progress-wrapper').fadeOut('fast').addClass('hidden').html('');
                        $('.avatar-change-wrapper').removeClass('hidden');
                    });
            }
            else {
                $('.avatar-progress-wrapper').fadeOut('fast').addClass('hidden').html('');
                $('.avatar-change-wrapper').removeClass('hidden');
            }
        }
    });
    $('form.cover-form').ajaxForm({
        url: '/request.php?t=cover&a=new',
        
        beforeSend: function() {
            $('.cover-progress')
                .html('0% Tải lên')
                .css('line-height', $('.cover-resize-wrapper').height() + 'px')
                .fadeIn('fast')
                .removeClass('hidden');
        },
        
        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete+'%';
            $('.cover-progress').html(percentVal+' Tải lên');
            
            if (percentComplete == 100) {
                
                setTimeout(function () {
                    $('.cover-progress').html('Đang xử lý...');
                    
                    setTimeout(function () {
                        $('.cover-progress').html('Xin chờ...');
                    }, 2000);
                }, 500);
            }
        },
        
        success: function(responseText) {
            
            if (responseText.status == 200) {
                $('.cover-wrapper img')
                    .attr('src', responseText.cover_url + '?' + new Date().getTime())
                    .load(function() {
                        $('.cover-progress').fadeOut('fast', function(){
                            $(this).addClass('hidden').html('');
                        });
                        $('.cover-resize-wrapper img').attr('src', responseText.actual_cover_url + '?' + new Date().getTime()).css('top', 0);
                    });
            }
            else {
                $('.cover-progress').fadeOut('fast', function(){
                    $(this).addClass('hidden').html('');
                });
                $('.cover-resize-wrapper img').css('top', 0);
            }
        }
    });
    $('form.cover-position-form').ajaxForm({
        url: '/request.php?t=cover&a=reposition',
        
        beforeSend: function() {
            $('.cover-progress').html('Đang định vị...').fadeIn('fast').removeClass('hidden');
        },
        
        success: function(responseText) {
            
            if (responseText.status == 200) {
                $('.cover-wrapper img')
                    .attr('src', responseText.url + '?' + new Date().getTime())
                    .load(function () {
                        $('.cover-progress').fadeOut('fast').addClass('hidden').html('');
                        $('.cover-wrapper').show();
                        $('.cover-resize-wrapper')
                            .hide()
                            .find('img').css('top', 0);
                        $('.cover-resize-buttons').hide();
                        $('.default-buttons').show();
                        $('input.cover-position').val(0);
                        $('.cover-resize-wrapper img').draggable('destroy').css('cursor','default');
                    });
            }
        }
    });
    $(window).resize(function () {
        cover_width = $('.cover-resize-wrapper').width();
        $('.cover-resize-wrapper').height(cover_width * 0.39);
        $('.cover-resize-wrapper img').css('top', 0);
        $('.cover-progress').css('line-height', $('.cover-resize-wrapper').height() + 'px');
        $('.screen-width').val(cover_width);
    });
});

function SK_repositionCover() {
    $('.cover-wrapper').hide();
    $('.cover-resize-wrapper').show();
    $('.cover-resize-buttons').show();
    $('.default-buttons').hide();
    $('.screen-width').val($('.cover-resize-wrapper').width());
    $('.cover-resize-wrapper img')
    .css('cursor', 's-resize')
    .draggable({
        scroll: false,
        
        axis: "y",
        
        cursor: "s-resize",
        
        drag: function (event, ui) {
            y1 = $('.timeline-header-wrapper').height();
            y2 = $('.cover-resize-wrapper').find('img').height();
            
            if (ui.position.top >= 0) {
                ui.position.top = 0;
            }
            else
            if (ui.position.top <= (y1-y2)) {
                ui.position.top = y1-y2;
            }
        },
        
        stop: function(event, ui) {
            $('input.cover-position').val(ui.position.top);
        }
    });
}

function SK_saveReposition() {
    if ($('input.cover-position').length == 1) {
        posY = $('input.cover-position').val();
        $('form.cover-position-form').submit();
    }
}

function SK_cancelReposition() {
    $('.cover-wrapper').show();
    $('.cover-resize-wrapper').hide();
    $('.cover-resize-buttons').hide();
    $('.default-buttons').show();
    $('input.cover-position').val(0);
    $('.cover-resize-wrapper img').draggable('destroy').css('cursor','default');
}

// Load old stories
function SK_loadOldStories() {
    body_wrapper = $('.stories-container');
    button_wrapper = $('.stories-container').find('.load-btn');
    
    SK_progressIconLoader(button_wrapper);
    
    outgoing_data = new Object();
    outgoing_data.t = 'post';
    outgoing_data.a = 'filter';
    
    if (typeof(body_wrapper.attr('data-story-timeline')) =="string")
    {
        outgoing_data.timeline_id = body_wrapper.attr('data-story-timeline');
    }
    
    if ($('.stories-wrapper .sttlist').length > 0)
    {
        outgoing_data.start_row = $('.stories-wrapper .sttlist').length;
    }
    
    $.get('/request.php', outgoing_data, function (data) {
        
        if (data.status == 200)
        {
            $('.stories-wrapper').append(data.html);

            if (data.html.length == 0)
            {
                button_wrapper.text('Không còn nội dung để xem.').removeAttr('onclick');
            }
        }
        
    SK_progressIconLoader(button_wrapper);        
    });
}

// Show delete post window
function SK_viewRemove(post_id) {
    main_wrapper = $('.story_' + post_id);
    button_wrapper = main_wrapper.find('.remove-btn');
    SK_progressIconLoader(button_wrapper);
    
    $.get(
        '/request.php',

        {
            t: 'post',
            post_id: post_id,
            a: 'view_remove'
        },

        function(data) {
            if (data.status == 200) {
                $(document.body)
                    .append(data.html)
                    .css('overflow','hidden');
                
                if ($('#main').width() < 920) {
                    $('.window-wrapper').css('margin-top',($(document).scrollTop()+10)+'px');
                }
            }
            
            SK_progressIconLoader(button_wrapper);
        }
    );
}

// Cancel remove
function SK_cancelRemove(post_id) {
    main_wrapper = $('.story_' + post_id);
    SK_progressIconLoader(main_wrapper.find('.remove-btn'));
    SK_closeWindow();
}

// Delete post
function SK_removePost(post_id) {
    SK_closeWindow();
    $.get('/request.php', {t: 'post', post_id: post_id, a: 'remove'}, function(data) {
        
        if (data.status == 200) {
            $('.story_' + post_id).slideUp(function(){
                $(this).remove();
            });
        }
    });
}

// Post comment
function postComment(text, post_id, timeline_id, event)
{
    if (event.keyCode == 13 && event.shiftKey == 0)
    {
        main_wrapper = $('.story_' + post_id);
        comment_textarea = main_wrapper.find('.comment-textarea');
        textarea_wrapper = comment_textarea.find('textarea');
        textarea_wrapper.val('');
        
        SK_progressIconLoader(comment_textarea);
        
        $.post('/request.php?t=post&a=comment&post_id=' + post_id, {text: text, timeline_id: timeline_id}, function (data) {
            
            if (data.status == 200) {
                main_wrapper.find('.comment-wrapper:last').before(data.html);
                main_wrapper.find('.story-comment-activity').html(data.activity_html);
            }
            
            SK_progressIconLoader(comment_textarea);
        });
    }
}
/* Like comment */
function likeComment(comment_id) {
    main_elem = $('.comment_' + comment_id);
    like_btn = main_elem.find('.comment-like-btn');
    like_activity_btn = main_elem.find('.comment-like-activity');
    
    SK_progressIconLoader(like_btn);
    
    $.get(
        '/request.php',

        {
            t: 'comment',
            comment_id: comment_id,
            a: 'like'
        },

        function(data) {
            if (data.status == 200)
            {
                if (data.liked == true)
                {
                    like_btn
                        .after(data.button_html)
                        .remove();
                    like_activity_btn
                        .html(data.activity_html);
                }
                else
                {
                    like_btn
                        .after(data.button_html)
                        .remove();
                    like_activity_btn
                        .html(data.activity_html);
                }
            }
        }
    );
}

/* View comment likes */
function viewCommentLikes(comment_id) {
    main_elem = $('.comment_' + comment_id);
    like_activity_btn = main_elem.find('.comment-like-activity');
    SK_progressIconLoader(like_activity_btn);
    
    $.get(
        '/request.php',

        {
            t: 'comment',
            comment_id: comment_id,
            a: 'view_likes'
        },

        function(data) {
            if (data.status == 200)
            {
                $(document.body)
                    .append(data.html)
                    .css('overflow','hidden');
                
                if ($('#main').width() < 920)
                {
                    $('.window-wrapper').css('margin-top', ($(document).scrollTop() + 10) + 'px');
                }
            }
            
            SK_progressIconLoader(like_activity_btn);
        }
    );
}

/* View comment remove */
function viewCommentRemove(comment_id) {
    main_wrapper = $('.comment_' + comment_id);
    button_wrapper = main_wrapper.find('.comment-remove-btn');
    
    SK_progressIconLoader(button_wrapper);
    
    $.get(
        '/request.php',

        {
            t: 'comment',
            comment_id: comment_id,
            a: 'view_remove'
        },

        function(data)
        {
            if (data.status == 200)
            {
                $(document.body)
                    .append(data.html)
                    .css('overflow','hidden');
                
                if ($('#main').width() < 920)
                {
                    $('.window-wrapper').css('margin-top', ($(document).scrollTop()+10)+'px');
                }
            }
            
            SK_progressIconLoader(button_wrapper);
        }
    );
}

/* Cancel comment remove */
function cancelCommentRemove(comment_id) {
    button = $('.comment_' + comment_id).find('.remove-btn');
    SK_progressIconLoader(button);
    SK_closeWindow();
}

/* Remove comment */
function removeComment(comment_id) {
    SK_closeWindow();

    $.get(
        '/request.php',

        {
            t: 'comment',
            comment_id: comment_id,
            a: 'remove'
        },

        function(data)
        {
            if (data.status == 200)
            {
                $('.comment_' + comment_id).slideUp(function()
                {
                    $(this).remove();
                });
            }
        }
    );
}

/* View all comments */
function viewAllComments(post_id) {
    main_wrapper = $('.story_' + post_id);
    view_more_wrapper = main_wrapper.find('.view-more-wrapper');
    
    SK_progressIconLoader(view_more_wrapper);
    
    $.get('/request.php', {t: 'post', a: 'view_all_comments', post_id: post_id}, function (data) {
        
        if (data.status == 200) {
            main_wrapper.find('.comments-wrapper').html(data.html);
            view_more_wrapper.remove();
        }
    });
}

$('.story-publisher-box form').ajaxForm({
    url: '/request.php?t=post&a=new',
    
    beforeSend: function() {
        main_wrapper = $('.story-publisher-box');
        textarea = main_wrapper.find('textarea');
        inputs = main_wrapper.find('input[type="text"]');
        button = main_wrapper.find('button.submit-btn');
        
        button_default_text = button.find('span').text();
        
        textarea.attr('disabled', true);
        inputs.attr('disabled', true);
        button.attr('disabled', true);
        SK_progressIconLoader(button);
    },
    
    success: function(responseText) {
        
        if (responseText.status == 200) {
            $('.stories-wrapper').prepend(responseText.html);
        }
        
        $('.story-publisher-box form').resetForm();
        
        main_wrapper
            .find('.story-text-input')
            .val('')
            
            .end().find('.result-container')
            .remove()
            
            .end().find('.input-wrapper')
                .find('.result-container')
                .remove()
                
                .end()
                .find('.input-result-wrapper')
                .empty()
                
                .end().find('input')
                .show()
                .val('')
                
                .end().find('.remove-btn')
                .remove()
                
                .end().find('.youtube-link')
                .remove()
            .end().slideUp();
        
        textarea.removeAttr('disabled');
        inputs.removeAttr('disabled');
        
        button
            .removeAttr('disabled')
            .find('span').text(button_default_text);
        
        SK_progressIconLoader(button);
    }
});