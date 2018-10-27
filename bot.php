<?php

include 'bot_ass.php';
/*
id group = -274726845
BOT PENGANTAR
Materi EBOOK: Membuat Sendiri Bot Telegram dengan PHP
Ebook live http://telegram.banghasan.com/
oleh: bang Hasan HS
id telegram: @hasanudinhs
email      : banghasan@gmail.com
twitter    : @hasanudinhs
disampaikan pertama kali di: Grup IDT
dibuat: Juni 2016, Ramadhan 1437 H
nama file : PertamaBot.php
change log:
revisi 1 [15 Juli 2016] :
+ menambahkan komentar beberapa line
+ menambahkan kode webhook dalam mode comment
Pesan: baca dengan teliti, penjelasan ada di baris komentar yang disisipkan.
Bot tidak akan berjalan, jika tidak diamati coding ini sampai akhir.
*/
//isikan token dan nama botmu yang di dapat dari bapak bot :
$TOKEN      = "689728551:AAHSzCBcJWKe4xxOJwUWm2kDy5npSX5Frrw";
$usernamebot= "@teknisidispatchbot"; // sesuaikan besar kecilnya, bermanfaat nanti jika bot dimasukkan grup.
// aktifkan ini jika perlu debugging
$debug = false;
 
// fungsi untuk mengirim/meminta/memerintahkan sesuatu ke bot 
function request_url($method)
{
    global $TOKEN;
    return "https://api.telegram.org/bot" . $TOKEN . "/". $method;
}
 
// fungsi untuk meminta pesan 
// bagian ebook di sesi Meminta Pesan, polling: getUpdates
function get_updates($offset) 
{
    $url = request_url("getUpdates")."?offset=".$offset;
        $resp = file_get_contents($url);
        $result = json_decode($resp, true);
        if ($result["ok"]==1)
            return $result["result"];
        return array();
}
// fungsi untuk mebalas pesan, 
// bagian ebook Mengirim Pesan menggunakan Metode sendMessage
function send_reply($chatid, $msgid, $text)
{
    global $debug;
    $data = array(
        'chat_id' => $chatid,
        'text'  => $text,
        'parse_mode'=>'Markdown',
        'reply_to_message_id' => $msgid   // <---- biar ada reply nya balasannya, opsional, bisa dihapus baris ini
    );
    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options); 
    $result = file_get_contents(request_url('sendMessage'), false, $context);
    if ($debug) 
        print_r($result);
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
 
