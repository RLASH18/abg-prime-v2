<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ai\AskAiRequest;
use App\Services\AiChatService;

class AiChatController extends Controller
{
    /**
     * Inject AiChatService.
     *
     * @param AiChatService $aiChatService
     */
    public function __construct(
        protected AiChatService $aiChatService
    ) {}

    /**
     * Handle a customer AI chat message and return the AI's reply.
     */
    public function ask(AskAiRequest $request)
    {
        $reply = $this->aiChatService->ask(
            $request->validated()['message'],
            $request->validated()['history']
        );

        return response()->json([
            'reply' => $reply
        ]);
    }
}
