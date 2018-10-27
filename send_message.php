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

$json = json_decode(curl('http://192.168.211.20/reporting/json/report'), true);
if (@$json['data'] == 'empty') {
    $message_text = "Hi Teknisi, tanggal {$json['date']} shift {$json['shift']} tidak ada data.";
} else {
    $message_text = "";
    if (empty($json['jigsaw'])) {
        
    } else {
        $message_text .= "*Report Data Jigsaw " . $json['date'] . " / Shift {$json['shift']}*

";
        foreach ($json['jigsaw'] as $value) {
$message_text .= '*' . $value['no'] . ').* *ID Unit* : ' . $value['unit_id'] . '
*Problem* : ' . $value['problem'] .  '
*Action*   : ' . $value['activity'] . '
*Duration*: ' . $value['duration'] . '
*PIC*        : ' . $value['pic'] . '

';
        }

    }

    if (empty($json['network'])) {
        
    } else {
        $message_text .= "*Report Data Network " . $json['date'] . " / Shift {$json['shift']}*

";
        foreach ($json['network'] as $value) {
$message_text .= '*' . $value['no'] . ').* *ID Unit* : ' . $value['unit_id'] . '
*Problem* : ' . $value['problem'] .  '
*Action*   : ' . $value['activity'] . '
*Duration*: ' . $value['duration'] . '
*PIC*        : ' . $value['pic'] . '

';
        }

    }
}
sendMessage($message_text);
?>