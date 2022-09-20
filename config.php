<?php session_start();
const API_KEY = '5796891202:AAHB9Z5KnfPATQlx3Qs27UBHi9dpz13fBGw';
date_default_timezone_set('Asia/Tashkent');
// admin akkounti id raqamini ushbu bot orqali bilishingiz mumkin @infomiruz_idbot

$system_pass = "123";
$logging = true; //falsez
$work_folder = str_replace("app.php", "", $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."".$_SERVER['REQUEST_URI']);
$my_group=-1001537740802;

$db_mysql=[
    "status"=>true,
    "host"=> "localhost",
    "name"=> "dotaonye_films",
    "user"=> "dotaonye_films",
    "pass"=> 'Riki$9898',
];

//echo date("H:i", time());
//echo ' '. time() - 86400 - 7200;
//1662109887
//echo date("H:i", 1662109887);
$fallows = [
    [
        'text_btn' => "🤟 Dasturlash darslari", 
        'chat_id' => "-1001090616869",
        'link' => "https://t.me/Infomiruz",
        'required'=> false
    ],
    [
        'text_btn' => "👌 Bizning kanal", 
        'link' => "https://t.me/+s_eqCPDJXBY0Mjgy", 
        'chat_id' => $my_channel,
        'required'=> true
    ],
    [
        'text_btn' => "👉 Bizning guruh 👈",
        'link' => "https://t.me/+-E_cdjjuETRiY2Qy",
        'chat_id' => $my_group,
        'required'=> true
    ]
];
// bu majbury obunalarni tekshirish entervali soatlarda ko'rsating !
$fallow_time = 24; //7*24
$share_btn = [
    'share_btn' => "Do'stlarni taklif qilish 👭",
    'share_text' => "🤩🥳 Salom, biz do'stlarimiz bilan yangi guruhda, sovg'alar o'yini tashkil etdik, omadingizni sinab ko'rmaysizmi (tekinga) ?!",
    'share_link' => "https://t.me/supergrop_api"
];

$comands = [
   [
        'commands' => json_encode([
            ["command" => "/info", "description" => "Bot faoliyati haqida."],
            ["command" => "/top", "description" => "Takliflar bo'yicha natijalar."],
            ["command" => "/newfile", "description" => "Yangi file yuklash."],
            ["command" => "/mycloud_photo", "description" => "Mening barcha photolarim."],
            ["command" => "/mycloud_audio", "description" => "Mening barcha audio filelarim."],
            ["command" => "/mycloud_video", "description" => "Mening barcha videolarim."],
            ["command" => "/mycloud_document", "description" => "Mening barcha documentlarim."]
        ]),
        'scope' => json_encode([
            'type' => "chat",
            'chat_id' => $admin
        ])
    ],
   [
        'commands' => json_encode([
            ["command" => "/ban", "description" => "Qatnashchiga ban berish, reply /ban."],
            ["command" => "/mute", "description" => "Foydalanuvchini cheklash, reply 10 menut."],
            ["command" => "/money", "description" => "Valyuta Kurslarini olish."],
            ["command" => "/top", "description" => "Takliflar bo'yicha yetakchilar + 3...100."],
            ["command" => "/game", "description" => "Tasodifiy o'yin boshlash."],
            ["command" => "/stop", "description" => "Chatni yopib qo'yish."],
            ["command" => "/start", "description" => "Chatni ishga tushurish."]
        ]),
        'scope' => json_encode([
            'type' => "all_chat_administrators"
        ])
    ],
   [
        'commands' => json_encode([
            ["command" => "/top", "description" => "Takliflar bo'yicha yetakchilar."],
            ["command" => "/game", "description" => "Tasodifiy o'yin boshlash."],
            ["command" => "/money", "description" => "Valyuta Kurslarini olish."],
        ]),
        'scope' => json_encode([
            'type' => "all_group_chats"
        ])
    ],
   [
        'commands' => json_encode([
            ["command" => "/top", "description" => "Статистика по приглашению."],
            ["command" => "/game", "description" => "Начать случайная игра."],
            ["command" => "/money", "description" => "Узнать о курсах валют."],
        ]),
        'scope' => json_encode([
            'type' => "all_group_chats"
        ]),
        "language_code" => "ru"
    ],
    [
        'commands' => json_encode([
            ["command" => "/start", "description" => "Bot haqida malumot."],
            ["command" => "/game", "description" => "Bot bilan o'yin o'ynash."],
            ["command" => "/myscore", "description" => "Mening ballarim."],
        ]),
        'scope' => json_encode([
            'type' => "all_private_chats"
        ])
    ],
    [
        'commands' => json_encode([
            ["command" => "/start", "description" => "Информация о работе бота."],
            ["command" => "/game", "description" => "Играть в случайную игру с ботом."],
            ["command" => "/myscore", "description" => "Мои баллы."],
        ]),
        'scope' => json_encode([
            'type' => "all_private_chats"
        ]),
        "language_code" => "ru"
    ],
];