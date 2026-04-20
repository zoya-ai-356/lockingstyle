<?php
function sendTelegramOTP($otp, $type = "ADMIN") {
    $botToken = "8700087681:AAFLSuXCYC6cuU0hZnqdAQ2N67vDaVOpwWw"; 
    $chatId   = "6192278212"; 
    $message = "🔐 *LOCKINGSTYLE SECURITY*\nTarget: $type\nCode: $otp\nTime: ".date('H:i A');
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $data = ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'Markdown'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
?>