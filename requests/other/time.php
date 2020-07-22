$html = mt_rand(1, 9);
$data = array(
    'status' => 200,
    'html' => $html
);
header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
exit();