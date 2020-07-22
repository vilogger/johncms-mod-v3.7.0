<?php
$comment_id = $_GET['comment_id'];
$html = '';
if (!empty($comment_id)){
$html .= '<div class="window-container">
    <div class="window-background" onclick="SK_closeWindow();"></div>

    <div class="window-wrapper">
        <div class="window-header-wrapper">
            <i class="fa fa-minus-sign"></i> 

            Xóa bình luận?

            <span class="window-close-btn" title="Close window" onclick="cancelCommentRemove('.$comment_id.');">
                <i class="fa fa-times"></i>
            </span>
        </div>

        <div class="button-content-wrapper">
            <button class="active" onclick="removeComment('.$comment_id.');">
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
}