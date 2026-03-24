<!-- AI Coach Floating Button -->
<button class="btn btn-primary rounded-circle shadow-lg ai-fab d-flex align-items-center justify-content-center" type="button" id="aiCoachToggle" aria-label="Chat with AI Coach">
    <i class="fa-solid fa-robot fs-3"></i>
</button>

<!-- AI Coach Chat Window -->
<div class="ai-chat-window shadow-lg rounded-4 d-flex flex-column" id="aiChatWindow">
    <!-- Header -->
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-primary text-white" style="border-radius: 1rem 1rem 0 0;">
        <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="fa-solid fa-robot"></i> AI Coach
        </h6>
        <button type="button" class="btn-close btn-close-white" id="aiChatClose" aria-label="Close"></button>
    </div>
    
    <!-- Messages Body -->
    <div class="p-3 flex-grow-1 overflow-auto bg-light" id="aiChatMessages" style="scrollbar-width: thin;">
        <div class="d-flex mb-3">
            <div class="bg-white p-3 rounded-4 shadow-sm text-dark border me-4" style="border-radius: 0 1rem 1rem 1rem !important; font-size: 0.95rem;">
                Hello! I am your AI Coach powered by Ollama. Ask me anything about managing your habits, tasks, or life goals!
            </div>
        </div>
    </div>

    <!-- Debug Log (Hidden by default) -->
    <div id="aiDebugLog" class="d-none bg-dark text-success p-2 small font-monospace" style="max-height: 100px; overflow-y: auto; font-size: 10px; border-top: 1px solid #444;"></div>
    
    <!-- Input Form -->
    <div class="p-3 border-top bg-white" style="border-radius: 0 0 1rem 1rem;">
        <form id="aiChatForm" class="d-flex gap-2 m-0">
            <input type="text" class="form-control rounded-pill bg-light border-0 px-4 py-2 shadow-none" id="aiChatInput" placeholder="Ask your coach..." autocomplete="off" required>
            <button type="submit" class="btn btn-primary rounded-circle shadow-sm flex-shrink-0 d-flex align-items-center justify-content-center" id="aiChatSubmit" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('aiCoachToggle');
    const closeBtn = document.getElementById('aiChatClose');
    const chatWindow = document.getElementById('aiChatWindow');
    const chatForm = document.getElementById('aiChatForm');
    const chatInput = document.getElementById('aiChatInput');
    const chatMessages = document.getElementById('aiChatMessages');
    const submitBtn = document.getElementById('aiChatSubmit');
    
    // Maintain chat history for context (last 10 turns)
    let chatHistory = [];

    // Toggle window visibility
    toggleBtn.addEventListener('click', () => {
        chatWindow.classList.add('active');
        toggleBtn.classList.add('d-none');
        chatInput.focus();
    });

    closeBtn.addEventListener('click', () => {
        chatWindow.classList.remove('active');
        toggleBtn.classList.remove('d-none');
    });

    // Handle form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const prompt = chatInput.value.trim();
        if(!prompt) return;

        // Add user message
        appendMessage(prompt, 'user');
        chatInput.value = '';
        
        // Disable input and show loading
        submitBtn.disabled = true;
        chatInput.disabled = true;
        const loadingId = appendMessage('...', 'assistant', true);

        // Fetch from OllamaController
        fetch('{{ route("ollama.generate", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                prompt: prompt, 
                model: 'translategemma:latest',
                history: chatHistory
            })
        })
        .then(async response => {
            if (!response.ok) {
                const errorJson = await response.json().catch(() => ({}));
                throw new Error(errorJson.message || `Server Error (${response.status})`);
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let assistantMessage = '';
            let buffer = '';
            const loadingMsg = document.getElementById(loadingId);
            loadingMsg.innerHTML = ''; 

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop(); // Keep partial line

                for (const line of lines) {
                    const trimmed = line.trim();
                    if (!trimmed) continue;
                    try {
                        const json = JSON.parse(trimmed);
                        if (json.message && json.message.content) {
                            assistantMessage += json.message.content;
                            loadingMsg.innerHTML = formatAIResponse(assistantMessage);
                            scrollToBottom();
                        }
                    } catch (e) {
                        console.error("Stream parse error:", e, trimmed);
                    }
                }
            }

            // Handle any remaining content in buffer
            if (buffer.trim()) {
                try {
                    const json = JSON.parse(buffer.trim());
                    if (json.message && json.message.content) {
                        assistantMessage += json.message.content;
                        loadingMsg.innerHTML = formatAIResponse(assistantMessage);
                    }
                } catch(e) {}
            }

            console.log("Full Assistant Message:", assistantMessage);

            // Add user message to history
            chatHistory.push({ role: 'user', content: prompt });

            // Extract and execute actions
            const actions = extractActions(assistantMessage);
            
            // Add clean assistant message to history (without JSON)
            const cleanAssistantMessage = assistantMessage.replace(/\[ACTION\][\s\S]*?(\[\/ACTION\]|$)/g, '').trim();
            if (cleanAssistantMessage) {
                chatHistory.push({ role: 'assistant', content: cleanAssistantMessage });
            }
            
            // Keep history manageable
            if (chatHistory.length > 20) chatHistory = chatHistory.slice(-20);

            if (actions.length > 0) {
                let successCount = 0;
                for (const action of actions) {
                    if (await handleAIAction(action)) {
                        successCount++;
                    }
                }
                if (successCount > 0) {
                    setTimeout(() => {
                        showToast("Reloading to apply changes...", "success");
                        setTimeout(() => window.location.reload(), 1500);
                    }, 1000);
                }
            }
        })
        .catch(err => {
            console.error(err);
            const loadingMsg = document.getElementById(loadingId);
            if (loadingMsg) {
                loadingMsg.innerHTML = `<span class="text-danger fw-bold">接続エラー: ${err.message || 'AIモデルに接続できません。'}</span>`;
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            chatInput.disabled = false;
            chatInput.focus();
            scrollToBottom();
        });
    });

    // Helper to format AI response and hide JSON actions
    function formatAIResponse(text) {
        // Match both complete and potentially partial JSON actions to keep the UI clean
        const tokenActionPattern = /\[ACTION\][\s\S]*?(\[\/ACTION\]|$)/g;
        
        let cleanText = text.replace(tokenActionPattern, '').trim();
        
        if (!cleanText && text.includes('[ACTION]')) {
            return '<i class="fa-solid fa-gear fa-spin me-1"></i> <em class="text-muted small">Synchronizing with database...</em>';
        }

        // Escape HTML to prevent XSS
        const div = document.createElement('div');
        div.innerText = cleanText;
        let html = div.innerHTML;

        // Replace markdown-style bullets (* or - or •) with a consistent bullet
        html = html.replace(/^[\s]*[\*\-\•][\s]+/gm, '• ');
        
        // Convert newlines to <br> for HTML display
        return html.replace(/\n/g, '<br>');
    }

    function appendMessage(text, sender, isLoading = false) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `d-flex mb-3 ${sender === 'user' ? 'justify-content-end' : ''}`;
        
        const bubble = document.createElement('div');
        
        if(sender === 'user') {
            bubble.className = 'p-3 rounded-4 shadow-sm bg-primary text-white ms-4';
            // Custom border radius for user (tail on right)
            bubble.style.cssText = 'border-radius: 1rem 1rem 0 1rem !important; font-size: 0.95rem;';
        } else {
            bubble.className = 'p-3 rounded-4 shadow-sm bg-white text-dark me-4 border';
            // Custom border radius for assistant (tail on left)
            bubble.style.cssText = 'border-radius: 0 1rem 1rem 1rem !important; font-size: 0.95rem;';
        }
        
        if (isLoading) {
            const id = 'msg-' + Date.now();
            bubble.id = id;
            bubble.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-primary"></i> <span class="ms-1 text-muted small">Thinking...</span>';
            msgDiv.appendChild(bubble);
            chatMessages.appendChild(msgDiv);
            scrollToBottom();
            return id;
        }

        bubble.innerText = text;
        msgDiv.appendChild(bubble);
        chatMessages.appendChild(msgDiv);
        scrollToBottom();
    }

    // --- AI Action Handling Logic ---

    function extractActions(text) {
        // Extract content between [ACTION] and [/ACTION]
        const actionPattern = /\[ACTION\]([\s\S]*?)\[\/ACTION\]/g;
        let matches = [];
        let match;
        while ((match = actionPattern.exec(text)) !== null) {
            matches.push(match[1]);
        }
        
        console.log("Found action matches:", matches.length);
        
        return matches.map(m => {
            try { 
                const parsed = JSON.parse(m.trim());
                console.log("Successfully parsed action:", parsed.action);
                return parsed;
            } catch(e) { 
                console.error("JSON parse error for action block:", e, m);
                return null; 
            }
        }).filter(m => m !== null);
    }

    async function handleAIAction(action) {
        logDebug("Executing AI Action:", action);
        
        const actionMap = {
            'create_task': { url: '{{ route("tasks.store", [], false) }}', method: 'POST', data: { priority_type: action.priority || 1, due_date: action.date, is_repeat: 0 } },
            'update_task': { url: `/tasks/${action.id}/update`, method: 'PATCH', data: { priority_type: action.priority || 1, due_date: action.date } },
            'delete_task': { url: `/tasks/${action.id}/destroy`, method: 'DELETE' },
            'complete_task': { url: `/tasks/${action.id}/complete`, method: 'PATCH' },
            'create_habit': { url: '{{ route("habits.store", [], false) }}', method: 'POST', data: { repeat_type: action.repeat_type || 1, start_date: action.start_date || new Date().toISOString().split('T')[0] } },
            'update_habit': { url: `/habits/${action.id}/update`, method: 'PUT' },
            'delete_habit': { url: `/habits/${action.id}/delete`, method: 'DELETE' },
            'toggle_habit': { url: `/habits/${action.id}/toggle`, method: 'POST', data: { date: new Date().toISOString().split('T')[0] } },
            'create_goal': { url: '{{ route("lifeplan.goal.store", [], false) }}', method: 'POST', data: { description: action.description || "" } },
            'update_goal': { url: `/lifeplan/goal/${action.id}`, method: 'PUT', data: { description: action.description || "" } },
            'delete_goal': { url: `/lifeplan/goal/${action.id}`, method: 'DELETE' },
            'create_milestone': { url: '{{ route("lifeplan.milestone.store", [], false) }}', method: 'POST' },
            'update_milestone': { url: `/lifeplan/milestone/${action.id}`, method: 'PUT' },
            'delete_milestone': { url: `/lifeplan/milestone/${action.id}`, method: 'DELETE' },
            'create_journal': { url: '{{ route("journals.store", [], false) }}', method: 'POST' },
            'update_journal': { url: `/journals/${action.id}/update`, method: 'PUT' },
            'delete_journal': { url: `/journals/${action.id}/delete`, method: 'DELETE' },
            'create_category': { url: '{{ route("lifeplan.category.store", [], false) }}', method: 'POST', data: { color_id: 1, icon_id: 1 } },
            'update_category': { url: `/lifeplan/category/${action.id}`, method: 'PUT' },
            'delete_category': { url: `/lifeplan/category/${action.id}`, method: 'DELETE' },
        };

        const config = actionMap[action.action];
        if (!config) {
            logDebug("Unknown action name:", action.action);
            return false;
        }

        const bodyData = { ...config.data, ...action };
        delete bodyData.action;

        try {
            logDebug(`Sending ${config.method} to ${config.url}`, bodyData);
            const response = await fetch(config.url, {
                method: config.method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(bodyData)
            });

            logDebug(`Response status: ${response.status}`);
            
            if (response.status === 419) {
                showToast("Session expired. Please refresh the page.", "danger");
                return false;
            }

            const result = await response.json();
            if (result.success) {
                logDebug("Action success:", result.message);
                showToast(result.message || "Update successful", "success");
                return true;
            } else {
                logDebug("Action fail server-side:", result.message || result.errors);
                showToast(result.message || "Action failed", "danger");
                return false;
            }
        } catch (e) {
            logDebug("Fetch error:", e.message);
            showToast("Network error", "danger");
            return false;
        }
    }

    function showToast(message, type = "dark") {
        // Simple temporary notification
        const toast = document.createElement('div');
        const bgColor = type === "success" ? "bg-success" : (type === "danger" ? "bg-danger" : "bg-dark");
        const icon = type === "success" ? "fa-check-circle" : (type === "danger" ? "fa-triangle-exclamation" : "fa-info-circle");
        
        toast.className = `position-fixed bottom-0 start-50 translate-middle-x mb-5 ${bgColor} text-white px-4 py-2 rounded-pill shadow-lg animate__animated animate__fadeInUp`;
        toast.style.zIndex = '9999';
        toast.style.fontSize = '0.85rem';
        toast.innerHTML = `<i class="fa-solid ${icon} me-2"></i> ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.replace('animate__fadeInUp', 'animate__fadeOutDown');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }

    function logDebug(msg, data = null) {
        const debugArea = document.getElementById('aiDebugLog');
        if (!debugArea) return;
        const entry = document.createElement('div');
        entry.className = 'mb-1';
        entry.textContent = `[${new Date().toLocaleTimeString()}] ${msg} ${data ? JSON.stringify(data) : ''}`;
        debugArea.appendChild(entry);
        debugArea.scrollTop = debugArea.scrollHeight;
        console.log(`[AI DEBUG] ${msg}`, data);
    }
    
    // Enable debug log if URL has ?debugAI
    if (window.location.search.includes('debugAI')) {
        document.getElementById('aiDebugLog').classList.remove('d-none');
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>
