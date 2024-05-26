<?php

use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Support\Facades\Log;

function Substring($text, $count) {
    $toReturn = substr($text, 0, $count);
    $rest = explode($toReturn, $text);
    if ($rest[1] != "") {
        $toReturn .= "...";
    }
    return $toReturn;
}

function like($needle, $haystack, $reversed = false) {
    if ($reversed) {
        $cond = strpos($needle, $haystack);
    } else {
        $cond = strpos($haystack, $needle);
    }
    return $cond === false ? false : true;
}

function changeEnv($key, $newValue, $delim = "") {
    $path = base_path('.env');
    $oldValue = env($key);
    $newDelim = "";
    $oldDelim = "";

    if ($oldValue == $newValue) return;
    
    if (file_exists($path)) {
        if (like(" ", $newValue)) {
            $newDelim = '"';
        }
        if (like(" ", $oldValue)) {
            $oldDelim = '"';
        }
        file_put_contents($path, str_replace(
            $key.'='.$oldDelim.$oldValue.$oldDelim,
            $key.'='.$newDelim.$newValue.$newDelim,
            file_get_contents($path)
        ));
    }
}

function arahSurat() {
    return explode(",", env('ARAH_SURAT'));
}

function initial($name) {
    $names = explode(" ", $name);
    $toReturn = $names[0][0];
    if (count($names) > 1) {
        $toReturn .= $names[count($names) - 1][0];
    }

    return strtoupper($toReturn);
}

function currency_encode($angka, $currencyPrefix = '$', $thousandSeparator = ',') {
    return $currencyPrefix.' '.strrev(implode($thousandSeparator,str_split(strrev(strval($angka)),3)));
}
function currency_decode($rupiah) {
    return intval(preg_replace("/,.*|[^0-9]/", '', $rupiah));
}

function getBlockedUser($token, $key = 'token') {
    $blockedUserIDs = [];
    $user = null;
    
    if ($token) {
        $user = User::where($key, $token)->first();
        
        $blockedUsers = UserBlock::where('blocker_id', $user->id)->orWhere('blocked_id', $user->id)->get();
        foreach ($blockedUsers as $blocked) {
            if ($blocked->blocker_id == $user->id) {
                array_push($blockedUserIDs, $blocked->blocked_id);
            } else {
                array_push($blockedUserIDs, $blocked->blocker_id);
            }
        }
    }
    return $blockedUserIDs;
}