<?php
$continue = false;

if ($_POST['timeline_id'] == $user_id){
    $timelineId = $_POST['timeline_id'];
    $continue = true;
}

if (isset($_FILES['image']['tmp_name']) && $continue == true){
    $image = $_FILES['image'];
    $avatar = registerMedia($image);

    if (isset($avatar['id']))
    {
        $query = mysql_query("UPDATE `users` SET avatar_extension='" . $avatar['extension'] . "' WHERE id=" . $timelineId);
        
        if ($query)
        {
            $data = array(
                'status' => 200,
                'avatar_url' => $home . '/' . $avatar['url'] . '_100x100.' . $avatar['extension']
            );
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
exit();