<?php
    $photo_dir = 'files/users/avatar';
    $ext = 'png';
    $original_file_name = $photo_dir . '/' . $usid;
    $original_file = $original_file_name . '.' . $ext;
    $font = 'myfont.ttf';
    $t = $reg_nick;
    $size = 32;
    $angle = 0;
    $width = 100;
    $height = 100;
    $TextReg1 = substr($t,0,1);

    $color = array (
        mt_rand(250, 255),
        mt_rand(250, 255),
        mt_rand(250, 255)
    );

    $background_color = array (
        mt_rand(2, 222),
        mt_rand(2, 222),
        mt_rand(2, 222)
    );

    $im = @imagecreatetruecolor($width, $height);
    $background = @imagecolorallocate($im, $background_color[0], $background_color[1], $background_color[2]);
    @Imagefill($im, 0, 0, $background);

    $bbox = @imagettfbbox($size,$angle,$font,$TextReg1);
    $wb = abs($bbox[0]) + abs($bbox[2]);
    $hb = abs($bbox[1]) + abs($bbox[5]);
    if($wb < 8){
        $wb = 8;
    }
    $x = ($width-$wb)/2;
    $y = ($height+$hb)/2;
    $co = @imagecolorallocate($im, $color[0], $color[1], $color[2]);
    imagettftext($im, $size, $angle, $x, $y, $co, $font, $TextReg1);

    imagepng($im, 'files/users/avatar/'.$usid.'.png');

        list($width, $height) = getimagesize($original_file);
        $min_size = $width;

        if ($width > $height)
        {
            $min_size = $height;
        }

        $min_size = floor($min_size);

        if ($min_size > 920)
        {
            $min_size = 920;
        }
        
        $imageSizes = array(
            'thumb' => array(
                'type' => 'crop',
                'width' => 64,
                'height' => 64,
                'name' => $original_file_name . '_thumb'
            ),
            '100x100' => array(
                'type' => 'crop',
                'width' => $min_size,
                'height' => $min_size,
                'name' => $original_file_name . '_100x100'
            )
        );
        
        foreach ($imageSizes as $ratio => $data)
        {
            $save_file = $data['name'] . '.' . $ext;
            processMedia($data['type'], $original_file, $save_file, $data['width'], $data['height']);
        }
        mysql_query("UPDATE `users` SET avatar_extension='" . $ext . "' WHERE id=" . $usid);

?>