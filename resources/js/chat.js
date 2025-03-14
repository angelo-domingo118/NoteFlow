export function initChat(notebookId) {
    const chatForm = document.getElementById('chat-form');
    const chatMessages = document.getElementById('chat-messages');
    const questionInput = document.getElementById('question');
    const suggestionsContainer = document.getElementById('suggestions');
    
    let isGenerating = false;
    
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
                    </div>
                `;
            }
        });
    }
    
    if (editNotebookBtn) {
        editNotebookBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-notebook' }));
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

        try {
            const response = await fetch(`/notebooks/${notebookId}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ question }),
            });

            const data = await response.json();
            
            if (response.ok) {
                appendMessage('assistant', data.response);
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
        }
    });

    // Load suggested questions
    async function loadSuggestions() {
        try {
            const response = await fetch(`/notebooks/${notebookId}/suggestions`);
            const data = await response.json();
            
            if (response.ok && data.suggestions.length > 0) {
                renderSuggestions(data.suggestions);
            }
        } catch (error) {
            console.error('Failed to load suggestions:', error);
        }
    }

    // Render suggested questions
    function renderSuggestions(suggestions) {
        suggestionsContainer.innerHTML = suggestions
            .map(suggestion => `
                <button type="button"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200"
                    onclick="document.getElementById('question').value = this.textContent">
                    ${suggestion}
                </button>
            `)
            .join('');
    }

    // Append a message to the chat
    function appendMessage(type, content) {
        // Clear chat messages if this is the first message
        if (chatMessages.children.length === 1 && chatMessages.querySelector('.text-center')) {
            chatMessages.innerHTML = '';
        }

        const messageElement = document.createElement('div');
        messageElement.className = 'flex items-start space-x-4';

        const iconClass = type === 'user' ? 'text-blue-600' : type === 'assistant' ? 'text-green-600' : 'text-red-600';
        const messageClass = type === 'error' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100';

        // Create action buttons for AI responses
        let actionButtons = '';
        if (type === 'assistant') {
            actionButtons = `
                <div class="flex space-x-2 my-1">
                    <button type="button" class="copy-response-btn inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Copy
                    </button>
                    <button type="button" class="save-to-notes-btn inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Save to Notes
                    </button>
                </div>
            `;
        }

        messageElement.innerHTML = `
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 ${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${getIconPath(type)}
                </svg>
            </div>
            <div class="flex-1 space-y-1">
                <p class="text-sm font-medium ${messageClass}">${type.charAt(0).toUpperCase() + type.slice(1)}</p>
                <p class="text-sm ${messageClass} whitespace-pre-wrap">${content}</p>
                ${actionButtons}
                <p class="text-xs text-gray-500">${new Date().toLocaleTimeString()}</p>
            </div>
        `;

        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Add event listeners for the buttons if this is an AI response
        if (type === 'assistant') {
            const copyBtn = messageElement.querySelector('.copy-response-btn');
            const saveBtn = messageElement.querySelector('.save-to-notes-btn');
            
            // Copy to clipboard functionality
            copyBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(content)
                    .then(() => {
                        // Show a temporary success message
                        const originalText = copyBtn.innerHTML;
                        copyBtn.innerHTML = `
                            <svg class="h-3 w-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Copied!
                        `;
                        setTimeout(() => {
                            copyBtn.innerHTML = originalText;
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
                
                // Call once and also set up a small delay as backup
                setContentInEditor();
                setTimeout(setContentInEditor, 600);
            });
        }
    }

    // Get SVG path for message icon
    function getIconPath(type) {
        switch (type) {
            case 'user':
                return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />';
            case 'assistant':
                return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />';
            case 'error':
                return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
        }
    }
}
