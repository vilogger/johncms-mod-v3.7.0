<?php
define('_IN_JOHNCMS', 1);
require_once('../incfiles/core.php');
if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
    if(!isset($_FILES['image_file']) || !is_uploaded_file($_FILES['image_file']['tmp_name'])){
        die('<div class="menu">Files không tồn tại!</div>');
     }
    //uploaded file info we need to proceed
    $image_name = $_FILES['image_file']['name']; //file name
    $image_size = $_FILES['image_file']['size']; //file size
    $image_temp = $_FILES['image_file']['tmp_name']; //file temp
    $image_size_info = getimagesize($image_temp); //get image size
    if($image_size_info){
        $image_type = $image_size_info['mime']; //image type
    }else{
        die("<div class=menu>Không thể lấy dữ liệu ảnh!</div>");
    }
    $filename = $image_temp;
    $client_id = "efe8365a1a23765";
    $handle = fopen($filename, "r");
    $data = fread($handle, filesize($filename));
    $pvars = array('image' => base64_encode($data));
    $timeout = 30;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
    $out = curl_exec($curl);
    curl_close ($curl);
    $pms = json_decode($out,true);
    $url = $pms['data']['link'];
    $size = substr(($pms['data']['size']/1024),0,4);
    $time = time();
    $id = isset($id) ? $id:$user_id;
    @unlink($filename);
    if(!empty($url)){
        mysql_query("INSERT INTO `cms_image` SET
            `user` = '$user_id',
            `time` = '$time',
            `size` = '$size',
            `url` = '$url'
        ");
        $id_new = mysql_insert_id();
        echo '<div class="list1" style="border-width: 1px; margin-top: 2px;"><center><b><font size=4><font color=red>Tải Ảnh Lên Thành Công!!</font></font></b></center></div>';
        echo '<div class=list1><center><img style="max-width: 100%;" src="'.$url.'"/></center><center><div style="background:#9C27B0;border:2px solid #9C27B0;margin-top: 2px;padding:4px;width:45%;text-align:center;border-radius:2px;"><a href="'.$url.'"><b><font color=#ffffff>Download ảnh ('.$size.'KB)</font></b></a></div></center>Chia sẻ:<br /><form><input value="[img='.$url.']" /></form>
<form><input value="[img]'.$url.'[/img]" /></form></div>';
    }else{
        echo $pms['data']['error'];  
    }
}else{
    echo 'Error!';
}