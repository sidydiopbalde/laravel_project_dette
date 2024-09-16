<?php
// use Illuminate\Notifications\Notification;
// use Infobip\Api\SendSmsApi;
// use Infobip\Configuration;
// use Infobip\Model\SmsAdvancedTextualRequest;
// use Infobip\Model\SmsDestination;
// use Infobip\Model\SmsTextualMessage;

// class InfobipSmsNotification extends Notification
// {
//     public function via($notifiable)
//     {
//         return ['infobip'];
//     }

//     public function toInfobip($notifiable)
//     {
//         $config = Configuration::getDefaultConfiguration()->setApiKey('Authorization', env('INFOBIP_API_KEY'));
//         $smsClient = new SendSmsApi(new \GuzzleHttp\Client(), $config);

//         $message = new SmsTextualMessage([
//             'from' => env('INFOBIP_FROM'),
//             'destinations' => [
//                 new SmsDestination([
//                     'to' => $notifiable->phone_number,
//                 ]),
//             ],
//             'text' => 'Votre notification SMS via Infobip.',
//         ]);

//         $request = new SmsAdvancedTextualRequest(['messages' => [$message]]);

//         $smsClient->sendSmsMessage($request);
//     }
// }
