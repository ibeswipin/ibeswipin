<?php
ob_start();
error_reporting(0);
date_default_timezone_set("Asia/Tashkent");
define('API_KEY', '5829724193:AAFo0NPAvflYlZ39kKCiBSLQDFeAE8JF3B8');

/* Ushbu kod @UzMaxDev tomonidan tuzilgan!
Kod 15.01.2023 sanasida @pcouz kanali orqali tarqatildi!
Manbasiz koʻrsam hafa qilaman! */

/* Kod tushunarli boʻlishi uchun fayl bazada qilindi! */

function bot($method, $steps = [])
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $steps);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}

$update = json_decode(file_get_contents('php://input'));
$text = $update->message->text;
$chat_id = $update->message->chat->id;
$name = $update->message->chat->first_name;
$step = file_get_contents("step/$chat_id/step.txt");
$callback = $update->callback_query->data;
$callid = $update->callback_query->id;
$callcid = $update->callback_query->message->chat->id;

mkdir("step");
mkdir("step/$chat_id");

$home = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => "💰 Hisobni toʻldirish"]]
    ]
]);

$back = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => "◀️ Ortga"]]
    ]
]);

if ($text == "/start" or $text == "◀️ Ortga") {
    unlink("step/$chat_id/step.txt");
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "<b>👋🏻 Salom <a href = 'tg://user?id=$chat_id'>$name</a> botimizga xush kelibsiz!
💫 Hisob toʻldirish uchun «💰 Hisobni toʻldirish» tugmasini bosing!</b>",
        'parse_mode' => 'html',
        'reply_markup' => $home
    ]);
    exit;
}

if ($text == "💰 Hisobni toʻldirish") {
    file_put_contents("step/$chat_id/step.txt", "payme");
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "<b>🔢 Toʻlov miqdorini kiriting!

<i>Minimal toʻlov miqdori: 1000 soʻm!</i></b>",
        'parse_mode' => 'html',
        'reply_markup' => $back
    ]);
    exit;
}

if ($step == "payme" and $text != "/start" and $text != "◀️ Ortga") {
    if (is_numeric($text)) {
        if ($text >= 1000) {
            $amount = $text;
            $card = "PAYME_KARTA_ID_RAQAMI"; /* Ushbu joyga payme karta id raqami yoziladi */
            $description = "Bot uchun toʻlov"; /* Ushbu joyga biror bir maʼlumot yoziladi! */
            $checkout = json_decode(file_get_contents("https://uzmaxdev.ru/payme/index.php?action=create&amount=" . $amount . "&description=" . urlencode($description) . "&card=" . $card . ""), true);
            $url = $checkout['url'];
            $check_id = $checkout['id'];
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "<b>✅ Toʻlov miqdori qabul qilindi.

🔽 Endi esa pastdagi tugma orqali toʻlov qiling va toʻlovingizni tasdiqlang!</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "💸 Toʻlovni amalga oshirish (Ilova/Brauzer)", 'url' => "$url"],
                        ],
                        [['text' => "💸 Toʻlovni amalga oshirish (Telegram)", 'web_app' => ['url' => "$url"]]],
                        [['text' => "✅ Toʻlovni tekshirish", 'callback_data' => "checkout=$check_id=$amount"]]
                    ]
                ])
            ]);
            unlink("step/$chat_id/step.txt");
            exit;
        } else {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "<b>⚠️ Toʻlov miqdori notoʻgʻri kiritilmoqda!

⬇️ Minimal toʻlov miqdori: 1000 soʻm

🔢 Boshqa miqdor kiriting.</b>",
                'parse_mode' => 'html',
                'reply_markup' => $back
            ]);
            exit;
        }
    } else {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "<b>⚠️ Toʻlov miqdori faqat raqamdan tashkil topgan boʻlishi kerak!</b>",
            'parse_mode' => 'html',
            'reply_markup' => $back
        ]);
        exit;
    }
}

if (mb_stripos($callback, "checkout=") !== false) {
    $check_id = explode("=", $callback)[1];
    $amount = explode("=", $callback)[2];
    $payments = file_get_contents("payments.txt");
    if (mb_stripos($payments, $check_id) !== false) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $callid,
            'text' => "⚠ Ushbu toʻlov amalga oshirilgan!",
            'show_alert' => true
        ]);
        exit;
    } else {
        $get = json_decode(file_get_contents("https://uzmaxdev.ru/payme/index.php?action=info&id=" . $check_id . ""), true);
        $result = $get['result'];
        if ($result == "successfully") {
            file_put_contents("payments.txt", "\n" . $check_id, FILE_APPEND);
            bot('sendMessage', [
                'chat_id' => $callcid,
                'text' => "<b>💰 Hisobingizga $amount soʻm qabul qilindi!</b>",
                'parse_mode' => 'html',
                'reply_markup' => $back
            ]);
            exit;
        } else {
            bot('answerCallbackQuery', [
                'callback_query_id' => $callid,
                'text' => "⚠ Ushbu toʻlov amalga oshirilmagan!",
                'show_alert' => true
            ]);
            exit;
        }
    }
}
?>