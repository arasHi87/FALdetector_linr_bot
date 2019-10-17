<?php

function ErrLog($text) {
    $handle = fopen('log.txt', 'a');
    $txt = '[ ' . date("Y-m-d H:i:s") . ' ] ' . $text . "\n";
    fwrite($handle, $txt);
    fclose($handle);
}

function reply($text) {
    ErrLog($event['replyToken']);
    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
            [
                'type' => 'text',
                'text' => $text
            ]
        ]
    ]);
}
