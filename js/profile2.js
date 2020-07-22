$(function() {

    setInterval(function() {
        
        if ($('.listcmt').length > 0)
        {
            element = $('.post-id');
            request_data = new Object();
            request_data.t = 'comment';
            request_data.a = 'filter';
            request_data.before_id = $('.listcmt:first').attr('data-comment-id');
            request_data.post_id = element.attr('data-id');
            
            $.get('/request.php', request_data, function(data) {
                
                if (data.status == 200) {
                    $('.stories-wrapper').prepend(data.html);
                }
            });
        }
    }, 6000);
});

function addload() {
    button_wrapper = $('.load-btn');
    
    SK_progressIconLoader(button_wrapper);
    
    outgoing_data = new Object();
    outgoing_data.t = 'comment';
    outgoing_data.a = 'filter';
    outgoing_data.post_id = button_wrapper.attr('data-id');
    
    if ($('.listcmt').length > 0)
    {
        outgoing_data.start_row = $('.listcmt').length;
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

// Post comment
function postComment(text, post_id, timeline_id, event)
{
    if (event.keyCode == 13 && event.shiftKey == 0)
    {
        main_wrapper = $('.status_' + post_id);
        comment_textarea = $('.comment-textarea');
        textarea_wrapper = comment_textarea.find('textarea');
        textarea_wrapper.val('');
        
        SK_progressIconLoader(comment_textarea);
        
        $.post('/request.php?t=comment&a=new&post_id=' + post_id, {text: text, timeline_id: timeline_id}, function (data) {

            if (data.status == 200) {
                $('.listcmt:first').before(data.html);
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