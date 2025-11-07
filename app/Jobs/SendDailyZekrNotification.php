<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class SendDailyZekrNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $timeType; // morning أو evening

    public function __construct($timeType)
    {
        $this->timeType = $timeType;
    }

    public function handle()
    {
        $factory = (new Factory)
    ->withServiceAccount(storage_path('firebase/aladhan-e5325-firebase-adminsdk-fbsvc-bf4f3aaf59.json'));
        $messaging = $factory->createMessaging();

        $title = $this->timeType === 'morning' ? '🌅 أذكار الصباح' : '🌇 أذكار المساء';
        $body  = 'حان وقت ' . ($this->timeType === 'morning' ? 'أذكار الصباح' : 'أذكار المساء') . '، لا تنسَ قراءتها الآن 💬';

        $message = CloudMessage::new()
            ->withNotification(['title' => $title, 'body' => $body])
            ->withTarget('topic', $this->timeType . '_zekr');

        $messaging->send($message);
    }
}
