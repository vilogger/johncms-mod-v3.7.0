<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');
$textl = $lng_profile['profile_edit'];
require('../incfiles/head.php');
require('../incfiles/lib/class.upload.php');

echo '<script type="text/javascript" src="/js/jquery.form.min.js"></script>';

echo '<style>#upload-wrapper {
width: 50%;
margin-right: auto;
margin-left: auto;
margin-top: 50px;
background: #F5F5F5;
padding: 50px;
border-radius: 10px;
box-shadow: 1px 1px 3px #AAA;
}
#upload-wrapper h3 {
padding: 0px 0px 10px 0px;
margin: 0px 0px 20px 0px;
margin-top: -30px;
border-bottom: 1px dotted #DDD;
}
#upload-wrapper input[type=file] {
border: 1px solid #DDD;
padding: 6px;
background: #FFF;
border-radius: 5px;
}
#upload-wrapper #submit-btn {
border: none;
padding: 10px;
background: #61BAE4;
border-radius: 5px;
color: #FFF;
}
#output{
padding: 5px;
font-size: small;
}
#output img {
border: 1px solid #DDD;
padding: 5px;
}</style>';

echo "<script type=\"text/javascript\">
$(document).ready(function() { 
var options = { 
target: '#output',   // target element(s) to be updated with server response 
beforeSubmit: beforeSubmit,  // pre-submit callback 
success: afterSuccess,  // post-submit callback 
resetForm: true        // reset the form after successful submit 
}; 

 $('#MyUploadForm').submit(function() { 
$(this).ajaxSubmit(options);  
// always return false to prevent standard browser submit and page navigation 
return false; 
}); 
}); 

function afterSuccess()
{
$('#submit-btn').show(); //hide submit button
$('#loading-img').hide(); //hide submit button

}

//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
{

if( !$('#imageInput').val()) //check empty input filed
{
$(\"#output\").html(\"<div class=menu>Hãy chọn tệp tin?</div>\");
return false
}

var fsize = $('#imageInput')[0].files[0].size; //get file size
var ftype = $('#imageInput')[0].files[0].type; // get file type


//allow only valid image file types 
switch(ftype)
        {
            case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
                break;
            default:
                $(\"#output\").html(\"<div class=menu><b>\"+ftype+\"</b> Định dạng file không cho phép!</div>\");
return false
        }

$('#submit-btn').hide(); //hide submit button
$('#loading-img').show(); //hide submit button
$(\"#output\").html('');  
}
else
{
//Output error to older browsers that do not support HTML5 File API
$(\"#output\").html(\"Please upgrade your browser, because your current browser lacks some new features we need!\");
return false;
}
}

//function to format bites bit.ly/19yoIPO
function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Bytes';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

</script>";

if ($user_id != $user['id']) {
    // Если не хватает прав, выводим ошибку
    echo display_error('Bạn không có quyền...');
    require('../incfiles/end.php');
    exit;
}
switch ($mod) {
    case 'avatar':
        /*
        -----------------------------------------------------------------
        Выгружаем аватар
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | ' . $lng_profile['upload_avatar'] . '</div>';
            echo '<div class="list1"><form method="post" enctype="multipart/form-data" action="/request.php?t=avatar&a=default" id="MyUploadForm">
    <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png" />
    <input name="submit"  id="submit-btn" value="Upload" type="submit" />
<img src="/images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Xin chờ..."/>
</form></div>
<div id="mail">
<div id="output"></div>
</div>';

        break;

    case 'up_photo':
        echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | ' . $lng_profile['upload_photo'] . '</div>';

            echo '<div class="list1"><form method="post" enctype="multipart/form-data" action="/request.php?t=cover&a=default" id="MyUploadForm">
    <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png" />
    <input name="submit"  id="submit-btn" value="Upload" type="submit" />
<img src="/images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Xin chờ..."/>
</form></div>
<div id="mail">
<div id="output"></div>
</div>';

        break;

    case 'mail_photo':
        echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | Upload ảnh nền tin nhắn</div>';
        if (isset($_POST['submit'])) {
            $handle = new upload($_FILES['imagefile']);
            if ($handle->uploaded) {
                // Обрабатываем фото
                $handle->file_new_name_body = $user['id'];
                //$handle->mime_check = false;
                $handle->allowed = array(
                    'image/jpeg',
                    'image/gif',
                    'image/png'
                );
                $handle->file_max_size = 1024 * $set['flsz'];
                $handle->file_overwrite = true;
                $handle->image_resize = true;
                $handle->image_x = 640;
                $handle->image_y = 480;
                $handle->image_ratio_no_zoom_in = true;
                //$handle->image_ratio_y = true;
                $handle->image_convert = 'jpg';
                $handle->process('../files/users/mail-photo/');
                if ($handle->processed) {
                    // Обрабатываем превьюшку
                    $handle->file_new_name_body = $user['id'] . '_small';
                    $handle->file_overwrite = true;
                    $handle->image_resize = true;
                    $handle->image_x = 100;
                    $handle->image_ratio_y = true;
                    $handle->image_convert = 'jpg';
                    $handle->process('../files/users/mail-photo/');
                    if ($handle->processed) {
                        echo '<div class="gmenu"><p>' . $lng_profile['photo_uploaded'] . '<br /><a href="profile.php?act=edit&amp;user=' . $user['id'] . '">' . $lng['continue'] . '</a></p></div>';
                        echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '">' . $lng['profile'] . '</a></div>';
                    } else {
                        echo functions::display_error($handle->error);
                    }
                } else {
                    echo functions::display_error($handle->error);
                }
                $handle->clean();
            }
        } else {
            echo '<form enctype="multipart/form-data" method="post" action="profile.php?act=images&amp;mod=mail_photo&amp;user=' . $user['id'] . '"><div class="menu"><p>' . $lng_profile['select_image'] . ':<br />' .
                '<input type="file" name="imagefile" value="" />' .
                '<input type="hidden" name="MAX_FILE_SIZE" value="' . (1024 * $set['flsz']) . '" /></p>' .
                '<p><input type="submit" name="submit" value="' . $lng_profile['upload'] . '" /></p>' .
                '</div></form>' .
                '<div class="phdr"><small>' . $lng_profile['select_image_help'] . ' ' . $set['flsz'] . 'kb.<br />' . $lng_profile['select_image_help_5'] . '<br />Ảnh nền cũ sẽ được thay mới khi upload.</small></div>';
        }
        break;
}
?>