<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useChatAdmin } from '@/composables/useChatAdmin';
import { useFormatters } from '@/composables/useFormatters';
import { useInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import chatsRoutes from '@/routes/admin/chats';
import { type BreadcrumbItem, type Conversation, type Message } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { MessageSquare, Package, Send } from 'lucide-vue-next';
import { computed, nextTick, onBeforeUnmount, ref } from 'vue';

interface Props {
    conversations: Conversation[];
}

const props = defineProps<Props>();
const { formatDate } = useFormatters();
const { getInitials } = useInitials();
const { fetchMessages, sendMessage, markAsRead } = useChatAdmin();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Chats',
        href: chatsRoutes.index().url,
    },
];

// State
const selectedConversationId = ref<number | null>(null);
const messages = ref<Message[]>([]);
const newMessage = ref('');
const messagesEndRef = ref<HTMLElement | null>(null);

// Stored Echo subscription so it can be properly left later and avoid duplicate listeners
let echoSub: { leave: () => void; listen: () => void } | null = null;

// Computed: currently selected conversation
const selectedConversation = computed(() => {
    return props.conversations.find(
        (c) => c.id === selectedConversationId.value,
    );
});

// Select a conversation, load messages, mark them as read, and subscribe to real-time updates.
const selectConversation = async (conversationId: number) => {
    if (echoSub) {
        echoSub.leave();
        echoSub = null;
    }

    selectedConversationId.value = conversationId;

    const data = await fetchMessages(conversationId);
    messages.value = data.messages || [];

    await markAsRead(conversationId);

    echoSub = useEcho(
        `conversation.${conversationId}`,
        '.message.sent',
        (event: Message) => {
            if (!event.created_at) {
                event.created_at = new Date().toISOString();
            }
            const exists = messages.value.some((m) => m.id === event.id);
            if (!exists) {
                messages.value.push(event);
                scrollToBottom();
            }
        },
        [],
        'private',
    );

    echoSub.listen();

    scrollToBottom();
};

// Send a new message for the selected conversation.
const handleSendMessage = async () => {
    if (!newMessage.value.trim() || !selectedConversationId.value) return;

    const message = await sendMessage(
        selectedConversationId.value,
        newMessage.value,
    );

    const exists = messages.value.some((m) => m.id === message.id);
    if (!exists) {
        messages.value.push(message);
    }

    newMessage.value = '';
    scrollToBottom();
};

// Scroll the message list to the most recent message.
const scrollToBottom = () => {
    nextTick(() => {
        messagesEndRef.value?.scrollIntoView({ behavior: 'smooth' });
    });
};

// Cleanup the Echo subscription when leaving the page.
onBeforeUnmount(() => {
    if (echoSub) {
        echoSub.leave();
    }
});
</script>

