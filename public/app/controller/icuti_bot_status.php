<?php
function kirimTelegram($pesan)
{
    $token = "7283511005:AAFjGPP1EHd9MBRHlP8MXHWVfnq29X2xTkc"; // ganti dengan token bot kamu
    $chat_id =     $chat_id = [
        "7324689890",
        "1826576117"
    ]; //ID Chat

    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $pesan,
        'parse_mode' => 'HTML'
    ];

    $options = [
        "http" => [
            "method"  => "POST",
            "header"  => "Content-Type:application/x-www-form-urlencoded",
            "content" => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}
