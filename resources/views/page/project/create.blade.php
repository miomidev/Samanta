@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create New Project') }}
    </h2>
@endsection

@section('content')
<div class="py-12" x-data="projectGenerator()">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            
            <!-- FORM MODE -->
            <div class="p-6 text-gray-900 dark:text-gray-100" x-show="mode === 'form'">
                <form @submit.prevent="submitForm" class="space-y-6">
                    
                    <!-- Basic Info -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Project Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="project_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Name</label>
                                <input type="text" x-model="form.project_name" id="project_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <input type="text" x-model="form.description" id="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Database Config -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Database Configuration</h3>
                        
                        <div class="mb-4">
                            <label for="db_connection" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Database Type</label>
                            <select x-model="form.db_connection" id="db_connection" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                <option value="mysql">MySQL</option>
                                <option value="pgsql">PostgreSQL</option>
                                <option value="sqlite">SQLite</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-show="form.db_connection !== 'sqlite'">
                            <div>
                                <label for="db_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Host</label>
                                <input type="text" x-model="form.db_host" id="db_host" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            </div>
                            <div>
                                <label for="db_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Port</label>
                                <input type="number" x-model="form.db_port" id="db_port" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            </div>
                            <div>
                                <label for="db_database" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Database Name</label>
                                <input type="text" x-model="form.db_database" id="db_database" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            </div>
                            <div>
                                <label for="db_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                                <input type="text" x-model="form.db_username" id="db_username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            </div>
                            <div>
                                <label for="db_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                <input type="password" x-model="form.db_password" id="db_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            </div>
                        </div>

                        <!-- Update defaults when DB type changes -->
                        <div x-effect="
                            if (form.db_connection === 'mysql') {
                                form.db_port = 3306;
                                form.db_username = 'root';
                            } else if (form.db_connection === 'pgsql') {
                                form.db_port = 5432;
                                form.db_username = 'postgres';
                            }
                        "></div>
                    </div>

                    <!-- Submit Button & Loading -->
                    <div class="pt-5 flex items-center justify-end">
                        <span x-show="errorMessage" class="text-red-500 mr-4" x-text="errorMessage"></span>
                        <button type="submit" :disabled="isLoading" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50">
                            <span x-show="!isLoading">Start Generation</span>
                            <span x-show="isLoading">Initializing...</span>
                        </button>
                    </div>

                </form>
            </div>

            <!-- TERMINAL MODE -->
            <div x-show="mode === 'terminal'" class="p-0 bg-gray-900 border border-black rounded shadow-lg overflow-hidden h-[600px] flex flex-col" style="display: none;">
                <!-- Terminal Header Bar -->
                <div class="bg-gray-800 text-gray-400 py-2 px-4 font-sans text-xs flex justify-between tracking-wide border-b border-gray-700">
                    <div class="flex space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <span>Composer Installation Sequence</span>
                </div>
                
                <!-- Terminal Output Window -->
                <div class="flex-1 p-4 font-mono text-sm overflow-y-auto text-green-400 leading-relaxed" id="terminal-window">
                    <div class="animate-pulse mb-4 text-white">⚙️ AI Builder Booting Engine...</div>
                    <pre class="whitespace-pre-wrap font-mono" x-text="terminalOutput"></pre>
                    
                    <div x-show="!isDone" class="mt-2 text-indigo-400 flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Building dependencies. Please wait... This may take up to 2 minutes...
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function projectGenerator() {
        return {
            mode: 'form', // 'form' or 'terminal'
            form: {
                project_name: 'My New App',
                description: '',
                db_connection: 'mysql',
                db_host: '127.0.0.1',
                db_port: 3306,
                db_database: 'laravel',
                db_username: 'root',
                db_password: ''
            },
            isLoading: false,
            errorMessage: '',
            
            // Terminal props
            generatedProjectId: null,
            terminalOutput: '',
            isDone: false,
            pollInterval: null,
            
            async submitForm() {
                this.isLoading = true;
                this.errorMessage = '';
                
                try {
                    const response = await fetch('/project/generate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        // Switch to Terminal Mode
                        this.generatedProjectId = data.project_id;
                        this.mode = 'terminal';
                        this.startPolling();
                    } else {
                        this.errorMessage = data.error || data.message || 'An error occurred';
                    }
                } catch (error) {
                    this.errorMessage = 'Network error. Please try again.';
                    console.error('Error:', error);
                } finally {
                    this.isLoading = false;
                }
            },
            
            startPolling() {
                this.pollInterval = setInterval(async () => {
                    if(this.isDone) return clearInterval(this.pollInterval);
                    
                    try {
                        const response = await fetch(`/project/${this.generatedProjectId}/stream-log`);
                        const data = await response.json();
                        
                        this.terminalOutput = data.log || '';
                        
                        // Auto scroll
                        const terminalBlock = document.getElementById('terminal-window');
                        if(terminalBlock) {
                            terminalBlock.scrollTop = terminalBlock.scrollHeight;
                        }

                        if(data.done) {
                            this.isDone = true;
                            clearInterval(this.pollInterval);
                            
                            // 2 sec delay before redirection to show completion
                            setTimeout(() => {
                                window.location.href = `/project/${this.generatedProjectId}/viewer`;
                            }, 2000);
                        }
                    } catch (e) {
                         // wait next cycle
                    }
                }, 1500); // 1.5 seconds polling rate so it feels live
            }
        }
    }
</script>
@endsection
