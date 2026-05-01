@extends('layouts.app')

@section('content')
    <div class="h-[calc(100vh-120px)] flex flex-col md:flex-row gap-6" x-data="chatSystem()">
        <!-- Conversation Sidebar -->
        <div class="w-full md:w-80 flex-shrink-0 flex flex-col rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Messages</h3>
            </div>
            
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <template x-for="conv in conversations" :key="conv.id">
                    <div 
                        @click="selectConversation(conv)"
                        :class="selectedConv && selectedConv.id === conv.id ? 'bg-brand-50 dark:bg-brand-500/10' : 'hover:bg-gray-50 dark:hover:bg-white/5'"
                        class="p-4 cursor-pointer border-b border-gray-100 dark:border-gray-800/50 transition-all flex items-center gap-3"
                    >
                        <div class="relative flex-shrink-0">
                            <img :src="getParticipant(conv).profile_picture_url" class="h-10 w-10 rounded-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="getParticipant(conv).name"></h4>
                                <span class="text-[10px] text-gray-400" x-text="formatDate(conv.last_message_at)"></span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="conv.messages.length ? conv.messages[0].message_text : 'No messages yet'"></p>
                        </div>
                    </div>
                </template>
                
                @if($conversations->isEmpty())
                    <div class="p-8 text-center text-gray-500 text-sm">No conversations found.</div>
                @endif
            </div>
        </div>

        <!-- Chat Window -->
        <div class="flex-1 flex flex-col rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
            <template x-if="selectedConv">
                <div class="h-full flex flex-col">
                    <!-- Chat Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex items-center gap-3 bg-gray-50/50 dark:bg-transparent">
                        <img :src="getParticipant(selectedConv).profile_picture_url" class="h-10 w-10 rounded-full object-cover">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white" x-text="getParticipant(selectedConv).name"></h3>
                            <span class="text-[10px] text-green-500 font-medium">Online</span>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar" id="message-container">
                        <template x-for="msg in messages" :key="msg.id">
                            <div :class="msg.sender_id == authId ? 'justify-end' : 'justify-start'" class="flex">
                                <div 
                                    :class="msg.sender_id == authId ? 'bg-brand-500 text-white rounded-br-none' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-bl-none'"
                                    class="max-w-[70%] p-3 rounded-2xl text-sm shadow-sm"
                                >
                                    <p x-text="msg.message_text"></p>
                                    <span :class="msg.sender_id == authId ? 'text-white/70' : 'text-gray-400'" class="text-[9px] mt-1 block" x-text="formatTime(msg.created_at)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Input Area -->
                    <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                        <form @submit.prevent="sendMessage()" class="flex gap-2">
                            <input 
                                type="text" 
                                x-model="newMessage"
                                placeholder="Type your message..."
                                class="flex-1 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition-all dark:text-white"
                            >
                            <button 
                                type="submit" 
                                :disabled="!newMessage.trim()"
                                class="bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white p-2.5 rounded-xl transition-all shadow-md shadow-brand-500/20"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </template>
            
            <template x-if="!selectedConv">
                <div class="h-full flex flex-col items-center justify-center text-gray-400 p-8 text-center">
                    <div class="w-20 h-20 rounded-full bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select a conversation</h3>
                    <p class="text-sm mt-1 max-w-xs">Pick a person from the left to start messaging. Your history will appear here.</p>
                </div>
            </template>
        </div>
    </div>

    <script>
        function chatSystem() {
            return {
                authId: {{ Auth::id() }},
                conversations: @json($conversations),
                selectedConv: null,
                messages: [],
                newMessage: '',
                
                async selectConversation(conv) {
                    this.selectedConv = conv;
                    const response = await fetch(`/admin/chat/${conv.id}`);
                    const data = await response.json();
                    this.messages = data.messages;
                    this.$nextTick(() => this.scrollToBottom());
                },

                async sendMessage() {
                    if (!this.newMessage.trim()) return;
                    
                    const msgText = this.newMessage;
                    this.newMessage = '';

                    const response = await fetch(`/admin/chat/${this.selectedConv.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message_text: msgText })
                    });

                    if (response.ok) {
                        const newMsg = await response.json();
                        this.messages.push(newMsg);
                        
                        // Update conversation preview
                        const convIdx = this.conversations.findIndex(c => c.id === this.selectedConv.id);
                        if (convIdx !== -1) {
                            this.conversations[convIdx].messages = [newMsg];
                            this.conversations[convIdx].last_message_at = newMsg.created_at;
                            // Re-sort conversations
                            this.conversations.sort((a, b) => new Date(b.last_message_at) - new Date(a.last_message_at));
                        }
                        
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                getParticipant(conv) {
                    return conv.participants.find(p => p.id !== this.authId) || { name: 'Unknown', profile_picture_url: '' };
                },

                scrollToBottom() {
                    const container = document.getElementById('message-container');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
                },

                formatTime(dateStr) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
            }
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
        }
    </style>
@endsection
