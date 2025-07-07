<?php
function kirimTelegram($pesan) {
    $token = "7637196457:AAEQJFAbD1lkrnxEu_JJkrKiAv3-IAOv3V0"; // Token bot kamu
    $chat_ids = [
        "7324689890",
        "1826576117"
    ]; // Daftar chat ID

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
        $result = @file_get_contents($url, false, $context);

        // Debug log jika terjadi error
        if ($result === FALSE) {
            error_log("Gagal kirim ke chat_id $chat_id");
        }
    }
}
?>
