<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useAiChat } from '@/composables/useAiChat';
import type { AiMessage } from '@/types';
import { Bot, Send, X } from 'lucide-vue-next';
import { marked } from 'marked';
import { nextTick, ref } from 'vue';

// Configure marked for safe inline rendering
marked.setOptions({ breaks: true });

// Parse markdown to HTML for AI responses
const parseMarkdown = (content: string): string => {
    return marked.parse(content) as string;
};

const { loading, sendMessage } = useAiChat();

// State
const isOpen = ref(false);
const messages = ref<AiMessage[]>([]);
const newMessage = ref('');
const messagesEndRef = ref<HTMLElement | null>(null);

// Toggle the AI chat window open/close.
const toggleChat = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) scrollToBottom();
};

// Scroll the message list to the most recent message.
const scrollToBottom = () => {
    nextTick(() => {
        messagesEndRef.value?.scrollIntoView({ behavior: 'smooth' });
    });
};

// Send a new message to the AI and push both sides to the messages list.
const handleSendMessage = async () => {
    if (!newMessage.value.trim() || loading.value) return;

    const userMessage: AiMessage = {
        id: Date.now(),
        role: 'user',
        content: newMessage.value,
    };

    messages.value.push(userMessage);
    const currentMessage = newMessage.value;
    newMessage.value = '';
    scrollToBottom();

    try {
        const history = messages.value
            .slice(0, -1)
            .map((m) => ({ role: m.role, content: m.content }));

        const { reply, showStoreImage } = await sendMessage(
            currentMessage,
            history,
        );

        messages.value.push({
            id: Date.now() + 1,
            role: 'model',
            content: reply,
            showStoreImage,
        });
    } catch (err) {
        console.error('Failed to get AI response', err);

        messages.value.push({
            id: Date.now() + 1,
            role: 'model',
            content: 'Sorry, I encountered an error. Please try again.',
        });
    } finally {
        scrollToBottom();
    }
};
</script>

<template>
    <!-- Floating AI Chat Button -->
    <Button
        v-if="!isOpen"
        @click="toggleChat"
        size="icon"
        class="fixed right-6 bottom-6 z-50 h-14 w-14 rounded-full shadow-2xl transition-transform hover:scale-110"
    >
        <Bot class="h-6 w-6" />
    </Button>

    <!-- AI Chat Window -->
    <Card
        v-if="isOpen"
        class="fixed right-6 bottom-6 z-50 flex h-[600px] w-[400px] flex-col shadow-2xl"
    >
        <!-- Header -->
        <CardHeader
            class="flex flex-row items-center justify-between border-b p-4"
        >
            <CardTitle class="flex items-center gap-2 text-lg">
                <Bot class="h-5 w-5" />
                AI Assistant
            </CardTitle>
            <Button
                @click="toggleChat"
                variant="ghost"
                size="icon"
                class="h-8 w-8"
            >
                <X class="h-4 w-4" />
            </Button>
        </CardHeader>

        <!-- Messages -->
        <CardContent class="flex-1 overflow-hidden p-0">
            <ScrollArea class="h-full p-4">
                <div class="space-y-4">
                    <!-- Welcome State -->
                    <div v-if="messages.length === 0" class="text-center">
                        <div
                            class="mx-auto mb-4 w-fit rounded-full bg-primary/10 p-4"
                        >
                            <Bot class="h-8 w-8 text-primary" />
                        </div>
                        <h3 class="font-semibold">Hi! I'm your AI Assistant</h3>
                        <p class="text-sm text-muted-foreground">
                            Ask me anything about our products or store!
                        </p>
                    </div>

                    <!-- Messages -->
                    <div
                        v-for="message in messages"
                        :key="message.id"
                        :class="[
                            'flex',
                            message.role === 'user'
                                ? 'justify-end'
                                : 'justify-start',
                        ]"
                    >
                        <div class="flex max-w-[80%] flex-col gap-2">
                            <!-- User message: plain text -->
                            <div
                                v-if="message.role === 'user'"
                                class="rounded-lg bg-primary p-3 text-sm break-words whitespace-pre-wrap text-primary-foreground"
                            >
                                {{ message.content }}
                            </div>

                            <!-- AI message: rendered markdown -->
                            <div
                                v-else
                                class="ai-message-content rounded-lg bg-muted p-3 text-sm break-words"
                                v-html="parseMarkdown(message.content)"
                            />

                            <!-- Store image shown when AI answers a location question -->
                            <img
                                v-if="message.showStoreImage"
                                src="/img/abg-store.png"
                                alt="ABG Prime Builders Supplies Inc. Store"
                                class="w-full rounded-lg object-cover shadow-sm"
                            />
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div v-if="loading" class="flex justify-start">
                        <div
                            class="max-w-[80%] rounded-lg bg-muted p-3 text-sm"
                        >
                            <span class="animate-pulse">AI is typing...</span>
                        </div>
                    </div>

                    <div ref="messagesEndRef"></div>
                </div>
            </ScrollArea>
        </CardContent>

        <!-- Input -->
        <CardContent class="border-t p-4">
            <form @submit.prevent="handleSendMessage" class="flex gap-2">
                <Input
                    v-model="newMessage"
                    placeholder="Ask about our products..."
                    class="flex-1"
                    :disabled="loading"
                />
                <Button
                    type="submit"
                    :disabled="!newMessage.trim() || loading"
                    size="icon"
                >
                    <Send class="h-4 w-4" />
                </Button>
            </form>
        </CardContent>
    </Card>
</template>
