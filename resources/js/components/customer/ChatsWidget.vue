<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useChatCustomer } from '@/composables/useChatCustomer';
import { useFormatters } from '@/composables/useFormatters';
import type { Conversation, Message } from '@/types';
import { useEcho } from '@laravel/echo-vue';
import { MessageCircle, Package, Send, X } from 'lucide-vue-next';
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

interface Props {
    attachedItem?: {
        id: number;
        item_name: string;
        item_code: string;
        unit_price: number;
        item_image_1?: string;
    } | null;
}

const props = defineProps<Props>();

const { formatDate } = useFormatters();
const { fetchConversation, sendMessage, markAsRead, getUnreadCount } =
    useChatCustomer();

// State
const isOpen = ref(false);
const conversation = ref<Conversation | null>(null);
const messages = ref<Message[]>([]);
const newMessage = ref('');
const unreadCount = ref(0);
const messagesEndRef = ref<HTMLElement | null>(null);
const hasAttachedItem = ref(false);

// Stored Echo subscription so it can be properly left later and avoid duplicate listeners
let echoSub: { leave: () => void; listen: () => void } | null = null;

// Toggle the chat window open/close and sync unread state.
const toggleChat = async () => {
    isOpen.value = !isOpen.value;

    if (isOpen.value && !conversation.value) {
        await loadConversation();
    }

    if (isOpen.value) {
        if (conversation.value) {
            await markAsRead();
            unreadCount.value = 0;
        }

        scrollToBottom();
    }
};

// Load the current customer's conversation and messages.
const loadConversation = async () => {
    try {
        const data = await fetchConversation();
        conversation.value = data.conversation;
        messages.value = data.messages || [];
    } catch (err) {
        console.error('Failed to load conversation', err);
    }
};

// Subscribe to real-time updates for the current conversation.
const setupRealTimeSubscription = async () => {
    if (!conversation.value) return;

    if (echoSub) {
        echoSub.leave();
        echoSub = null;
    }

    console.log('Subscribing to conversation:', conversation.value.id);

    echoSub = useEcho(
        `conversation.${conversation.value.id}`,
        '.message.sent',
        handleNewMessage,
        [],
        'private',
    );
    echoSub.listen();
};

// Send a new message (optionally with an attached item).
const handleSendMessage = async () => {
    if (!newMessage.value.trim()) return;

    try {
        const itemId =
            hasAttachedItem.value && props.attachedItem
                ? props.attachedItem.id
                : undefined;

        const message = await sendMessage(newMessage.value, itemId);

        if (!message.created_at) {
            message.created_at = new Date().toISOString();
        }

        const exists = messages.value.some((m) => m.id === message.id);
        if (!exists) {
            messages.value.push(message);
        }

        newMessage.value = '';
        hasAttachedItem.value = false;
        scrollToBottom();
    } catch (err) {
        console.error('Failed to send message', err);
    }
};

// Handle an incoming real-time message event.
const handleNewMessage = (event: Message) => {
    const message = event;

    console.log('handleNewMessage called with', message);

    if (!message.created_at) {
        message.created_at = new Date().toISOString();
    }

    const exists = messages.value.some((m) => m.id === message.id);
    if (exists) {
        console.log('Message already exists, skipping');
        return;
    }

    console.log('Adding message to array');
    messages.value.push(message);

    if (!isOpen.value) {
        unreadCount.value++;
    } else {
        markAsRead();
    }

    scrollToBottom();
};

// Scroll the message list to the most recent message.
const scrollToBottom = () => {
    nextTick(() => {
        messagesEndRef.value?.scrollIntoView({ behavior: 'smooth' });
    });
};

// Attach the current item to the next message and auto-open chat if needed.
const attachItem = () => {
    if (props.attachedItem) {
        hasAttachedItem.value = true;

        if (!isOpen.value) {
            toggleChat();
        }
    }
};

// Remove the currently attached item.
const removeAttachment = () => {
    hasAttachedItem.value = false;
};

// Cleanup the Echo subscription when the widget is destroyed.
onBeforeUnmount(() => {
    if (echoSub) {
        echoSub.leave();
    }
});

// Load initial state and start real-time subscription on mount.
onMounted(async () => {
    try {
        unreadCount.value = await getUnreadCount();
        await loadConversation();
        setupRealTimeSubscription();
    } catch (err) {
        console.error('Failed to load unread account:', err);
    }
});

// Watch for attached item changes and auto-open chat when an item gets attached.
watch(
    () => props.attachedItem,
    (newItem) => {
        if (newItem && !hasAttachedItem.value) {
            attachItem();
        }
    },
    { immediate: true },
);
</script>

