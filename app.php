<?php

require_once "config.php";
require_once "funcs.php";

$log = file_get_contents('php://input');
$update = json_decode($log);
if($update && $logging) file_put_contents("log.json", reformat($log));
// message variables
$message = $update->message;
$text = html($message->text);
$chat_id = $message->chat->id;
$chat_type = $message->chat->type;
// filelar
$document = $message->document;
$photo = $message->photo;
$audio = $message->audio;
$video = $message->video;
$sticker = $message->sticker;
$from_id=$message->from->id;
$message_id=$message->message_id;
// call back
$call = $update->callback_query;
if ($call){
    $chat_id = $call->message->chat->id;
    $chat_type = $call->message->chat->type;
    $message_id = $call->message->message_id;
    $user_id = $call->message->from->id;
    $from_id=$call->from->id;
}
$call_from_id = $call->from->id;
$call_id = $call->id;
$call_data = $call->data;
$call_message_id = $call->message->message_id;

if($message->new_chat_member || $message->left_chat_member){
    bot('deleteMessage',    [
        'chat_id' => $chat_id, 
        'message_id' =>$message->message_id
    ]);
    $new_members = $message->new_chat_members;
};

       

if($chat_type == 'private' && !is_admin($from_id, $my_group)){
  // botga start bosgan foydalanuvchi
    if($text=='/start' ){
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"Salom kino filmlar botiga hush kelibsz",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'resize_keyboard'=>true,
                'keyboard'=>[
                    [['text'=>"films"],['text'=>"serials"]],
                    [['text'=>"Oxirgi videolar"]],
                    [['text'=>"Adminga xabar qoldirish"]],
                    ]
                ])
            ]);
            
    }else if($text=='bosh sahifa'){
          bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"Bosh menu",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'resize_keyboard'=>true,
                'keyboard'=>[
                    [['text'=>"films"],['text'=>"serials"]],
                    [['text'=>"Oxirgi videolar"]],
                         [['text'=>"Adminga xabar qoldirish"]],
                    ]
                ])
            ]);
            bot('deletemessage',[
                'chat_id'=>$chat_id,
                'message_id'=>$message_id
                ]);
        }else if($text=='films'  || (mb_strpos($message->text,"film")!==false)){
            $db_file=fileCloudKategoriya("r");
           // $db=fileCloud("r");
         $files = [];
//         $c=0;
        foreach($db_file as $file){
           // foreach($db as $f){
               // file_put_contents('1a.json', json_encode($f['kategoriya']['id']));
              //  if($f['kategoriya']['id']==$file->id){
               //     $c++;
              //  }
            //}
            $files[]=[
                [
                    'text'=>$file['janr'],
                    'callback_data'=>"film||".$file['id'].""
                ]
            ];
        }
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"Serial Kategoriya",
            'reply_markup'=>json_encode([
               'inline_keyboard'=> $files
                ])
            ]);
             bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"...",
            'reply_markup'=>json_encode([
                'resize_keyboard'=>true,
               'keyboard'=> [
                   [['text'=>"bosh sahifa"]]
                   ]
                ])
            ]);
     
    }else if($text=='serials'  || (mb_strpos($message->text,"serial")!==false)){
     
         $db_file=fileCloudKategoriya("r");
         $files = [];
        foreach($db_file as $file){
            $files[]=[
                [
                    'text'=>$file['janr'],
                    'callback_data'=>"serial||".$file['id'].""
                ]
            ];
        }
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"Serial Kategoriya",
            'reply_markup'=>json_encode([
               'inline_keyboard'=> $files
                ])
            ]);
                  bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"...",
            'reply_markup'=>json_encode([
                  'resize_keyboard'=>true,
               'keyboard'=> [
                   [['text'=>"bosh sahifa"]]
                   ]
                ])
            ]);
    }else if($text=="Adminga xabar qoldirish"){
        if($message){
        bot('sendMessage',[
            'chat_id'=>1879071584,
            'text'=>$message->text
            ]);
        }
    }

    if($call){
        $f=explode('||', $call_data);
        if($f[0]=="kino"){
       $db=fileCloud("f",['id'=>$f[1]]);
       bot('sendVideo',[
           'chat_id'=>$chat_id,
           'video'=>$db['file_id'],
           'caption'=>$db['caption'],

           ]);
        bot('deletemessage',[
            'chat_id'=>$chat_id,
            'message_id'=>$call->message->message_id
            ]);
          
        }else if($f[0]=="serial"){
            $db_file=photo("r"); 
            foreach($db_file as $file){ 
                $db_cloud=fileCloud("r",['id'=>$file['cloud_id']]); 
                foreach($db_cloud as $file1){ 
                    if($file1['fs']['type']=='serial' && $file1['kategoriya_id']==$f[1]){ 
                        bot('sendphoto',[ 'chat_id'=>$chat_id, 
                        'photo'=>$file['file_id'], 
                        'caption'=>"Sarlavha: " 
                        .$file1['name']."\nTavsifi: " 
                        .$file1['caption']."\nYili: " 
                        .$file1['year']."\nTili: " 
                        .$file1['lang']['lang']."\nDavomiyligi: " 
                        .$file1['data']."\nMamlakat"
                        .$file1['city']."", 'disable_web_page_preview'=>true, 
                        'reply_markup'=>json_encode([ 
                            'inline_keyboard'=>[ 
                                [['text'=>"ko'rish",'callback_data'=>"kino||".$file1['id'].""]] 
                                ] 
                                ]) 
                                ]);
                        
                    } 
                    
                } 
                
            } 
            bot('sendmessage',[ 
                'chat_id'=>$chat_id, 
                'text'=>"davomi bor...", 
                'disable_web_page_preview'=>true,
                'reply_markup'=>json_encode([ 
                    'resize_keyboard'=>true, 
                    'keyboard'=>[ 
                        [['text'=>"bosh sahifa"]],
                         [['text'=>"serial ortga"]]

                        ] 
                        ]) 
                        ]);
        }else if($f[0]=="film"){
                $db_file=photo("r");
       
       foreach($db_file as $file){
           $db_cloud=fileCloud("r",['id'=>$file['cloud_id']]);
           foreach($db_cloud as $file1){
              if($file1['fs']['type']=='film' && $file1['kategoriya_id']==$f[1]){
                  
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>$file['file_id'],
                    'caption'=>"Sarlavha: "
                    .$file1['name']."\nTavsifi: "
                    .$file1['caption']."\nYili: "
                    .$file1['year']."\nTili: "
                    .$file1['lang']['lang']."\nDavomiyligi: "
                    .$file1['data']."\nMamlakat".$file1['city']."",
                    'disable_web_page_preview'=>true,
                     'reply_markup'=>json_encode([
                         
                        'inline_keyboard'=>[
                            [['text'=>"ko'rish",'callback_data'=>"kino||".$file1['id'].""]]
                            ]
                            
                        ])
                    ]);}
           }
       }
    
       bot('sendmessage',[
                'chat_id'=>$chat_id,
                'text'=>"davomi bor...",
                'disable_web_page_preview'=>true,
                'reply_markup'=>json_encode([
                    'resize_keyboard'=>true,
                    'keyboard'=>[
                        
                        [['text'=>"bosh sahifa"]],
                         [['text'=>"film ortga"]]
                    ]
                    ])
                ]);
            
        }
    }
    
    
      
}else if($chat_type=='private' && is_admin($from_id, $my_group)){
 // botga start bosgan admin 
////////////////////////////////////////////////////////////////////
             if($video){
                   $file=$video->file_id;
                    $method="sendVideo";
                    $type="video";
                if($method){
                            $v = explode('$',$message->caption);
                    $db_file=fileCloud("c", ['file_id'=>$file,'name'=>$v[0] ,'caption'=>$v[1],'lang_id'=>$v[2],'year'=>$v[3], 'fs_id'=>$v[4],'kategoriya_id'=>$v[5], 'data'=>$v[6],'city'=>$v[7] ]);
                    $caption="✅fayl saqlandi.\n Cloud_id: <b>".$db_file['id']."</b>\n Maxsus Hashtag: #".$v[0]."";
                    $caption .="\n\n<pre> /mycloud+<b>".$db_file['id']."</b>+file haqida !!!</pre>";
                bot($method,[
                    'chat_id'=>$chat_id,
                        $type=>$file,
                        'caption'=>$caption,
                    'parse_mode'=>'HTML',
                    'protect_content'=>true,
                     'reply_markup'=>json_encode([
                   'inline_keyboard'=>[
                            [
                               ['text'=>"Rasm qo'yish ~ ".$v[0]."", 'callback_data'=>'photo||'.$db_file['id'].'']
                            ]
                       ]
                    ])
                    ]);
                }else{
                bot('sendMessage',[
                    'chat_id'=>$chat_id,
                    'text'=>"Xatolik file topilmadi!!",
                    'parse_mode'=>'HTML',   
                ]);
                }
                              
                }else
                if(mb_strpos($call_data,'photo') !== false){
                    $v = explode('||',$call_data);
                    $re=bot('sendmessage',[
                    'chat_id'=>$chat_id,
                    'text'=>"".$v[1]."-videoga rasm tashlang",
                    'reply_to_message_id'=>$message_id,
                    'parse_mode'=>'HTML', 
                     'reply_markup'=>json_encode([
                    'force_reply'=>true,
                    'input_field_placeholder'=>"Rasm tanlang yoki chatga tashlang..."])
                ]); 
               
                }   if($photo){
                        $v = explode('-',$message->reply_to_message->text);
                           $file=array_pop($photo)->file_id;
                   $db_file=photo("c", ['file_id'=>$file,'cloud_id'=>$v[0]  ]);
                            bot('sendmessage',[
                            'chat_id'=>$chat_id,
                            'text'=>"rasm vedioga quyildi",]);
                }
              
             
//////////////////////////////////////////////////////////////////////////////////
        if(mb_strpos($call_data,'next') !== false){
              $ps = explode('||',$call_data);

              $db_files1=$ps[1] ? fileCloud1("r", ['type'=> $ps[1], 'last_id' => $ps[2] ] ): 0;
               // file_put_contents("db.json", json_encode($ps['2']));
                
              if(count($db_files1) > 1){
                                 file_put_contents("db.json", json_encode($db_files1));

                $files1=[];
                foreach ($db_files1 as $file1){
                    $files1[]=[
                        'type'=>$file1['type'],
                        'media'=>$file1['file_id'],
                        'caption'=>"Ok <code>/mycloud+<b>".$file1['id']."</b>+ File haqida !!!</code>",
                         'parse_mode'=>"HTML"
                    ];
                }
                bot('sendMediaGroup',[
                        'chat_id'=>$chat_id,
                        'media'=>json_encode($files1),
                ]); 
                  bot('sendMessage',[
                    'text'=>"show more ".$ps[1]." 👇",
                    'chat_id'=>$chat_id,
                    'disable_web_page_preview'=>true,
                   'reply_markup'=>json_encode([
                       'inline_keyboard'=>[
                           [
                               ['text'=>"show - ".$file1['id']."", 
                           'callback_data'=>'next||'.$file1['type'].'||'.$file1['id'].'' ]
                           ]
                       ]
                           ])
            ]);
            
        } else if(count($db_files1)==1){
                $file1=array_shift($db_files1);
                $re= bot($file1['method'],[
                'chat_id'=>$chat_id,
                $file1['type']=>$file1['file_id'],
                'caption'=>"ok <code>/myclode+<b>".$file1['id']."<b>+ file haqida !!! </code>",
                'parse_mode'=>"HTML"
            ]);
                  bot('sendPhoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>$file1['file_id'],
                    'disable_web_page_preview'=>true,
                      
            ]);
        }else{
            bot('sendMessage',[
                'chat_id'=>$chat_id,
                'text'=>"Xatolik , file Topilmadi !!!"
            ]);
         
        }
      
      
  }else  if((mb_stripos($text, "/game")!==false)){
        
       bot('sendDice',[
           'chat_id'=>$chat_id,
           'emoji'=>$dices[rand(0, count($dices)-1)]
           ]);
    
      
       
    }else  if($text=="/newfile"){
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"<pre>Shaxsiy FileCloudga file qo'shish uchun istalgan fileni shu yerga tashlang, yoki file yuborishda ostida #addfile hashtagini qo'sib qo'ying </pre>",
            'parse_mode'=>'HTML',
            'reply_markup'=>json_encode([
                'force_reply'=>true,
                'input_field_placeholder'=>"file tanlang yoki chatga tashlang..."])
            ]);
    }
                            
   
          

   }else   if(mb_stripos($text, "/mycloud_")!==false){
        $type=explode("_", $text)[1];
        $db_files=$type ? fileCloud("r", ['type'=> $type]): 0;
     
        if(count($db_files) > 1){
            foreach ($db_files as $file){
                if($file['method']=="sendVideo" || $file['method']=="sendDocument"){
              
                   bot($file['method'],[  
                      'chat_id'=>$chat_id,
                    $file['type']=>$file['file_id'],
                    'caption'=>"Ok <code>/mycloud+<b>".$file['id']."</b>+ File haqida !!!</code>",
                     'parse_mode'=>"HTML"
                ]);
                }else{
                   $files[]=[
                    'type'=>$file['type'],
                    'media'=>$file['file_id'],
                    'caption'=>"Ok <code>/mycloud+<b>".$file['id']."</b>+ File haqida !!!</code>",
                     'parse_mode'=>"HTML"
                ];
                }
            }
             bot('sendMediaGroup',[
                'chat_id'=>$chat_id,
                'media'=>json_encode($files),
            ]); 
          
            bot('sendMessage',[
                'text'=>"Show more ".$type." 👇",
                'chat_id'=>$chat_id,
                'disable_web_page_preview'=>true,
               'reply_markup'=>json_encode([
                   'inline_keyboard'=>[
                            [
                               ['text'=>"Show ~ ".$file['id']."", 'callback_data'=>'next||'.$type.'||'.$file['id'].'']
                            ]
                       ]
                ])
            ]);
        }
       else if(count($db_files)==1){
            $file=array_shift($db_files);
           $re= bot($file['method'],[
                'chat_id'=>$chat_id,
                $file['type']=>$file['file_id'],
                'caption'=>"ok <code>/myclode+<b>".$file['id']."<b>+ file haqida !!! </code>",
                'parse_mode'=>"HTML"
            ]);
            bot('sendMesage',[
                'chat_id'=>$chat_id,
                'text'=>"text",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=> [
                    'text' => "yana"
                    ]
                    ])
                ]);
        }else{
            bot('sendMessage',[
                'chat_id'=>$chat_id,
                'text'=>"Xatolik , file Topilmadi 1!!!"
            ]);
         
        }
          
    }else if(mb_stripos($text,"/mycloud+") !== false && $text!="/mycloud"){
        $cloud_id=explode("+", $text);
        if(count($cloud_id)>=2 && is_numeric($cloud_id[1])){
            $db_file=fileCloud("f", ['cloud_id'=>$cloud_id[1]]);
            $caption=trim($cloud_id['2']) ? : "";
            bot($db_file['method'],[
                'chat_id'=>$chat_id,
                $db_file['type']=>$db_file['file_id'],
                'parse_mode'=>'HTML',
                'caption'=>$caption
                ]);
        }else {
            bot('sendMessage',[
                'chat_id'=>$chat_id,
                'text'=>"Xatolik , file topilmadi!!!",
                'parse_mode'=>'HTML']);
        }
     
    }
    

else if(in_array($chat_type, ['group', 'supergroup']) && !is_admin($from_id, $chat_id)){
// grupa yo kanaldagi foydalanuvchi




}else if(in_array($chat_type, ['group', 'supergroup']) && is_admin($from_id, $chat_id)){
//grupa yo kanaldagi admin



}



// https://api.telegram.org/file/bot5337366257:AAGm2erpdobRgt-fbwKi5VgtHHFJT2ucVrY/music/file_19.mp3 