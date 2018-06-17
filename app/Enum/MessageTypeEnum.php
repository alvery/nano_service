<?php
declare(strict_types=1);

namespace App\Enum;

use App\Processor\Message\Sender\TelegramSender;
use App\Processor\Message\Sender\ViberSender;
use App\Processor\Message\Sender\WhatsAppSender;
use MabeEnum\Enum;

/**
 * @method static self TELEGRAM()
 * @method static self VIBER()
 * @method static self WHATSAPP()
 */
class MessageTypeEnum extends Enum
{
    public static $factoryMap = [
        self::TELEGRAM => TelegramSender::class,
        self::VIBER => ViberSender::class,
        self::WHATSAPP => WhatsAppSender::class,
    ];

    public const TELEGRAM = 1;
    public const VIBER = 2;
    public const WHATSAPP = 3;
}