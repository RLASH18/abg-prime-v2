<?php

namespace App\Services;

use Gemini\Data\Content;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\File;

class AiService
{
    /**
     * Load system prompt from the markdown file
     *
     * @return string
     */
    private function getSystemPrompt(): string
    {
        $path = resource_path('views/prompts/ai-assistant.md');

        return File::exists($path) ? File::get($path) : '';
    }

    /**
     * Send a message to Gemini with conversation history and get a response.
     *
     * @param string $message
     * @param array  $history  Previous exchanges [['role' => 'user'|'model', 'content' => string]]
     * @return string
     */
    public function ask(string $message, array $history = []): string
    {
        $formattedHistory = collect($history)->map(
            fn ($item) => Content::parse(
                $item['content'],
                $item['role'] === 'model' ? Role::MODEL : Role::USER
            )
        )->toArray();

        $response = Gemini::generativeModel('gemini-2.5-flash')
            ->withSystemInstruction(Content::parse($this->getSystemPrompt()))
            ->startChat($formattedHistory)
            ->sendMessage($message);

        return $response->text();
    }
}
