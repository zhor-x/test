<?php

namespace App\Services\Api\V1;

use Exception;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    public function sendMessage(string $message, ?string $chatId = null): void
    {
        $chatId = '-1002973978425';

        try {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'reply_markup' => Keyboard::make()
                    ->inline()
                    ->row([
                        Keyboard::inlineButton(['text' => 'Մտածումա', 'callback_data' => 'called']),
                        Keyboard::inlineButton(['text' => 'Հրաժարվեց', 'callback_data' => 'refused']),
                        Keyboard::inlineButton(['text' => 'Հաստատեց', 'callback_data' => 'accepted']),
                    ]),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send Telegram message', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
                'message' => $message,
            ]);
        }
    }
}
