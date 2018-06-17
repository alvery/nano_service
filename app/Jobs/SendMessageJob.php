<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Processor\Message\MessageProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delay in seconds before next attempt
     */
    private const ATTEMPT_DELAY_SECS = 5;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;


    /**
     * @var MessageProcessor
     */
    protected $processor;

    /**
     * Create a new job instance.
     *
     * @param MessageProcessor $processor
     */
    public function __construct(MessageProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->processor->process();
        } catch (\Throwable $e) {
            Log::warning($e->getMessage());
            $this->release(self::ATTEMPT_DELAY_SECS);
        }
    }
}
