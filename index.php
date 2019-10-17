<?php
error_reporting(0); 
date_default_timezone_set("Asia/Taipei");

// require_once('LINEBotTiny.php');
require_once('LINEBotTinyV2.php');
require_once('functions.php');

$config             = parse_ini_file("/home/me/config.ini", true);
$channelAccessToken = $config['Channel']['Token'];
$channelSecret      = $config['Channel']['Secret'];
$client             = new LINEAPI($channelAccessToken, $channelSecret);
$msgobj             = new LINEMSG();

foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            $token   = $event['replyToken'];
            $uid     = $event['source']['userId'];
            $mid     = $event['message']['id'];
            $gid     = $event['source']['groupId'];
            switch ($message['type']) {
                case 'text':
                     $text = $message['text'];
                     if (substr($text, 0, 7)=='sakura:') {
                        $text = explode('sakura:', $text)[1];
                        switch ($text) {
                            case 'test':
                                $text = '測試成功!(ﾉ>ω<)ﾉ';
                                $client->replyMessage($event['replyToken'], $msgobj->Text($text));
                                break;
                            case substr($text, 0, 7)=='warped!':
                                $mid = explode(' ', $text)[1];
                                $url = 'https://yiarashi.com/line-bk/images/' . $uid . '/' . $mid . '/warped.jpg';
                                $client->replyMessage($event['replyToken'], $msgobj->Image($url));
                            case substr($text, 0, 5)=='heat!':
                                $mid = explode(' ', $text)[1];
                                $url = 'https://yiarashi.com/line-bk/images/' . $uid . '/' . $mid . '/heatmap.jpg';
                                $client->replyMessage($event['replyToken'], $msgobj->Image($url));
                            default:
                                $text = '你個傻逼，看不懂你寫甚麼辣!( ´･ω)';
                                $client->replyMessage($event['replyToken'], $msgobj->Text($text));
                                break;
                        };
                      }
                    // $client->replyMessage($event['replyToken'], $msgobj->Text($message['text']));
                    break;
                case 'image':
                    if ($gid != '') break;
                    if (!is_dir('./images/' . $uid))
                        mkdir('./images/' . $uid, 0755);
                    mkdir('./images/' . $uid . '/' . $mid, 0755);
                    $path = './images/' . $uid . '/' . $mid . '/' . 'origin.jpg';
                    $client->downloadMessageObject($mid, $path);
                    $img_path  = 'images/' . $uid . '/' . $mid .'/origin.jpg';
                    $save_path = 'images/' . $uid . '/' . $mid;
                    $res       = [];
                    $cmd       = 'sudo docker exec ai-sys bash -c "source /root/virenv/AI_TEST/bin/activate && python /root/FALdetector/local_detector.py --input_path ' . $img_path . ' --model_path weights/local.pth --dest_folder ' . $save_path . ' --no_crop"';
                    exec($cmd, $res);
                    if ($res[0]!='Success!') {
                        $text = '出了些錯誤!請再檢查一次確定圖片裡有包含顯而易見的你，或者請稍後再嘗試看看,或者只是你在太醜系統辨認不出來而已>3';
                        $client->replyMessage($event['replyToken'], $msgobj->Text($text));
                    } else {
                        $text      = 'Detected result!';
                        $heat      = 'https://yiarashi.com/line-bk/images/' . $uid . '/' . $mid . '/heatmap.jpg';
                        $warped    = 'https://yiarashi.com/line-bk/images/' . $uid . '/' . $mid . '/warped.jpg';
                        $templates = array(
                            'type'    => 'carousel',
                            'columns' => array(
                                array(
                                    'thumbnailImageUrl' => $heat,
                                    'title'              => 'Heat image',
                                    'text'               => '修改過後的熱力圖',
                                    'actions'            => array(
                                        array(
                                            'type'  => 'message',
                                            'label' => '熱力圖下載!',
                                            'text'  => 'sakura:heat! ' . $mid
                                        )
                                    )
                                ),
                                array(
                                    'thumbnailImageUrl' => $warped,
                                    'title'              => 'warped image',
                                    'text'               => '修復後的圖片',
                                    'actions'            => array(
                                        array(
                                            'type'  => 'message',
                                            'label' => '修復圖下載!',
                                            'text'  => 'sakura:warped! ' . $mid
                                        )
                                    )
                                )
                            )
                        );
                        $client->replyMessage($event['replyToken'], $msgobj->Template($text, array(
                            'type'    => 'carousel',
                            'columns' => array(
                                array(
                                    'thumbnailImageUrl' => $heat,
                                    'title'              => 'Heat image',
                                    'text'               => '修改過後的熱力圖',
                                    'actions'            => array(
                                        array(
                                            'type'  => 'postback',
                                            'label' => '熱力圖下載!',
                                            'data'  => 'heat ' . $mid
                                        )
                                    )
                                ),
                                array(
                                    'thumbnailImageUrl' => $warped,
                                    'title'              => 'warped image',
                                    'text'               => '修復後的圖片',
                                    'actions'            => array(
                                        array(
                                            'type'  => 'postback',
                                            'label' => '修復圖下載!',
                                            'data'  => 'warped ' . $mid
                                        )
                                    )
                                )
                            )
                        )));
                    }
                default:
                    error_log("Unsupporeted message type: " . $message['type']);
                    break;
            }
            break;
        case 'postback':
            $text = $event['postback']['data'];
            switch ($text) {
                case 'author':
                    $uri = 'https://line.me/ti/p/9b6fTPgT4L';
                    $client->replyMessage($event['replyToken'], $msgobj->Text($uri));
                    break;
                case 'teach':
                    $text = 'Hi, 我是一個剛開發完的逆向修圖機器人,只要丟圖片給我就能有結果囉! 請耐心等待結果,大約需要5-10秒左右的時間喔! 如果有問題歡迎回報 ~';
                    $client->replyMessage($event['replyToken'], $msgobj->Text($text));
                    break;
                case substr($text, 0, 6)=='warped':
                    $mid = explode(' ', $text)[1];
                    $url = 'https://yiarashi.com/line-bk/images/' . $event['source']['userId'] . '/' . $mid . '/warped.jpg';
                    ErrLog($url);
                    $client->replyMessage($event['replyToken'], $msgobj->Image($url));
                case substr($text, 0, 4)=='heat':
                    $mid = explode(' ', $text)[1];
                    $url = 'https://yiarashi.com/line-bk/images/' . $event['source']['userId'] . '/' . $mid . '/heatmap.jpg';
                    $client->replyMessage($event['replyToken'], $msgobj->Image($url));
                default:
                    error_log("Unsupporeted poseback command: " . $text);
                    break;
            }
            break;
        case 'follow': // frient add event
            $client->replyMessage($event['replyToken'], $msgobj->Template('歡迎使用!', array(
                'type'              => 'buttons',
                'thumbnailImageUrl' => 'https://yiarashi.com/line-bk/origin.jpg',
                'title'             => '初次見面!',
                'text'              => '點擊按鈕獲得幫助!',
                'actions'           => array(
                    array(
                        'type'  => 'postback',
                        'label' => '獲得作者資料',
                        'data'  => 'author'
                    ),
                    array(
                        'type'  => 'postback',
                        'label' => '獲得使用說明',
                        'data'  => 'teach'
                    ),
                    array(
                        'type'  => 'uri',
                        'label' => '作者個人網站(暫定)',
                        'uri'   => 'https://yiarashi.com/'
                    )
                )
            )));
            break;
        case 'join': // Group add event
            $text = 'Hi, 謝謝邀我進群組, 但我現在不支援群組功能喔!';
            $client->replyMessage($event['replyToken'], $msgobj->Text($text));
            break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
};
