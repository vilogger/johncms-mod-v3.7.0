

// Load old stories
function SK_loadOldStories() {
    body_wrapper = $('.chatbox-container');
    button_wrapper = $('.chatbox-container').find('.load-btn');
    
    SK_progressIconLoader(button_wrapper);
    
    outgoing_data = new Object();
    outgoing_data.t = 'chatbox';
    outgoing_data.a = 'filter';
    
    if ($('.chatbox-wrapper .menu').length > 0)
    {
        outgoing_data.start_row = $('.chatbox-wrapper .menu').length;
    }
    
    $.get('/request.php', outgoing_data, function (data) {
        
        if (data.status == 200)
        {
            $('.chatbox-wrapper').append(data.html);

            if (data.html.length == 0)
            {
                button_wrapper.text('Không còn nội dung để xem.').removeAttr('onclick');
            }
        }
        
    SK_progressIconLoader(button_wrapper);        
    });
}

$('.story-publisher-box form').ajaxForm({
    url: '/request.php?t=chatbox&a=new',
    
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
            $('.chatbox-wrapper').prepend(responseText.html);
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