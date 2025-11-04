<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TelegramReserveRequest;
use App\Jobs\HandleTelegramCallbackJob;
use App\Jobs\SendTelegramMessageJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
class TelegramController extends Controller
{
    public function handle(TelegramReserveRequest $request): void
    {

        $payload = $request->validated();
        $name = $payload['name'];
        $phone = $payload['phone'];
        $tariff = $payload['tariff'];
        $message = "Նոր պատվեր։\n";           // New order
        $message .= "Անուն: $name\n";         // Name
        $message .= "Հեռախոս: $phone\n";     // Phone
        $message .= "Փաթեթ: $tariff\n";      // Tariff/package

        Log::channel('telegram')->info($message);
        SendTelegramMessageJob::dispatchSync($message);
    }

    public function handleCallback(Request $request)
    {
        $update = Telegram::getWebhookUpdate();

        if (!$update->isType('callback_query')) {
            return;
        }

        $callback = $update->callbackQuery;

        HandleTelegramCallbackJob::dispatchSync(
            $callback->data,
            $callback->message->chat->id,
            $callback->message->message_id,
            $callback->message->text,
            $callback->id
        );
    }

}
