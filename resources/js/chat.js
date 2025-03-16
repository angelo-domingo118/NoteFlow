export function initChat(notebookId) {
    const chatForm = document.getElementById('chat-form');
    const chatMessages = document.getElementById('chat-messages');
    const questionInput = document.getElementById('question');
    const suggestionsContainer = document.getElementById('suggestions');
    const scrollToBottomBtn = document.getElementById('scroll-to-bottom');
    const sourceReferenceIndicator = document.getElementById('source-reference');
    const activeSourcesCount = document.getElementById('active-sources-count');
    const activeSourcesIndicator = document.getElementById('active-sources-indicator');
    
    let isGenerating = false;
    let lastScrollPosition = 0;
    let isNearBottom = true;
    
    // Load saved messages from localStorage
    loadSavedMessages();
    
    // Update active sources count
    updateActiveSourcesCount();
    
    // Character count functionality only
    questionInput.addEventListener('input', function() {
        // Character count functionality can be added here if needed
    });
    
    // Handle textarea Enter key
    questionInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey && !isGenerating) {
            e.preventDefault();
            const trimmedValue = questionInput.value.trim();
            if (trimmedValue) {
                chatForm.dispatchEvent(new Event('submit'));
            }
        }
    });

    // Initialize event listeners for buttons
    const refreshChatBtn = document.getElementById('refresh-chat-btn');
    const editNotebookBtn = document.getElementById('edit-notebook-btn');
    
    if (refreshChatBtn) {
        refreshChatBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to clear the chat history?')) {
                chatMessages.innerHTML = `
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <h3 class="text-lg font-medium mb-2">Ask questions about your sources</h3>
                        <p class="max-w-md mx-auto">
                            Use this chat to ask questions about the content in your sources. The AI will analyze your active sources and provide relevant answers.
                        </p>
                        
                        <!-- Quick start suggestions -->
                        <div class="mt-6">
                            <h4 class="text-sm font-medium mb-3">Try asking:</h4>
                            <div id="suggestions" class="flex flex-col space-y-2 max-w-md mx-auto">
                                <!-- Suggestions will be populated here -->
                            </div>
                        </div>
                    </div>
                `;
                // Clear saved messages from localStorage
                clearSavedMessages();
                // Reload suggestions
                loadSuggestions();
            }
        });
    }
    
    if (editNotebookBtn) {
        editNotebookBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-notebook' }));
        });
    }
    
    // Scroll to bottom button functionality
    if (scrollToBottomBtn) {
        scrollToBottomBtn.addEventListener('click', () => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
            scrollToBottomBtn.classList.add('hidden');
            isNearBottom = true;
        });
        
        // Show/hide scroll to bottom button based on scroll position
        chatMessages.addEventListener('scroll', () => {
            const currentScrollPosition = chatMessages.scrollTop;
            const maxScroll = chatMessages.scrollHeight - chatMessages.clientHeight;
            
            // Check if user is scrolling up
            if (currentScrollPosition < lastScrollPosition) {
                // Check if not near bottom
                if (maxScroll - currentScrollPosition > 100) {
                    isNearBottom = false;
                    scrollToBottomBtn.classList.remove('hidden');
                }
            } else if (maxScroll - currentScrollPosition < 50) {
                // User is near bottom
                isNearBottom = true;
                scrollToBottomBtn.classList.add('hidden');
            }
            
            lastScrollPosition = currentScrollPosition;
        });
    }

    // Load suggested questions
    loadSuggestions();

    // Handle chat form submission
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (isGenerating) return;

        const question = questionInput.value.trim();
        if (!question) return;

        // Show loading states
        const chatLoading = document.getElementById('chat-loading');
        const submitButton = chatForm.querySelector('button[type="submit"]');
        const loadingHide = submitButton.querySelector('.loading-hide');
        const loadingShow = submitButton.querySelector('.loading-show');
        
        chatLoading.classList.remove('hidden');
        submitButton.disabled = true;
        loadingHide.classList.add('hidden');
        loadingShow.classList.remove('hidden');

        isGenerating = true;
        appendMessage('user', question);
        questionInput.value = '';
        // No need to adjust height since it's fixed
        // Source reference is now always visible

        try {
            const response = await fetch(`/notebooks/${notebookId}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ 
                    question,
                    thinking_mode: localStorage.getItem('thinking_mode_enabled') === 'true'
                }),
            });

            const data = await response.json();
            
            if (response.ok) {
                appendMessage('assistant', data.response, data.sources || []);
            } else {
                appendMessage('error', data.error || 'Failed to generate response. Please try again.');
            }
        } catch (error) {
            appendMessage('error', 'Network error occurred. Please try again.');
        } finally {
            isGenerating = false;
            // Hide loading states
            chatLoading.classList.add('hidden');
            submitButton.disabled = false;
            loadingHide.classList.remove('hidden');
            loadingShow.classList.add('hidden');
            
            // Focus back on input
            questionInput.focus();
        }
    });

    // Load suggested questions
    async function loadSuggestions() {
        try {
            const response = await fetch(`/notebooks/${notebookId}/suggestions`);
            const data = await response.json();
            
            if (response.ok && data.suggestions.length > 0) {
                renderSuggestions(data.suggestions);
            } else {
                // Default suggestions if none are returned from the server
                renderSuggestions([
                    'Summarize the key points from all sources',
                    'What are the main arguments presented?',
                    'Compare and contrast the different perspectives'
                ]);
            }
        } catch (error) {
            console.error('Failed to load suggestions:', error);
            // Default suggestions on error
            renderSuggestions([
                'Summarize the key points from all sources',
                'What are the main arguments presented?',
                'Compare and contrast the different perspectives'
            ]);
        }
    }

    // Render suggested questions
    function renderSuggestions(suggestions) {
        if (!suggestionsContainer) return;
        
        suggestionsContainer.innerHTML = suggestions
            .map(suggestion => `
                <button type="button"
                    class="text-sm px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    onclick="document.getElementById('question').value = this.textContent; document.getElementById('question').focus();">
                    ${suggestion}
                </button>
            `)
            .join('');
            
        // Add event listeners to suggestion buttons
        suggestionsContainer.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', function() {
                questionInput.value = this.textContent;
                questionInput.focus();
                // Source reference is now always visible
                
                // Trigger input event for character count
                questionInput.dispatchEvent(new Event('input'));
            });
        });
    }

    // Append a message to the chat
    function appendMessage(type, content, sources = []) {
        // Clear chat messages if this is the first message
        if (chatMessages.children.length === 1 && chatMessages.querySelector('.text-center')) {
            chatMessages.innerHTML = '';
        }

        const messageElement = document.createElement('div');
        messageElement.className = 'mb-2 chat-message-enter';

        const iconClass = type === 'user' ? 'text-blue-600 dark:text-blue-400' : 
                         type === 'assistant' ? 'text-blue-600 dark:text-blue-400' : 
                         'text-red-600 dark:text-red-400';
                         
        const messageClass = type === 'error' ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-gray-100';
        const bgClass = type === 'user' ? '' : 'bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4';

        // Create source citations if available
        let sourceCitations = '';
        if (type === 'assistant' && sources.length > 0) {
            const sourceList = sources.map((source, index) => 
                `<li class="text-xs text-gray-500 dark:text-gray-400">
                    <a href="#" class="hover:underline" title="${source.title || 'Source'}">[${index + 1}] ${source.title || 'Source'}</a>
                </li>`
            ).join('');
            
            sourceCitations = `
                <div class="mt-2 border-t border-gray-200 dark:border-gray-600 pt-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Sources:</p>
                    <ol class="list-decimal list-inside space-y-1">
                        ${sourceList}
                    </ol>
                </div>
            `;
        }

        // Create action buttons for AI responses
        let actionButtons = '';
        if (type === 'assistant') {
            actionButtons = `
                <div class="flex flex-wrap gap-2 mt-3">
                    <button type="button" class="action-button copy-response-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Copy
                    </button>
                    <button type="button" class="action-button save-to-notes-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Save to Notes
                    </button>
                    <button type="button" class="action-button regenerate-response-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Regenerate
                    </button>
                </div>
            `;
        }

        const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        if (type === 'user') {
            messageElement.innerHTML = `
                <div class="flex items-start justify-end mb-1">
                    <div class="user-message">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs font-medium text-blue-600 dark:text-blue-400">You</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 ml-2">${timestamp}</p>
                        </div>
                        <p class="text-sm ${messageClass} whitespace-pre-wrap">${content}</p>
                    </div>
                </div>
            `;
        } else {
            messageElement.innerHTML = `
                <div class="flex items-start mt-1">
                    <div class="flex-shrink-0 mr-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center ${type === 'assistant' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-red-100 dark:bg-red-900/30'}">
                            <svg class="h-5 w-5 ${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${getIconPath(type)}
                            </svg>
                        </div>
                    </div>
                    <div class="${type === 'assistant' ? 'assistant-message flex-1' : 'flex-1 max-w-[90%]'}">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs font-medium ${type === 'assistant' ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400'}">${type === 'assistant' ? 'AI Assistant' : 'Error'}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${timestamp}</p>
                        </div>
                        <div class="text-sm ${messageClass} whitespace-pre-wrap prose prose-sm dark:prose-invert max-w-none">${formatMessage(content)}</div>
                        ${sourceCitations}
                        ${actionButtons}
                    </div>
                </div>
            `;
        }

        chatMessages.appendChild(messageElement);
        
        // Scroll to bottom if user was already at the bottom
        if (isNearBottom) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } else {
            // Show scroll to bottom button
            scrollToBottomBtn.classList.remove('hidden');
        }
        
        // Save message to localStorage
        saveMessage(type, content, timestamp, sources);
        
        // Add event listeners for the buttons if this is an AI response
        if (type === 'assistant') {
            const copyBtn = messageElement.querySelector('.copy-response-btn');
            const saveBtn = messageElement.querySelector('.save-to-notes-btn');
            const regenerateBtn = messageElement.querySelector('.regenerate-response-btn');
            
            // Copy to clipboard functionality
            copyBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(content)
                    .then(() => {
                        // Show a temporary success message
                        const originalText = copyBtn.innerHTML;
                        copyBtn.innerHTML = `
                            <svg class="text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Copied!
                        `;
                        copyBtn.classList.add('bg-green-50', 'text-green-600', 'border-green-200');
                        
                        setTimeout(() => {
                            copyBtn.innerHTML = originalText;
                            copyBtn.classList.remove('bg-green-50', 'text-green-600', 'border-green-200');
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('Failed to copy text: ', err);
                    });
            });
            
            // Save to notes functionality
            saveBtn.addEventListener('click', () => {
                // Store the content in a global variable so it can be accessed after modal opens
                window.aiResponseContent = content;
                
                // Populate the title input
                const titleInput = document.getElementById('title-new');
                if (titleInput) {
                    titleInput.value = `AI Response - ${new Date().toLocaleString()}`;
                }
                
                // Open the add note modal
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-note' }));
                
                // Set up an event listener for the modal opening
                const setContentInEditor = () => {
                    const contentInput = document.getElementById('content-new');
                    if (contentInput) {
                        // Set the value in the textarea
                        contentInput.value = window.aiResponseContent;
                        
                        // Wait a bit for TinyMCE to initialize
                        setTimeout(() => {
                            if (window.tinymce) {
                                const editor = window.tinymce.get('content-new');
                                if (editor) {
                                    editor.setContent(window.aiResponseContent);
                                }
                            }
                        }, 500);
                    }
                };
                
                // Execute immediately and also set up a listener for modal opening
                setTimeout(setContentInEditor, 100);
                
                // Execute once and then remove the event listener
                const handleModalOpen = () => {
                    setTimeout(setContentInEditor, 300);
                    document.removeEventListener('x-dialog-opened', handleModalOpen);
                };
                
                document.addEventListener('x-dialog-opened', handleModalOpen);
            });
            
            // Regenerate response functionality
            regenerateBtn.addEventListener('click', () => {
                // Find the last user message
                const userMessages = Array.from(chatMessages.querySelectorAll('.user-message'));
                
                if (userMessages.length > 0) {
                    const lastUserMessage = userMessages[userMessages.length - 1];
                    const messageContent = lastUserMessage.querySelector('.whitespace-pre-wrap').textContent;
                    
                    // Set the input value to the last user message
                    questionInput.value = messageContent;
                    
                    // Remove the last assistant message (current message)
                    messageElement.remove();
                    
                    // Submit the form to regenerate
                    chatForm.dispatchEvent(new Event('submit'));
                }
            });
        }
    }
    
    // Format message with markdown-like syntax
    function formatMessage(content) {
        // Convert markdown-like syntax to HTML
        let formattedContent = content
            // Bold
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            // Italic
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            // Code blocks
            .replace(/```(.*?)```/gs, '<pre><code>$1</code></pre>')
            // Inline code
            .replace(/`(.*?)`/g, '<code class="bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">$1</code>')
            // Lists
            .replace(/^\s*-\s+(.*?)$/gm, '<li>$1</li>')
            // Headers
            .replace(/^###\s+(.*?)$/gm, '<h3 class="text-base font-semibold mt-3 mb-1">$1</h3>')
            .replace(/^##\s+(.*?)$/gm, '<h2 class="text-lg font-semibold mt-4 mb-2">$1</h2>')
            .replace(/^#\s+(.*?)$/gm, '<h1 class="text-xl font-bold mt-4 mb-2">$1</h1>');
            
        // Convert URLs to links
        formattedContent = formattedContent.replace(
            /(https?:\/\/[^\s]+)/g, 
            '<a href="$1" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">$1</a>'
        );
        
        return formattedContent;
    }

    function getIconPath(type) {
        if (type === 'user') {
            return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />';
        } else if (type === 'assistant') {
            return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />';
        } else {
            return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
        }
    }

    function saveMessage(type, content, timestamp, sources = []) {
        try {
            const savedMessages = JSON.parse(localStorage.getItem(`chat_${notebookId}`) || '[]');
            savedMessages.push({ type, content, timestamp, sources });
            localStorage.setItem(`chat_${notebookId}`, JSON.stringify(savedMessages));
        } catch (error) {
            console.error('Failed to save message to localStorage:', error);
        }
    }

    function loadSavedMessages() {
        try {
            const savedMessages = JSON.parse(localStorage.getItem(`chat_${notebookId}`) || '[]');
            
            if (savedMessages.length > 0) {
                // Clear the empty state
                chatMessages.innerHTML = '';
                
                // Append each saved message
                savedMessages.forEach(message => {
                    appendMessage(message.type, message.content, message.sources || []);
                });
            }
        } catch (error) {
            console.error('Failed to load messages from localStorage:', error);
            // Clear potentially corrupted data
            localStorage.removeItem(`chat_${notebookId}`);
        }
    }

    function clearSavedMessages() {
        try {
            localStorage.removeItem(`chat_${notebookId}`);
        } catch (error) {
            console.error('Failed to clear messages from localStorage:', error);
        }
    }
    
    // Update the active sources count
    function updateActiveSourcesCount() {
        const activeSourcesCheckboxes = document.querySelectorAll('input[name^="source-"]:checked');
        if (activeSourcesCount && activeSourcesIndicator) {
            const count = activeSourcesCheckboxes.length;
            activeSourcesCount.textContent = count;
            
            // Update the indicator color based on count
            if (count === 0) {
                activeSourcesIndicator.classList.remove('bg-blue-100', 'bg-green-100');
                activeSourcesIndicator.classList.add('bg-yellow-100');
                activeSourcesIndicator.classList.remove('dark:bg-blue-900/30', 'dark:bg-green-900/30');
                activeSourcesIndicator.classList.add('dark:bg-yellow-900/30');
                activeSourcesIndicator.classList.remove('text-blue-600', 'text-green-600');
                activeSourcesIndicator.classList.add('text-yellow-600');
                activeSourcesIndicator.classList.remove('dark:text-blue-400', 'dark:text-green-400');
                activeSourcesIndicator.classList.add('dark:text-yellow-400');
            } else if (count > 0 && count < 3) {
                activeSourcesIndicator.classList.remove('bg-yellow-100', 'bg-green-100');
                activeSourcesIndicator.classList.add('bg-blue-100');
                activeSourcesIndicator.classList.remove('dark:bg-yellow-900/30', 'dark:bg-green-900/30');
                activeSourcesIndicator.classList.add('dark:bg-blue-900/30');
                activeSourcesIndicator.classList.remove('text-yellow-600', 'text-green-600');
                activeSourcesIndicator.classList.add('text-blue-600');
                activeSourcesIndicator.classList.remove('dark:text-yellow-400', 'dark:text-green-400');
                activeSourcesIndicator.classList.add('dark:text-blue-400');
            } else {
                activeSourcesIndicator.classList.remove('bg-yellow-100', 'bg-blue-100');
                activeSourcesIndicator.classList.add('bg-green-100');
                activeSourcesIndicator.classList.remove('dark:bg-yellow-900/30', 'dark:bg-blue-900/30');
                activeSourcesIndicator.classList.add('dark:bg-green-900/30');
                activeSourcesIndicator.classList.remove('text-yellow-600', 'text-blue-600');
                activeSourcesIndicator.classList.add('text-green-600');
                activeSourcesIndicator.classList.remove('dark:text-yellow-400', 'dark:text-blue-400');
                activeSourcesIndicator.classList.add('dark:text-green-400');
            }
        }
    }
    
    // Listen for changes to source checkboxes
    document.addEventListener('change', function(e) {
        if (e.target && e.target.name && e.target.name.startsWith('source-')) {
            updateActiveSourcesCount();
        }
    });
    
    // Initial call to update sources count
    updateActiveSourcesCount();
}
