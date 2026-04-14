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
    
    if (!toggleBtn || !chatWindow || !chatForm || !chatInput || !chatMessages || !submitBtn) {
        console.warn("J.A.R.V.I.S. Chat: Core UI elements missing.");
        return;
    }

    let chatHistory = [];

    // Check for post-refresh success message
    const pendingSuccess = localStorage.getItem('ai_sync_success');
    if (pendingSuccess) {
        showToast(pendingSuccess, "success");
        localStorage.removeItem('ai_sync_success');
    }

    toggleBtn.addEventListener('click', () => {
        chatWindow.classList.add('active');
        toggleBtn.classList.add('d-none');
        chatInput.focus();
    });

    closeBtn.addEventListener('click', () => {
        chatWindow.classList.remove('active');
        toggleBtn.classList.remove('d-none');
    });

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const prompt = chatInput.value.trim();
        if(!prompt) return;

        appendMessage(prompt, 'user');
        chatInput.value = '';
        submitBtn.disabled = true;
        chatInput.disabled = true;
        const loadingId = appendMessage('...', 'assistant', true);

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
            if (!response.ok) throw new Error(`Server Error (${response.status})`);

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let assistantMessage = '';
            let buffer = '';
            let handledActions = new Set();
            let actionPromises = [];
            const loadingMsg = document.getElementById(loadingId);
            if (loadingMsg) loadingMsg.innerHTML = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop();

                for (const line of lines) {
                    const trimmed = line.trim();
                    if (!trimmed) continue;
                    try {
                        const json = JSON.parse(trimmed);
                        if (json.message && json.message.content) {
                            assistantMessage += json.message.content;
                            if (loadingMsg) loadingMsg.innerHTML = formatAIResponse(assistantMessage);
                            
                            const currentBlocks = extractActionBlocks(assistantMessage);
                            for (const block of currentBlocks) {
                                if (!handledActions.has(block)) {
                                    handledActions.add(block);
                                    try {
                                        let cleanBlock = block.replace(/```json/gi, '').replace(/```/g, '').trim();
                                        
                                        // Attempt to auto-fix common LLM JSON syntax mistakes (like single quotes)
                                        let parsedObj = null;
                                        try {
                                            parsedObj = JSON.parse(cleanBlock);
                                        } catch(err) {
                                            const fixedBlock = cleanBlock.replace(/'/g, '"').replace(/,\s*([}\]])/g, '$1');
                                            parsedObj = JSON.parse(fixedBlock);
                                        }
                                        
                                        if(parsedObj) {
                                            actionPromises.push(handleAIAction(parsedObj));
                                        }
                                    } catch(e) { console.error("Action JSON error", e, block); }
                                }
                            }
                            scrollToBottom();
                        }
                    } catch (e) { console.error("Stream parse error", e); }
                }
            }

            chatHistory.push({ role: 'user', content: prompt });
            let historyMessage = assistantMessage.replace(/\[ACTION\]([\s\S]*?)\[\/ACTION\]/g, (match, p1) => {
                try { 
                    let cleanBlock = p1.replace(/```json/gi, '').replace(/```/g, '').trim();
                    return ` (Executed Action: ${JSON.parse(cleanBlock).action}) `; 
                } catch(e) { return " (Action Executed) "; }
            }).replace(/\[ACTION\][\s\S]*$/g, '').trim();
            
            if (historyMessage) chatHistory.push({ role: 'assistant', content: historyMessage });
            if (chatHistory.length > 20) chatHistory = chatHistory.slice(-20);

            if (actionPromises.length > 0) {
                const results = await Promise.all(actionPromises);
                const hasSuccess = results.some(r => r === true);
                
                if (hasSuccess) {
                    setTimeout(() => {
                        localStorage.setItem('ai_sync_success', 'J.A.R.V.I.S. synchronized successfully!');
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast("No items were created. Please check the requirements.", "danger");
                }
            }
        })
        .catch(err => {
            console.error(err);
            const loadingMsg = document.getElementById(loadingId);
            if (loadingMsg) loadingMsg.innerHTML = `<span class="text-danger small">Error: ${err.message}</span>`;
        })
        .finally(() => {
            submitBtn.disabled = false;
            chatInput.disabled = false;
            chatInput.focus();
            scrollToBottom();
        });
    });

    function formatAIResponse(text) {
        if (!text) return '';
        let clean = text.replace(/\[ACTION\][\s\S]*?\[\/ACTION\]/gm, '')
                        .replace(/\[ACTION\][\s\S]*$/gm, '')
                        .replace(/```json[\s\S]*?```/gm, '')
                        .replace(/\{[\s]*"action"[\s\S]*?(\}|$)/gm, '').trim();
        
        if (!clean && text.includes('[ACTION]')) return '<i class="fa-solid fa-gear fa-spin me-1"></i> <em class="text-muted small">Synchronizing...</em>';
        const div = document.createElement('div');
        div.innerText = clean;
        return div.innerHTML.replace(/^[\s]*[\*\-\•][\s]+/gm, '• ').replace(/\n/g, '<br>');
    }

    function appendMessage(text, sender, isLoading = false) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `d-flex mb-3 ${sender === 'user' ? 'justify-content-end' : 'justify-content-start'}`;
        const bubble = document.createElement('div');
        bubble.className = `p-3 rounded-4 shadow-sm ${sender === 'user' ? 'bg-primary text-white ms-4' : 'bg-white text-dark me-4 border'}`;
        bubble.style.cssText = `border-radius: ${sender === 'user' ? '1rem 1rem 0 1rem' : '0 1rem 1rem 1rem'} !important; font-size: 0.95rem;`;
        
        if (isLoading) {
            const id = 'msg-' + Date.now();
            bubble.id = id;
            bubble.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-primary"></i>';
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

    function extractActionBlocks(text) {
        let blocks = [];
        
        // 1. Standard [ACTION] blocks
        const actionPattern = /\[ACTION\]([\s\S]*?)\[\/ACTION\]/g;
        let match;
        while ((match = actionPattern.exec(text)) !== null) blocks.push(match[1]);
        
        // 2. Markdown JSON blocks (forgive model if it forgets [ACTION])
        const markdownPattern = /```json\s*(\{[\s\S]*?"action"[\s\S]*?\})\s*```/g;
        while ((match = markdownPattern.exec(text)) !== null) {
            let duplicate = blocks.some(b => b.includes(match[1]));
            if (!duplicate) blocks.push(match[1]);
        }

        // 3. Unclosed [ACTION] blocks at the very end of generation
        const unclosedPattern = /\[ACTION\]([\s\S]*)$/;
        const unclosedMatch = unclosedPattern.exec(text);
        if (unclosedMatch && !text.includes('[/ACTION]', unclosedMatch.index)) {
            let possibleJson = unclosedMatch[1].trim();
            // Don't add if empty
            if (possibleJson) blocks.push(possibleJson);
        }
        
        return blocks;
    }

    async function handleAIAction(action) {
        logDebug("Action:", action);
        const actionMap = {
            'create_task': { url: '{{ route("tasks.store", [], false) }}', method: 'POST', data: { priority_type: 1 } },
            'create_habit': { url: '{{ route("habits.store", [], false) }}', method: 'POST', data: { repeat_type: 1 } },
            'create_goal': { url: '{{ route("lifeplan.goal.store", [], false) }}', method: 'POST' },
            'create_milestone': { url: '{{ route("lifeplan.milestone.store", [], false) }}', method: 'POST' },
            'create_journal': { url: '{{ route("journals.store", [], false) }}', method: 'POST' },
            'create_category': { url: '{{ route("lifeplan.category.store", [], false) }}', method: 'POST' },
            'delete_task': { url: `/tasks/${action.id}/destroy`, method: 'DELETE' },
            'delete_goal': { url: `/lifeplan/goal/${action.id}`, method: 'DELETE' }
        };
        const config = actionMap[action.action];
        if (!config) return false;
        
        // Auto-inject missing required fields for the updated schemas
        const todayStr = new Date().toISOString().split('T')[0];
        
        if (action.action === 'create_task') {
            if (action.date) {
                // Map AI's "date" keyword to our UI payload keys
                action.start_date_no_repeat = action.date;
                action.due_date = action.date;
            } else {
                action.start_date_no_repeat = todayStr;
                action.due_date = todayStr;
            }
        }
        
        if (action.action === 'create_habit') {
            if (!action.start_date) {
                action.start_date = todayStr;
            }
        }
        
        if (action.action === 'create_journal') {
            if (!action.entry_date) {
                action.entry_date = todayStr;
            }
        }

        const bodyData = { ...config.data, ...action };
        delete bodyData.action;

        try {
            const response = await fetch(config.url, {
                method: config.method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(bodyData)
            });
            const result = await response.json();
            if (result.success || response.ok) {
                showToast(result.message || "Command executed successfully", "success");
                return true;
            } else {
                let errorMsg = result.message || "Action failed";
                if (result.errors) {
                    const firstError = Object.values(result.errors)[0][0];
                    errorMsg = `Validation Error: ${firstError}`;
                }
                showToast(errorMsg, "danger");
                logDebug("Action fail server-side:", errorMsg);
                return false;
            }
        } catch (e) { console.error("Sync Error", e); return false; }
    }

    function showToast(message, type = "dark") {
        const toast = document.createElement('div');
        toast.className = `position-fixed bottom-0 start-50 translate-middle-x mb-5 bg-${type === 'success' ? 'success' : 'dark'} text-white px-4 py-2 rounded-pill shadow animate__animated animate__fadeInUp`;
        toast.style.zIndex = '9999';
        toast.innerHTML = message;
        document.body.appendChild(toast);
        setTimeout(() => { toast.remove(); }, 3000);
    }

    function logDebug(msg, data = null) {
        console.log(`[AI DEBUG] ${msg}`, data);
        const debugArea = document.getElementById('aiDebugLog');
        if (debugArea) {
            const entry = document.createElement('div');
            entry.textContent = `${msg} ${data ? JSON.stringify(data) : ''}`;
            debugArea.appendChild(entry);
            debugArea.scrollTop = debugArea.scrollHeight;
        }
    }

    if (window.location.search.includes('debugAI')) {
        const debugArea = document.getElementById('aiDebugLog');
        if (debugArea) debugArea.classList.remove('d-none');
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>
