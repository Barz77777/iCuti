<?php
function kirimTelegram($pesan)
{
    $token = "7283511005:AAFjGPP1EHd9MBRHlP8MXHWVfnq29X2xTkc"; // Ganti token bot kamu

    $chat_ids = [
        "7324689890", // Chat ID kamu
        "1826576117"  // Chat ID teman kamu
    ];

    foreach ($chat_ids as $chat_id) {
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
        @file_get_contents($url, false, $context); // Tambahkan @ untuk hindari warning error langsung ke output
    }
}
