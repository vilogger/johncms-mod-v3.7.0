<?php
    /** Mod ReaCtions for JohnCMS By MrT98
    * NhanhNao.Xyz Team CMS
    * Copyright by MrT98
    * Mọi thắc mắc và hỗ trợ tại http://nhanhnao.xyz và http://phieubac.ga
    */


    define('_IN_JOHNCMS', 1);
    require('incfiles/core.php');

    if(isset($_POST['msg_id']) && isset($_POST['rel'])) {
        $msg_id = $_POST['msg_id'];
        $rel = $_POST['rel'];
        $reqs = $_POST['req'];

        if($reqs == 'stt') {
            if (in_array($rel, array('Like','Love','Haha','Hihi','Woww','Cry','Angry'))){
                $cdata = functions::STTLike($msg_id, $user_id, $rel);
                //Check the rel type is UnLike, UnLove, UnHaha, UnHihi, UnWoww, UnCry, UnAngry
                // If rel is in array then delete it from message_like table using message id
            } else if (in_array($rel, array('UnLike','UnLove','UnHaha','UnHihi','UnWoww','UnCry','UnAngry'))) {
                $cdata = functions::STTUnlike($msg_id, $user_id, $rel);
            }
        }else{
            if (in_array($rel, array('Like','Love','Haha','Hihi','Woww','Cry','Angry'))){
                $cdata = functions::Like($msg_id, $user_id, $rel);
                //Check the rel type is UnLike, UnLove, UnHaha, UnHihi, UnWoww, UnCry, UnAngry
                // If rel is in array then delete it from message_like table using message id
            } else if (in_array($rel, array('UnLike','UnLove','UnHaha','UnHihi','UnWoww','UnCry','UnAngry'))) {
                $cdata = functions::Unlike($msg_id, $user_id, $rel);
            }
        }
        echo $cdata; 
    }