// fungsi mengolahan pesan, menyiapkan pesan untuk dikirimkan
function create_response($text, $message)
{
    global $usernamebot;
    // inisiasi variable hasil yang mana merupakan hasil olahan pesan
    $hasil = '';  
    $fromid = $message["from"]["id"]; // variable penampung id user
    $chatid = $message["chat"]["id"]; // variable penampung id chat
    $pesanid= $message['message_id']; // variable penampung id message
    // variable penampung username nya user
    isset($message["from"]["username"])
        ? $chatuser = $message["from"]["username"]
        : $chatuser = '';
    
    // variable penampung nama user
    isset($message["from"]["last_name"]) 
        ? $namakedua = $message["from"]["last_name"] 
        : $namakedua = '';   
    $namauser = $message["from"]["first_name"]. ' ' .$namakedua;
    // ini saya pergunakan untuk menghapus kelebihan pesan spasi yang dikirim ke bot.
    $textur = preg_replace('/\s\s+/', ' ', $text); 
    // memecah pesan dalam 2 blok array, kita ambil yang array pertama saja
    $command = explode(' ',$textur,2); //
    $default = "Hallo $namauser, perkenalkan Saya merupakan asisten report Mine Dispatch yang akan membantu Anda.

Anda dapat meminta saya melakukan beberapa tugas dengan menuliskan :

report
/battery - melaporkan volt battery secara real
/report tanggal/shift- melaporkan pekerjaan Jigsaw dan Network yang sudah diinputkan oleh user.
    Contoh */report 2018-06-1/1* Maka saya akan memberikan Anda list pekerjaan Jigsaw dan Network pada tanggal 1-06-2018 pada shift 1.
/summary (unit id) - melaporkan summary 20 pekerjaan terakhir pada unit yang Anda minta.
/detail (unit id) - melihat detail unit berupa series jigsaw, S/N, Backlog

Other
/id - Memberikan profile Anda
/time - menampilkan waktu local saya
/help - list command

Terima kasih telah mempercayakan saya sebagai asisten Anda. Berikan kami saran lebih baik lagi. Terima kasih :)";
    $unit = str_replace(' ', '_', @$command[1]);
    switch ($command[0]) {
        // jika ada pesan /id, bot akan membalas dengan menyebutkan idnya user
        case '/start':
        case '/start'.$usernamebot : //dipakai jika di grup yang haru ditambahkan @usernamebot
            $hasil = $default;
            break;

        case '/help':
        case '/help'.$usernamebot : //dipakai jika di grup yang haru ditambahkan @usernamebot
            $hasil = $default;
            break;

        case '/id':
        case '/id'.$usernamebot : //dipakai jika di grup yang haru ditambahkan @usernamebot
            $hasil = "$namauser, ID kamu adalah $fromid";
            break;

        case '/battery':
$hasil .= 'Berikut kami kirimkan voltage battery :';
$hasil .= '
*PT 01*       Volt : ' . GetScaledValue('201') . '';
$hasil .= '
*PT 02*       Volt : ' . GetScaledValue('202') . '';
//$hasil .= '*PT 03*       Volt : ' . GetScaledValue('203') . '';
$hasil .= '
*PT 04*       Volt : ' . GetScaledValue('204') . '';
//$hasil .= '*PT 05*       Volt : ' . GetScaledValue('205') . '';
//$hasil .= '*PT 06*       Volt : ' . GetScaledValue('206') . '';
$hasil .= '
*PT 07*       Volt : ' . GetScaledValue('207') . '';
$hasil .= '
*PT 08*       Volt : ' . GetScaledValue('208') . '';
$hasil .= '
*PT 09*       Volt : ' . GetScaledValue('209') . '';
$hasil .= '
*PT 10*       Volt : ' . GetScaledValue('210') . '';
$hasil .= '
*PT 11*       Volt : ' . GetScaledValue('211') . '';
$hasil .= '
*PT 12*       Volt : ' . GetScaledValue('212') . '';
//$hasil .= '*PT 13*       Volt : ' . GetScaledValue('213') . '';
//$hasil .= '*PT 14*       Volt : ' . GetScaledValue('214') . '';
$hasil .= '
*PT 15*       Volt : ' . GetScaledValue('215') . '';
//$hasil .= '*PT 16*       Volt : ' . GetScaledValue('216') . '';
$hasil .= '
*PT 17*       Volt : ' . GetScaledValue('217') . '';
$hasil .= '
*PT 18*       Volt : ' . GetScaledValue('218') . '';
$hasil .= '
*PT 19*       Volt : ' . GetScaledValue('219') . '';
//$hasil .= '*PT 20*       Volt : ' . GetScaledValue('220') . '';
$hasil .= '
*PT 21*       Volt : ' . GetScaledValue('221') . '';
$hasil .= '
*PT 22*       Volt : ' . GetScaledValue('222') . '';
$hasil .= '
*PT 23*       Volt : ' . GetScaledValue('223') . '';
$hasil .= '
*PT 24*       Volt : ' . GetScaledValue('224') . '';
$hasil .= '
*PT 25*       Volt : ' . GetScaledValue('225') . '';
//$hasil .= '*PT 26*       Volt : ' . GetScaledValue('226') . '';
$hasil .= '
*PT 27*       Volt : ' . GetScaledValue('227') . '';
$hasil .= '
*PT 28*       Volt : ' . GetScaledValue('228') . '';
$hasil .= '
*PT 29*       Volt : ' . GetScaledValue('229') . '';
$hasil .= '
*MTL CT*        Volt : ' . GetScaledValue('144') . '';
$hasil .= '
*PT MTL J5*   Volt : ' . GetScaledValue('197') . '';
$hasil .= '
*PT SKID 01*  Volt : ' . GetScaledValue('200') . '';
$hasil .= '
*PT SKID 02*  Volt : ' . GetScaledValue('160') . '';
            break;
        
        // jika ada permintaan waktu
        case '/time':
        case '/time'.$usernamebot :
            $hasil  = "$namauser, waktu lokal bot sekarang adalah :\n";
            $hasil .= date("d M Y")."\nPukul ".date("H:i:s");
            break;

        // jika ada permintaan waktu
        case '/report':
        case '/report'.$usernamebot :
            $datax = @$command[1];
            $json  = json_decode(curl('http://192.168.211.20/reporting/Json/report/' . $datax), true);
            if (@$json['data'] == 'empty') {
    $hasil = "Hi {$namauser}, data pada tanggal {$json['date']} shift {$json['shift']} tidak ada.";
} else {
    $hasil = "";
    if (empty($json['jigsaw'])) {
        
    } else {
        $hasil .= "*Report Data Jigsaw " . $json['date'] . " / Shift {$json['shift']}*

";
        foreach ($json['jigsaw'] as $value) {
$hasil .= '*' . $value['no'] . ').* *ID Unit* : ' . $value['unit_id'] . '
*Problem* : ' . $value['problem'] .  '
*Action*   : ' . $value['activity'] . '
*Duration*: ' . $value['duration'] . '
*PIC*        : ' . $value['pic'] . '

';
        }

    }

    if (empty($json['network'])) {
        
    } else {
        $hasil .= "*Report Data Network " . $json['date'] . " / Shift {$json['shift']}*

";
        foreach ($json['network'] as $value) {
$hasil .= '*' . $value['no'] . ').* *ID Unit* : ' . $value['unit_id'] . '
*Problem* : ' . $value['problem'] .  '
*Action*   : ' . $value['activity'] . '
*Duration*: ' . $value['duration'] . '
*PIC*        : ' . $value['pic'] . '

';
        }

    }
}
            break;

        // jika ada permintaan waktu
        case '/summary':
        case '/summary'.$usernamebot :
            $json  = json_decode(curl('http://192.168.211.20/reporting/Json/summary/' . $unit), true);
            if (empty($unit)) {
                $hasil = "Hi {$namauser}, command kurang lengkap. Ketik /help untuk list menu.";
            } else {
                $hasil .= "*== Summary Report {$unit} ==*
";
                foreach ($json['data'] as $value) {
$hasil .= '*' . $value['no'] . ').* *Date*  : ' . $value['date'] . '
*ID Unit* : ' . $value['unit_id'] . '
*Problem* : ' . $value['problem'] .  '
*Action*   : ' . $value['activity'] . '
*Duration*: ' . $value['duration'] . '
*PIC*        : ' . $value['pic'] . '

';
                }
            }
            break;

        // jika ada permintaan waktu
        case '/detail':
        case '/detail'.$usernamebot :
            $json  = json_decode(curl('http://192.168.211.20/reporting/Json/detail/' . $unit), true);
            if (empty($unit)) {
                $hasil = "Hi {$namauser}, command kurang lengkap. Ketik /help untuk list menu.";
            } else {
                $hasil .= "*== Detail {$unit} ==*
";
                foreach ($json['data'] as $value) {
                    if ($value['device_id'] == 1) {
                        $hasil .= '*ID Unit* : ' . $value['id'] . '
*Series*     : ' . $value['series_name'] . '
*Type Truck* : ' . $value['code_name'] . '
*S/N Display*: ' . $value['sn_display'] . '
*S/N WB*     : ' . $value['sn_wb'] . '
*S/N GPS*    : ' . $value['sn_gps'] . '
*Antenna*    : ' . $value['antenna_name'] . '
*Power Off*  : ' . $value['poweroff_name'] . '
*Backlog*    : ' . $value['backlog'] . '
*Status*     : ' . $value['status'] . '
*Locked*     : ' . $value['locked'] . '
';
                    } else {
                        $hasil .= '*ID Unit* : ' . $value['id'] . '
*Type*       : ' . $value['code_name'] . '
*Position*   : ' . $value['position'] . '
*Battery*    : ' . $value['battery'] . '
*Backlog*    : ' . $value['backlog'] . '
*Status*     : ' . $value['status'] . '
';
                    }
                }
            }
            break;

        // balasan default jika pesan tidak di definisikan
        default:
            
            break;
        
    }
    return $hasil;
}
 
