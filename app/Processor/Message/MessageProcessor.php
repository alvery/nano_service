<?php
declare(strict_types=1);

namespace App\Processor\Message;


use App\Enum\MessageTypeEnum;
use App\Processor\AbstractProcessor;
use App\Processor\Message\Sender\SenderInterface;
use Carbon\Carbon;


class MessageProcessor extends AbstractProcessor
{
    private const TIME_SCHEDULE = [
        '10:00:00',
        '12:00:00',
        '14:00:00',
    ];

    /**
     * @var SenderInterface
     */
    protected $sender;

    /**
     * @var string
     */
    protected $message;

    public function __construct(MessageTypeEnum $type, string $message)
    {
        $this->sender = $this->resourceFactory($type);
        $this->message = $message;
    }

    public function process(): void
    {
        $this->sender->send($this->message);
    }

    protected function resourceFactory(MessageTypeEnum $type): SenderInterface
    {
        if (!isset(MessageTypeEnum::$factoryMap[$type->getValue()])) {
            throw new \InvalidArgumentException("Sender type {$type->getValue()} is not supported");
        }

        $classname = MessageTypeEnum::$factoryMap[$type->getValue()];

        return new $classname();
    }

    public function getMessageDelay(): int
    {
        $now = Carbon::now('Europe/Moscow');

        /** @var Carbon[] $slots */
        $slots = array_map(function($time) use ($now){
            return Carbon::createFromTimeString($time, 'Europe/Moscow');
        }, self::TIME_SCHEDULE);

        foreach($slots as $slot) {
            if ($now->lessThan($slot->copy()->subMinute())) {
                return $now->diffInSeconds($slot);
            }
        }

        return $slots[0]->addDay()->diffInSeconds($now);
    }
}