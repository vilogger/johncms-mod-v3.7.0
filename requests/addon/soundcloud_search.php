<?php

    if (! empty($_GET['a']) && $_GET['a'] == "soundcloud_search")
    {
        if (! empty($_GET['q']))
        {
            if (preg_match('/^(soundcloud\.com)/', $_GET['q']))
            {
                $newdata = array(
                    'status' => 200,
                    'type' => 'embed',
                    'sc_uri' => $_GET['q']
                );
                
                if (!preg_match('/^(http\:\/\/|https\:\/\/)/', $_GET['q']))
                {
                    $newdata['sc_uri'] = 'https://' . $data['sc_uri'];
                }

                return $data;
            }
            else
            {
                $api_url = 'http://api.soundcloud.com/tracks.json?client_id=4346c8125f4f5c40ad666bacd8e96498&q=' . urlencode($_GET['q']);
                $api_content = @file_get_contents($api_url);
                $html = '';
                
                if (! $api_content)
                {
                    $data = "";
                }
                
                $api_content_array = json_decode($api_content, true);
                
                if (! is_array($api_content_array))
                {
                    $data = "";
                }
                
                foreach ($api_content_array as $k => $v)
                {
                    $soundcloud_title = $v['title'];
                    $soundcloud_uri = $v['uri'];
                    $soundcloud_thumbnail = $v['artwork_url'];
                    $soundcloud_genre = $v['genre'];

                    $html .= '<div class="api-data-wrapper soundcloud-api-data" onclick="addSoundcloudData(\'' . $soundcloud_title . '\',\'' . $soundcloud_uri . '\');">
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="40px" align="left" valign="middle">
                                <img class="thumbnail" src="' . $soundcloud_thumbnail . '" width="32px" height="32px" valign="middle" alt="Image">
                            </td>

                            <td align="left" valign="middle">
                                <div class="name">
                                    ' . $soundcloud_title . '
                                </div>

                                <div class="info">
                                    ' . $soundcloud_genre . '
                                </div>
                            </td>
                        </tr>
                        </table>
                    </div>';
                }
                
                if (! empty($html))
                {
                    $data = array(
                        'status' => 200,
                        'type' => 'api',
                        'html' => $html
                    );
                }
            }
        }
    }

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
exit();