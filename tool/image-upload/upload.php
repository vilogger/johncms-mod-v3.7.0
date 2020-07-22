<?php
define('_IN_JOHNCMS', 1);
require_once('../../incfiles/core.php');
$textl = 'Chia sẽ Hình ảnh miễn phí';
require('../../incfiles/head.php');
if (!$user_id) {
    echo functions::display_error($lng['access_forbidden']);
    require('../../incfiles/end.php');
    exit;
}
?>
<script type="text/javascript">
function showLoading(){
document.getElementById('btnSubmit1').style.display='none';
document.getElementById('btnSubmit2').style.display='inline-block';
document.getElementById('loading').style.display='block';
return true;
}
</script>
<style>
.fileupload-example-4-label {
    border: 1px solid #009688;
    padding: 5px 15px;
    margin:5px 3px;
   background:#009688;
   color:white;
   border-radius:4px;
}
</style>
<?php
echo '<form action="'.$home.'/tool/image-upload/imgur.php" enctype="multipart/form-data" method="POST" onsubmit="showLoading();">

 <div class="phdr"><i class="fa fa-camera-retro"></i> Chọn một bức hình</div>

<div class="menu"> <input name="img" type="file"/><br/>

<button type="submit" name="submit" class="fileupload-example-4-label" id="btnSubmit1"><i class="fa fa-upload"></i> UPLOAD</button></a>

<button type="submit" name="submit" class="fileupload-example-4-label" id="btnSubmit2" style="display: none;"><i class="fa fa-refresh fa-spin"></i> Xin chờ trong giây lát...</button>

<img src="http://i.imgur.com/XJICQrg.gif" id="loading" style="display: none;"></div>

</form>';
require_once('../../incfiles/end.php');
?>