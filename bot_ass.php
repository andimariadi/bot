<?php
function base_url($value='/') {
    return 'http://' . $_SERVER['SERVER_NAME'] . '/battery/' . $value;
}

function kode($value='')
{
    $str        = strtoupper($value);
    $ret        = str_replace(array('A', 'E', 'U', 'I', 'O'), '', $str);
    $result     = substr($ret, 0, 2);
    return ucwords($result);
}

function price($value='')
{
    $price = number_format($value, 0);
    $value = 'Rp. ' . $price;
    return $value;
}

function autonum($values='')
{
    $value = number_format($values);
    if ($value < 9) {
        $hasil = '0' . number_format($value+1);
    } else {
        $hasil = number_format($value+1);       
    }
    return $hasil;
}

function check_number($start=1,$end=10, $ex=array('')) {
    while( in_array( ($n = rand($start,$end)), $ex));
    return $n;
}


// MPPT FUNCTION
function MBJSReadModbusInts($PT, $ALO=38){
    $curl = curl_init('http://192.168.212.' . $PT . '/MBCSV.cgi?ID=1&F=4&AHI=0&ALO=' . $ALO .'&RHI=0&RLO=1');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($curl, CURLOPT_TIMEOUT, 1);
    $info = curl_exec($curl);
    curl_close($curl);
    $d = explode(",", $info);
    $a = $d[2];
    $i = 3;
    $g= "";
    $h = NULL;
    while ($i < $a+2) {
        $h = ($d[$i++]*256);
        $h+=$d[$i++];
        if ($i < $a+2) {
            $g +=  $h . "#";
        } else {
            $g += $h;
        }
    }
    return $g;
}


  function GetScaledValue($PT, $ALO=38, $F="V") {
    $D = 0;
    $D = MBJSReadModbusInts($PT, $ALO);
    $D<<=16;
    $D>>=16;
    if ($F == "V") {
        $C=180;
        $scale = $C+($C/(65535));
        $hasil = number_format((($D*$scale)/32768/1), 2);
    } elseif($F == "A") {
        $C=80;
        $scale = $C+($C/(65535));
        $hasil = number_format((($D*$scale)/32768/1), 1);
    }
    
    return $hasil;
  }


  function warna($hasil) {
    if ($hasil >= 20 ) {
        $g = '<span style="color: green">' . $hasil . '</span>';
    } elseif ($hasil < 20 AND $hasil >= 16) {
        $g = '<span style="color: #0058ff">' . $hasil . '</span>';
    } elseif ($hasil < 16 AND $hasil >= 12) {
        $g = '<span style="color: orange">' . $hasil . '</span>';
    }
    else {
        $g = '<span style="color: red">' . $hasil . '</span>';
    }
    return $g;
  }