<template>
    <!-- Floating Chat Button -->
    <Button
        v-if="!isOpen"
        @click="toggleChat"
        size="icon"
        class="fixed right-6 bottom-6 z-50 h-14 w-14 rounded-full shadow-2xl transition-transform hover:scale-110"
    >
        <MessageCircle class="h-6 w-6" />
        <Badge
            v-if="unreadCount > 0"
            class="absolute -top-1 -right-1 h-6 w-6 rounded-full border-2 border-background p-0 text-xs"
        >
            {{ unreadCount > 9 ? '9+' : unreadCount }}
        </Badge>
    </Button>

    <!-- Chat Window -->
    <Card
        v-if="isOpen"
        class="fixed right-6 bottom-6 z-50 flex h-[600px] w-[400px] flex-col shadow-2xl"
    >
        <!-- Header -->
        <CardHeader
            class="flex flex-row items-center justify-between border-b p-4"
        >
            <CardTitle class="flex items-center gap-2 text-lg">
                <MessageCircle class="h-5 w-5" />
                Chat with Us
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
                    <!-- Welcome Message -->
                    <div v-if="messages.length === 0" class="text-center">
                        <div
                            class="mx-auto mb-4 w-fit rounded-full bg-primary/10 p-4"
                        >
                            <MessageCircle class="h-8 w-8 text-primary" />
                        </div>
                        <h3 class="font-semibold">Welcome!</h3>
                        <p class="text-sm text-muted-foreground">
                            Send us a message and we'll get back to you soon.
                        </p>
                    </div>

                    <!-- Messages -->
                    <div
                        v-for="message in messages"
                        :key="message.id"
                        :class="[
                            'flex',
                            message.sender_type === 'customer'
                                ? 'justify-end'
                                : 'justify-start',
                        ]"
                    >
                        <div
                            :class="[
                                'max-w-[80%] rounded-lg p-3',
                                message.sender_type === 'customer'
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-muted',
                            ]"
                        >
                            <!-- Attached Item -->
                            <Card v-if="message.item" class="mb-2 border-2">
                                <CardContent
                                    class="flex items-center gap-2 p-2"
                                >
                                    <img
                                        v-if="message.item.item_image_1"
                                        :src="`/storage/${message.item.item_image_1}`"
                                        :alt="message.item.item_name"
                                        class="h-10 w-10 rounded object-cover"
                                    />
                                    <Package
                                        v-else
                                        class="h-10 w-10 text-muted-foreground"
                                    />
                                    <div class="flex-1 overflow-hidden">
                                        <p
                                            class="truncate text-xs font-semibold"
                                        >
                                            {{ message.item.item_name }}
                                        </p>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ message.item.item_code }}
                                        </p>
                                    </div>
                                </CardContent>
                            </Card>

                            <!-- Message Text -->
                            <p class="text-sm break-words whitespace-pre-wrap">
                                {{ message.message }}
                            </p>
                            <p
                                v-if="message.created_at"
                                :class="[
                                    'mt-1 text-xs',
                                    message.sender_type === 'customer'
                                        ? 'text-primary-foreground/70'
                                        : 'text-muted-foreground',
                                ]"
                            >
                                {{ formatDate(message.created_at) }}
                            </p>
                        </div>
                    </div>
                    <div ref="messagesEndRef"></div>
                </div>
            </ScrollArea>
        </CardContent>

        <!-- Message Input -->
        <CardContent class="border-t p-4">
            <!-- Attached Item Preview -->
            <Card
                v-if="hasAttachedItem && attachedItem"
                class="mb-2 border-2 border-primary/50"
            >
                <CardContent class="flex items-center gap-2 p-2">
                    <img
                        v-if="attachedItem.item_image_1"
                        :src="`/storage/${attachedItem.item_image_1}`"
                        :alt="attachedItem.item_name"
                        class="h-10 w-10 rounded object-cover"
                    />
                    <Package v-else class="h-10 w-10 text-muted-foreground" />
                    <div class="flex-1 overflow-hidden">
                        <p class="truncate text-xs font-semibold">
                            {{ attachedItem.item_name }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ attachedItem.item_code }}
                        </p>
                    </div>
                    <Button
                        @click="removeAttachment"
                        variant="ghost"
                        size="icon"
                        class="h-6 w-6"
                    >
                        <X class="h-3 w-3" />
                    </Button>
                </CardContent>
            </Card>

            <!-- Input Form -->
            <form @submit.prevent="handleSendMessage" class="flex gap-2">
                <Input
                    v-model="newMessage"
                    placeholder="Type a message..."
                    class="flex-1"
                />
                <Button
                    type="submit"
                    :disabled="!newMessage.trim()"
                    size="icon"
                >
                    <Send class="h-4 w-4" />
                </Button>
            </form>
        </CardContent>
    </Card>
</template>
