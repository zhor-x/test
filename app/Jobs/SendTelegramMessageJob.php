<?php

namespace App\Jobs;

use App\Services\Api\V1\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $message,
        public ?string $chatId = null,
    ) {}

    public function handle(TelegramService $telegramService): void
    {
        $telegramService->sendMessage($this->message, $this->chatId);
    }
}
