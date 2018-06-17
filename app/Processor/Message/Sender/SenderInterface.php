<?php
declare(strict_types=1);

namespace App\Processor\Message\Sender;


interface SenderInterface
{
    public function send(string $message): void;
}