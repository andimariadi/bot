<?php
function sendMessage($message_text) {
    //group teknisi = 
    $url = "https://api.telegram.org/bot689728551:AAHSzCBcJWKe4xxOJwUWm2kDy5npSX5Frrw/sendMessage?parse_mode=markdown&chat_id=-274726845";
    $url = $url . "&text=" . urlencode($message_text);
    $ch = curl_init();
    $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);
}

function curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}
$message_text = "Portable Down, Portable Down, Portable Down!";
sendMessage($message_text);
?>