<template>
    <Head title="Chat" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 gap-4 overflow-hidden p-4">
            <!-- Conversations List -->
            <Card class="w-80 flex-shrink-0">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <MessageSquare class="h-5 w-5" />
                        Conversations
                    </CardTitle>
                </CardHeader>

                <CardContent class="p-0">
                    <ScrollArea class="h-[calc(100vh-12rem)]">
                        <div class="space-y-2 p-4">
                            <button
                                v-for="conversation in conversations"
                                :key="conversation.id"
                                @click="selectConversation(conversation.id)"
                                :class="[
                                    'w-full rounded-lg border p-3 text-left transition-all',
                                    selectedConversationId === conversation.id
                                        ? 'border-primary bg-primary/10'
                                        : 'border-border hover:border-primary/50',
                                ]"
                            >
                                <div class="flex items-start gap-3">
                                    <Avatar class="h-10 w-10">
                                        <AvatarImage
                                            v-if="
                                                conversation.user_customer
                                                    ?.avatar
                                            "
                                            :src="`/storage/${conversation.user_customer.avatar}`"
                                        />
                                        <AvatarFallback>
                                            {{
                                                getInitials(
                                                    conversation.user_customer
                                                        ?.name || 'U',
                                                )
                                            }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div class="flex-1 overflow-hidden">
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <p class="truncate font-semibold">
                                                {{
                                                    conversation.user_customer
                                                        ?.name
                                                }}
                                            </p>
                                        </div>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{
                                                formatDate(
                                                    conversation.last_message_at,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </ScrollArea>
                </CardContent>
            </Card>

            <!-- Chat Area -->
            <Card class="flex flex-1 flex-col">
                <!-- Chat Header -->
                <CardHeader v-if="selectedConversation" class="border-b">
                    <div class="flex items-center gap-3">
                        <Avatar class="h-10 w-10">
                            <AvatarImage
                                v-if="
                                    selectedConversation.user_customer?.avatar
                                "
                                :src="`/storage/${selectedConversation.user_customer.avatar}`"
                            />
                            <AvatarFallback>
                                {{
                                    getInitials(
                                        selectedConversation.user_customer
                                            ?.name || 'U',
                                    )
                                }}
                            </AvatarFallback>
                        </Avatar>
                        <div>
                            <CardTitle class="text-lg">
                                {{ selectedConversation.user_customer?.name }}
                            </CardTitle>
                            <p class="text-sm text-muted-foreground">
                                {{ selectedConversation.user_customer?.email }}
                            </p>
                        </div>
                    </div>
                </CardHeader>

                <!-- Empty State -->
                <CardContent
                    v-if="!selectedConversation"
                    class="flex flex-1 items-center justify-center"
                >
                    <div class="text-center">
                        <MessageSquare
                            class="mx-auto h-12 w-12 text-muted-foreground"
                        />
                        <h3 class="mt-4 text-lg font-semibold">
                            Select a conversation
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            Choose a conversation from the list to start
                            chatting
                        </p>
                    </div>
                </CardContent>

                <!-- Messages -->
                <CardContent v-else class="flex-1 overflow-hidden p-0">
                    <ScrollArea class="h-[calc(100vh-20rem)] p-4">
                        <div class="space-y-4">
                            <div
                                v-for="message in messages"
                                :key="message.id"
                                :class="[
                                    'flex',
                                    message.sender_type === 'admin'
                                        ? 'justify-end'
                                        : 'justify-start',
                                ]"
                            >
                                <div
                                    :class="[
                                        'max-w-[70%] rounded-lg p-3',
                                        message.sender_type === 'admin'
                                            ? 'bg-primary text-primary-foreground'
                                            : 'bg-muted',
                                    ]"
                                >
                                    <!-- Attached Item -->
                                    <Card
                                        v-if="message.item"
                                        class="mb-2 border-2"
                                    >
                                        <CardContent
                                            class="flex items-center gap-3 p-3"
                                        >
                                            <img
                                                v-if="message.item.item_image_1"
                                                :src="`/storage/${message.item.item_image_1}`"
                                                :alt="message.item.item_name"
                                                class="h-12 w-12 rounded object-cover"
                                            />
                                            <Package
                                                v-else
                                                class="h-12 w-12 text-muted-foreground"
                                            />
                                            <div class="flex-1">
                                                <p
                                                    class="text-sm font-semibold"
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
                                    <p class="break-words whitespace-pre-wrap">
                                        {{ message.message }}
                                    </p>
                                    <p
                                        :class="[
                                            'mt-1 text-xs',
                                            message.sender_type === 'admin'
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
                <CardContent v-if="selectedConversation" class="border-t p-4">
                    <form
                        @submit.prevent="handleSendMessage"
                        class="flex gap-2"
                    >
                        <Input
                            v-model="newMessage"
                            placeholder="Type a message..."
                            class="flex-1"
                        />
                        <Button type="submit" :disabled="!newMessage.trim()">
                            <Send class="h-4 w-4" />
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
