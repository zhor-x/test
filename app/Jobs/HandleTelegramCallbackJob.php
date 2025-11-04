<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Laravel\Facades\Telegram;

class HandleTelegramCallbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $callbackData;
    protected $chatId;
    protected $messageId;
    protected $originalText;
    protected $callbackId;

    // Статусы, которые показываем пользователю
    protected $statusMap = [
        'called'   => 'Մտածումա',
        'refused'  => 'Հրաժարվեց',
        'accepted' => 'Հաստատեց',
    ];

    // Начальные кнопки
    protected $initialKeyboard = [
        [
            ['text' => 'Հաստատել ✅', 'callback_data' => 'accepted'],
            ['text' => 'Մտածել 🤔', 'callback_data' => 'called'],
            ['text' => 'Հրաժարվել ❌', 'callback_data' => 'refused'],
        ]
    ];

    public function __construct($callbackData, $chatId, $messageId, $originalText, $callbackId)
    {
        $this->callbackData = $callbackData;
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->originalText = $originalText;
        $this->callbackId = $callbackId;
    }

    public function handle()
    {
        // Если callback совпадает с одним из статусов
        if (array_key_exists($this->callbackData, $this->statusMap)) {
            $statusText = $this->statusMap[$this->callbackData];
            $newText = $this->appendStatus($this->originalText, $statusText);

            $keyboard = [
                [
                    ['text' => 'Փոփոխել', 'callback_data' => 'change']
                ]
            ];

            $this->editMessage($newText, $keyboard);

        } elseif ($this->callbackData === 'change') {
            // Убираем старый статус
            $cleanText = $this->removeStatus($this->originalText);

            $this->editMessage($cleanText, $this->initialKeyboard);

        } else {
            // Любой другой callback — можно логировать или обрабатывать
            $this->editMessage($this->originalText, $this->initialKeyboard);
        }

        // Убираем спиннер
        Telegram::answerCallbackQuery([
            'callback_query_id' => $this->callbackId,
        ]);
    }

    // Добавляем статус в текст
    protected function appendStatus(string $text, string $status): string
    {
        // Если статус уже есть, заменяем его
        if (preg_match('/\n\nԸնտրեց՝ .+$/u', $text)) {
            return preg_replace('/\n\nԸնտրեց՝ .+$/u', "\n\nԸնտրեց՝ $status", $text);
        }

        return $text . "\n\nԸնտրեց՝ $status";
    }

    // Убираем статус из текста
    protected function removeStatus(string $text): string
    {
        return preg_replace('/\n\nԸնտրեց՝ .+$/u', '', $text);
    }

    // Общая функция для редактирования сообщения
    protected function editMessage(string $text, array $keyboard)
    {
        // Не вызываем editMessageText если текст и кнопки не поменялись
        static $lastState = [];

        $key = $this->chatId . '_' . $this->messageId;

        if (isset($lastState[$key]) &&
            $lastState[$key]['text'] === $text &&
            $lastState[$key]['keyboard'] === $keyboard
        ) {
            return; // ничего не меняем
        }

        Telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => $text,
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);

        $lastState[$key] = [
            'text' => $text,
            'keyboard' => $keyboard
        ];
    }

}
