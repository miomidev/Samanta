@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('AI Project Viewer') }}
        </h2>
        <a href="{{ route('project.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
            Create Another Project
        </a>
    </div>
@endsection

@section('content')
<div class="py-6" x-data="projectViewer('{{ $id }}')">
    <div class="max-w-[1600px] mx-auto sm:px-4 lg:px-6 h-[75vh]">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg flex h-full border border-gray-200 dark:border-gray-700">
            
            <!-- Sidebar / Tree View -->
            <div class="w-1/5 min-w-[220px] max-w-[300px] border-r border-gray-200 dark:border-gray-700 overflow-y-auto bg-gray-50 dark:bg-gray-900 flex flex-col">
                <div class="p-3 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-gray-300 flex justify-between items-center">
                    <span>Explorer</span>
                    <button @click="loadTree" class="text-xs text-indigo-500 hover:text-indigo-700">Refresh</button>
                </div>
                
                <div class="p-2 flex-1 overflow-y-auto w-full">
                    <template x-if="isLoadingTree">
                        <div class="text-sm text-gray-500 p-2">Loading tree...</div>
                    </template>
                    <template x-if="!isLoadingTree && tree.length === 0">
                        <div class="text-sm text-gray-500 p-2">No files found.</div>
                    </template>
                    
                    <ul class="text-sm">
                        <template x-for="item in tree" :key="item.path">
                            <li x-data="{ expanded: false }" class="mb-1">
                                <!-- Directory -->
                                <template x-if="item.type === 'directory'">
                                    <div>
                                        <div @click="expanded = !expanded" class="flex items-center cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 px-2 py-1 rounded">
                                            <svg x-show="!expanded" class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            <svg x-show="expanded" class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            <span class="text-gray-800 dark:text-gray-200" x-text="item.name"></span>
                                        </div>
                                        
                                        <!-- Children -->
                                        <ul x-show="expanded" class="pl-4 mt-1 border-l ml-3 border-gray-300 dark:border-gray-600">
                                            <template x-for="child in item.children" :key="child.path">
                                                 <li class="py-0.5">
                                                     <template x-if="child.type === 'directory'">
                                                        <span class="text-gray-500 italic">... nested folder</span>
                                                     </template>
                                                     <template x-if="child.type === 'file'">
                                                        <div @click="loadFile(child.path)" 
                                                             class="flex flex-row items-center cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 px-2 py-1 rounded truncate w-full"
                                                             :class="{'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 font-medium': activeFile === child.path}">
                                                            <svg class="w-3.5 h-3.5 mr-1.5 min-w-[14px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                            <span class="text-gray-700 dark:text-gray-300 truncate" x-text="child.name" :title="child.name"></span>
                                                        </div>
                                                     </template>
                                                 </li>
                                            </template>
                                        </ul>
                                    </div>
                                </template>
                                
                                <!-- Root level File -->
                                <template x-if="item.type === 'file'">
                                    <div @click="loadFile(item.path)" 
                                         class="flex flex-row items-center cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 px-2 py-1 rounded truncate w-full"
                                         :class="{'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 font-medium': activeFile === item.path}">
                                        <svg class="w-4 h-4 mr-1.5 min-w-[16px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <span class="text-gray-700 dark:text-gray-300 truncate" x-text="item.name" :title="item.name"></span>
                                    </div>
                                </template>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Editor Area -->
            <div class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-[#1e1e1e] border-r border-gray-200 dark:border-gray-700 transition-colors duration-500"
                 :class="{'bg-green-50 dark:bg-green-900/20': isBlinking}">
                <div class="bg-gray-100 dark:bg-[#2d2d2d] flex border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
                    <template x-if="activeFile">
                        <div class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 border-r border-gray-200 dark:border-gray-700 flex items-center bg-white dark:bg-[#1e1e1e]">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            <span x-text="activeFile"></span>
                            <button @click="closeFile" class="ml-2 text-gray-400 hover:text-gray-800 dark:hover:text-white">&times;</button>
                        </div>
                    </template>
                </div>
                
                <div class="flex-1 overflow-auto relative">
                    <template x-if="isLoadingFile">
                        <div class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-black/50 z-10">
                            <span class="text-gray-600 dark:text-gray-300">Loading file...</span>
                        </div>
                    </template>
                    
                    <template x-if="!activeFile && !isLoadingFile">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-gray-400 dark:text-gray-500 italic">Select a file from the explorer to view its contents.</span>
                        </div>
                    </template>
                    
                    <textarea x-show="activeFile && !isLoadingFile" 
                              x-model="fileContent" 
                              readonly
                              class="w-full h-full resize-none p-4 font-mono text-sm bg-transparent text-gray-800 dark:text-[#d4d4d4] border-none focus:ring-0 outline-none whitespace-pre"
                              style="tab-size: 4;"
                    ></textarea>
                </div>
            </div>

            <!-- Right Panel: AI Chat -->
            <div class="w-1/4 min-w-[300px] max-w-[400px] bg-white dark:bg-gray-800 flex flex-col relative z-20">
                <div class="p-3 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-gray-300 flex items-center bg-indigo-50 dark:bg-indigo-900/30">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span>Gemini AI Agent</span>
                </div>

                <!-- Chat History -->
                <div class="flex-1 p-4 overflow-y-auto space-y-4" id="chat-window">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-full p-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="ml-3 bg-gray-100 dark:bg-gray-700 p-3 rounded-lg rounded-tl-none shadow-sm text-sm text-gray-800 dark:text-gray-200">
                            Hello! I am Gemini. Open any file in the Explorer and tell me how you want to modify it. E.g. "Add a welcome message to this blade file".
                        </div>
                    </div>

                    <template x-for="(msg, index) in messages" :key="index">
                        <div class="flex items-start" :class="{'justify-end': msg.role === 'user'}">
                            <!-- AI Avatar -->
                            <div x-show="msg.role === 'ai'" class="flex-shrink-0 bg-indigo-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            
                            <!-- Message Bubble -->
                            <div class="p-3 shadow-sm text-sm"
                                 :class="{
                                     'bg-indigo-600 text-white rounded-lg rounded-tr-none ml-8': msg.role === 'user',
                                     'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg rounded-tl-none mr-8': msg.role === 'ai',
                                     'bg-red-100 text-red-800 rounded-lg rounded-tl-none': msg.role === 'error'
                                 }">
                                <span x-text="msg.text"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Loading Indicator -->
                    <div x-show="isChatLoading" class="flex items-start">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-full p-2">
                            <svg class="animate-spin w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </div>
                        <div class="ml-3 p-3 rounded-lg rounded-tl-none bg-gray-100 dark:bg-gray-700 text-gray-500 text-sm italic font-medium">
                            Synthesizing code...
                        </div>
                    </div>
                </div>

                <!-- Chat Input Form -->
                <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <form @submit.prevent="sendChat">
                        <div x-show="!activeFile" class="text-xs text-red-500 mb-2 truncate">⚠️ Please open a file first to edit it.</div>
                        <div class="flex relative">
                            <input type="text" x-model="chatInput" placeholder="Ask me to edit this file..." 
                                   :disabled="isChatLoading || !activeFile"
                                   class="w-full pl-3 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white disabled:opacity-50">
                            <button type="submit" :disabled="isChatLoading || !activeFile || chatInput.trim() === ''" class="absolute inset-y-0 right-0 px-3 flex items-center bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 disabled:opacity-50">
                                <svg class="w-4 h-4" transform="rotate(90)" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
    function projectViewer(projectId) {
        return {
            id: projectId,
            tree: [],
            isLoadingTree: false,
            
            activeFile: null,
            fileContent: '',
            isLoadingFile: false,
            isBlinking: false,

            // Chat states
            chatInput: '',
            messages: [],
            isChatLoading: false,
            
            init() {
                this.loadTree();
            },
            
            async loadTree() {
                this.isLoadingTree = true;
                try {
                    const response = await fetch(`/project/${this.id}/tree`);
                    const data = await response.json();
                    
                    if (response.ok) {
                        this.tree = data.tree;
                    } else {
                        console.error('Failed to load tree:', data.error);
                    }
                } catch (error) {
                    console.error('Error:', error);
                } finally {
                    this.isLoadingTree = false;
                }
            },
            
            async loadFile(path) {
                if (this.activeFile === path) return;
                
                this.isLoadingFile = true;
                this.activeFile = path;
                this.fileContent = '';
                
                try {
                    const response = await fetch(`/project/${this.id}/file?path=${encodeURIComponent(path)}`);
                    const data = await response.json();
                    
                    if (response.ok) {
                        this.fileContent = data.content;
                    } else {
                        this.fileContent = `Error: ${data.error}`;
                    }
                } catch (error) {
                    this.fileContent = 'Error: Failed to load file from network.';
                } finally {
                    this.isLoadingFile = false;
                }
            },

            async sendChat() {
                if(!this.activeFile || this.chatInput.trim() === '') return;
                
                const prompt = this.chatInput;
                this.chatInput = '';
                
                this.messages.push({ role: 'user', text: prompt });
                this.scrollToBottom();

                this.isChatLoading = true;
                
                try {
                    const response = await fetch(`/project/${this.id}/ai-chat`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            prompt: prompt,
                            active_file_path: this.activeFile
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        this.messages.push({ role: 'ai', text: data.message || 'I have updated the file for you!' });
                        
                        // Update content in editor and flash it
                        if(data.edited_content) {
                            this.fileContent = data.edited_content;
                            this.isBlinking = true;
                            setTimeout(() => { this.isBlinking = false; }, 1500);
                        }
                        
                    } else {
                        this.messages.push({ role: 'error', text: data.error || 'The AI failed to process the request.' });
                    }
                } catch (error) {
                    console.error('Chat API Error:', error);
                    this.messages.push({ role: 'error', text: 'Network connection failed.' });
                } finally {
                    this.isChatLoading = false;
                    this.scrollToBottom();
                }
            },

            scrollToBottom() {
                setTimeout(() => {
                    const chatBox = document.getElementById('chat-window');
                    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
                }, 50);
            },
            
            closeFile() {
                this.activeFile = null;
                this.fileContent = '';
            }
        }
    }
</script>
@endsection
