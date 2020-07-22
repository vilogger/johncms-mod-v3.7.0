<?php
define('_IN_JOHNCMS', 1);
require_once('../incfiles/core.php');
$textl = 'Chia sẽ Hình ảnh miễn phí';
require('../incfiles/head.php');
if (!$user_id) {
    echo functions::display_error($lng['access_forbidden']);
    require('../incfiles/end.php');
    exit;
}
?>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery.form.min.js"></script>
<script type="text/javascript">
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
$("#output").html("<div class=menu>Hãy chọn tệp tin?</div>");
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
                $("#output").html("<div class=menu><b>"+ftype+"</b> Định dạng file không cho phép!</div>");
return false
        }

$('#submit-btn').hide(); //hide submit button
$('#loading-img').show(); //hide submit button
$("#output").html("");  
}
else
{
//Output error to older browsers that do not support HTML5 File API
$("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
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

</script>
<div class="phdr"><a href="/upload-hinhanh.html">Danh sách ảnh</a> | Upload ảnh</div>
<div class="menu">
<form action="processupload.php" method="post" enctype="multipart/form-data" id="MyUploadForm">
<input name="image_file" id="imageInput" type="file" />
<input type="submit"  id="submit-btn" value="Upload" />
<img src="images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait"/>
</form>
</div>
<div id="mail">
<div id="output"></div>
</div>
<?php
require_once('../incfiles/end.php');
?>