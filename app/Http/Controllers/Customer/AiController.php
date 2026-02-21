<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ai\AskAiRequest;
use App\Services\AiService;

class AiController extends Controller
{
    /**
     * Inject AiService.
     *
     * @param AiService $aiService
     */
    public function __construct(
        protected AiService $aiService
    ) {}

    /**
     * Handle a customer AI chat message and return the AI's reply.
     */
    public function ask(AskAiRequest $request)
    {
        $reply = $this->aiService->ask(
            $request->validated()['message'],
            $request->validated()['history']
        );

        return response()->json([
            'reply' => $reply
        ]);
    }
}
