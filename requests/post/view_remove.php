<?php
$html = '';
$post_id = stringEscape($_GET['post_id']);
$html = '<div class="window-container">
    <div class="window-background" onclick="SK_closeWindow();"></div>

    <div class="window-wrapper">
        <div class="window-header-wrapper">
            <i class="fa fa-minus-sign"></i> 

            Xóa tâm trạng?

            <span class="window-close-btn" title="Close window" onclick="SK_cancelRemove('.$post_id.');">
                <i class="fa fa-times"></i>
            </span>
        </div>

        <div class="button-content-wrapper">
            <button class="active" onclick="SK_removePost('.$post_id.');">
                Xóa
            </button>

            <button onclick="SK_closeWindow();">
                Hủy
            </button> 
        </div>
    </div>
</div>';
$data = array(
    'status' => 200,
    'html' => $html
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
exit();