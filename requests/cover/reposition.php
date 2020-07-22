<?php
$_POST['pos'] = stringEscape($_POST['pos']);

$position = preg_replace('/[^0-9]/', '', $_POST['pos']);
$width = 920;

if (isset($_POST['width']))
{
    $width = $_POST['width'];
}

$timelineId = $_POST['timeline_id'];
$cover_id = $timelineId;

    $cover_url = createCover($cover_id, ($position / $width));
    
    if ($cover_url)
    {
        $query = mysql_query("UPDATE `users` SET cover_position=$position WHERE id=$timelineId");

        if ($query)
        {
            $data = array(
                'status' => 200,
                'url' => $home . '/' . $cover_url
            );
        }
    }
header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
exit();