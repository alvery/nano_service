<?php
declare(strict_types=1);

namespace App\Processor\Message\Sender;


use Illuminate\Support\Facades\Log;

class ViberSender implements SenderInterface
{
    public function send(string $message): void
    {
        Log::info(__CLASS__ . ": " . $message);
    }
}