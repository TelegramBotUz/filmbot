<?php
function dump($what){
    echo '<pre>'; 
        print_r($what); 
    echo '</pre>';
};

function get_data($url){
    return json_decode(file_get_contents($url), true);
};
function get_data_post($url,$params){
    $myCurl = curl_init();
    curl_setopt_array($myCurl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params)
    ));
    $response = curl_exec($myCurl);
    curl_close($myCurl);
    if (!curl_error($myCurl)) return $response;
};

function bot($method = "getMe", $params = []){
    $url = "https://api.telegram.org/bot".API_KEY."/" . $method;
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_HTTPHEADER => ['Content-Type:multipart/form-data'],
    ]);
    $res = curl_exec($curl);
    // dump(curl_getinfo($curl));
    curl_close($curl);
    return !curl_error($curl) ? json_decode($res, true) : false;
};

function custom_file($file_path){
    return new CURLFile($file_path);
};

function html($text){
    return str_replace(['<','>'],['&#60;','&#62;'],$text);
};

function reformat($json){
    return json_encode(json_decode($json, true), JSON_PRETTY_PRINT);
};
function json($arr){
    return json_encode($arr, JSON_PRETTY_PRINT);
};
function isTime($needTime){
    return date("H:i", time()) == $needTime;
};

function save_file($file_id){
    global $work_folder;
    $result = bot('getFile', ['file_id' => $file_id]);
    if($result["ok"]){
        $file = $result["result"];
        $array = explode(".", $file['file_path']);
        $file = [
            "name" => $file['file_unique_id'],
            "format" => array_pop($array),
            "telegram_path" => "https://api.telegram.org/file/bot".API_KEY."/".$file['file_path'],
            "file_size" => round($file['file_size'] / 1024, 0, PHP_ROUND_HALF_UP)
        ];
        $new_path = "files/".$file['name'].".".$file['format'];
        if(copy($file['telegram_path'], $new_path)) $file['local_path'] = $work_folder."".$new_path;
        return $file;
    }else{
        return false;
    };
};

function db_mysql($test=false){
    global $db_mysql;
    if($db_mysql['status']){
        require_once "rb.php";
        if(!R::testConnection()){
        R::setup('mysql:host='.$db_mysql["host"].';dbname='.$db_mysql["name"].'',$db_mysql["user"],$db_mysql["pass"]);
    };
    if($test){
        print_r($db_mysql);
        echo R::testConnection() ? "GLobal db bor": "Global db yuq";
        dump(R::inspect());
    };
}
}
function fileCloud($flag="c", $params=[]){
    db_mysql();
    if($flag=="c" && $params['file_id'] && $params['name'] && $params['fs_id'] && $params['city'] && $params['data'] && $params['caption'] && $params['lang_id'] && $params['kategoriya_id'] && $params['year']){
        $file=R::dispense('cloud');
        $file->fileId=''.$params['file_id'].'';
        $file->caption=$params['caption'];
        $file->name=$params['name'];
        $file->lang_id=$params['lang_id'];
        $file->kategoriya_id=$params['kategoriya_id'];
        $file->data=$params['data'];
        $file->city=$params['city'];
        $file->year=$params['year'];
        $file->fs_id=$params['fs_id'];
        R::store($file);
    }else if($flag=="f" && $params['id']){
        $file=R::findOne('cloud', 'id=?',[$params["id"]]);
    }else if($flag=="r" && $params['id']){
        $file=R::findAll('cloud','id =? ORDER BY id DESC LIMIT 10', [$params["id"]]);
           
        };
  return $file ?:[];      
}

function photo($flag="c", $params=[]){
    db_mysql();
    if($flag=="c" && $params['cloud_id'] && $params['file_id']){
        $file=R::dispense('rasm');
        $file->file_id=''.$params['file_id'].'';
        $file->cloud_id=$params['cloud_id'];
        R::store($file);
    }else if($flag=="f" && $params['id']){
        $file=R::findOne('rasm', 'id=?',[$params["cloud_id"]]);
    }else if($flag=="f1" && $params['cloud_id']){
        $file=R::findOne('rasm', 'cloud_id=?',[$params["cloud_id"]]);
    }else if($flag=="r" ){
        $file=R::findAll('rasm','ORDER BY id DESC LIMIT 10');
        };
  return $file ?:[];      
}
function fileCloud1($flag="r", $params=[]){
    db_mysql();
 if($flag=="r" && $params['type']){
         $last_cond = '';
         if($params['last_id']) $last_cond = '<'.$params['last_id'].'';
        $file1 = R::findAll('cloud','type = ? AND ID '.$last_cond.' ORDER BY ID DESC LIMIT 10', [$params["type"]]);
        };
  return $file1 ?:[];      
}
function lang($flag="r"){
    db_mysql();
 if($flag=="r" ){
        $file = R::findAll('lang','ORDER BY ID ASC');
        };
  return $file ?:[];      
}
function fileCloudKategoriya($flag="r"){
    db_mysql();
 if($flag=="r" ){

        $file = R::findAll('kategoriya',' ORDER BY ID ASC ');
        };
  return $file ?:[];      
}
$dices=['🎲','🎯','🎳','🎰','🏀','⚽️'];

function is_admin($from_id, $chat_id){
    $res = bot('getChatMember',[
       'chat_id'=> $chat_id,
       'user_id'=> $from_id
       ]);
     return in_array($res['result']['status'], ['creator', 'admin']) ?: false;
}
// echo fileCloud("c", $params['file_id'],$params['method'],$params['type']);
function user_is_followed($user_id){
    global $chat_id, $fallow_time;
    $file = "datas/allow_".$chat_id."_".$user_id.".temp";
    if(file_exists($file) && filemtime($file) >= time()-($fallow_time * 3600)){
        return true;
    }else{
        global $fallows;
        $count = 0;
        $count_verf = 0;
        $stss = ['creator', 'administrator', 'member'];
        $res_str = [];
        foreach ($fallows as $channel){
            if($channel["required"]){
                $count++;
                    $res = get_data('https://api.telegram.org/bot'.API_KEY.'/getChatMember?chat_id='.$channel['chat_id'].'&user_id=' . $user_id)['result'];
                //$res = bot('getChatMember', [
               //     'chat_id' => $channel['chat_id'],
              //      'user_id'=> $user_id
              //  ])['result'];
                $res_str[] = $res;
                if(in_array($res['status'], $stss)){
                    $count_verf++;
                };
            };
        };
        file_put_contents('ress.log', json_encode($res_str));
        return ($count_verf == $count) ? (file_put_contents($file, 1) != false ? true : false) : false;
    }
};
function get_fallows($params = []){
    global $fallows, $share_btn;
    $list_channels = [];
    foreach ($fallows as $channel) {
        $list_channels[][] = ['text' => $channel['text_btn'], 'url'=> $channel['link']];
    };
    if($params['test_btn']){
        array_push($list_channels, [
            [
                'text' => "Obuna bo'ldim ✅",
                'callback_data' => "followed"
            ]
        ]);
    }else if($params['share_btn']){
        array_push($list_channels, [
            [
                'text' => $share_btn['share_btn'],
                'url' => 'https://t.me/share/url?url='.$share_btn['share_link'].'&text='.$share_btn['share_text']
            ]
        ]);
    };
    return $list_channels;
};



