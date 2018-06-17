<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\MessageTypeEnum;
use App\Jobs\SendMessageJob;
use App\Processor\Message\MessageProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'type' => Rule::in(MessageTypeEnum::getValues()),
            'message' => 'required',
            'delay' => 'nullable|integer',
        ]);

        $messageType = MessageTypeEnum::get($request->input('type'));
        $message = $request->input('message');
        $delay = $request->input('delay') ?? null;

        $this->queueJob($messageType, $message, $delay);
    }

    public function sendMultiple(Request $request)
    {
        Validator::make($request->all(), [
            'data.*.type' => Rule::in(MessageTypeEnum::getValues()),
            'data.*.message' => 'required',
            'data.*.delay' => 'nullable|integer',
        ])->validate();

        $data = $request->input('data');

        foreach ($data as $message) {
            $messageType = MessageTypeEnum::get($message['type']);
            $message = $message['message'];
            $delay = $message['delay'] ?? null;

            $this->queueJob($messageType, $message, $delay);
        }
    }

    private function queueJob(MessageTypeEnum $type, string $message, int $delay = null): void
    {
        $processor = new MessageProcessor($type, $message);
        $dispatcher = SendMessageJob::dispatch($processor);

        $delay = $delay ?? $processor->getMessageDelay();
        Log::info("Message delay: " . $delay . " seconds");
        $dispatcher->delay($delay);
    }
}