// jebakan token, klo ga diisi akan mati
// boleh dihapus jika sudah mengerti
if (strlen($TOKEN)<20) 
    die("Token mohon diisi dengan benar!\n");
// fungsi pesan yang sekaligus mengupdate offset 
// biar tidak berulang-ulang pesan yang di dapat 
function process_message($message)
{
    $updateid = $message["update_id"];
    $message_data = $message["message"];
    if (isset($message_data["text"])) {
    $chatid = $message_data["chat"]["id"];
        $message_id = $message_data["message_id"];
        $text = $message_data["text"];
        $response = create_response($text, $message_data);
        if (!empty($response))
          send_reply($chatid, $message_id, $response);
    }
    return $updateid;
}
 
// hapus baris dibawah ini, jika tidak dihapus berarti kamu kurang teliti!
//die("Mohon diteliti ulang codingnya..\nERROR: Hapus baris atau beri komen line ini yak!\n");
 
// hanya untuk metode poll
// fungsi untuk meminta pesan
// baca di ebooknya, yakni ada pada proses 1 
function process_one()
{
    global $debug;
    $update_id  = 0;
    echo "-";
 
    if (file_exists("last_update_id")) 
        $update_id = (int)file_get_contents("last_update_id");
 
    $updates = get_updates($update_id);
    // jika debug=0 atau debug=false, pesan ini tidak akan dimunculkan
    if ((!empty($updates)) and ($debug) )  {
        echo "\r\n===== isi diterima \r\n";
        print_r($updates);
    }
 
    foreach ($updates as $message)
    {
        echo '+';
        $update_id = process_message($message);
    }
    
    // update file id, biar pesan yang diterima tidak berulang
    file_put_contents("last_update_id", $update_id + 1);
}
// metode poll
// proses berulang-ulang
// sampai di break secara paksa
// tekan CTRL+C jika ingin berhenti 

while (true) {
    process_one();
    sleep(1);
}

// process_one();
// metode webhook
// secara normal, hanya bisa digunakan secara bergantian dengan polling
// aktifkan ini jika menggunakan metode webhook
/*
$entityBody = file_get_contents('php://input');
$pesanditerima = json_decode($entityBody, true);
process_message($pesanditerima);
*/
/*
 * -----------------------
 * Grup @botphp
 * Jika ada pertanyaan jangan via PM
 * langsung ke grup saja.
 * ----------------------
 
* Just ask, not asks for ask..
Sekian.
*/
    
?>
