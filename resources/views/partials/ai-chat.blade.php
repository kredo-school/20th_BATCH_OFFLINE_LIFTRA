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
            body: JSON.stringify({ prompt: prompt, model: 'gemma:2b' })
        })
        .then(async response => {
            if (!response.ok) {
                const errorJson = await response.json().catch(() => ({}));
                throw new Error(errorJson.message || `Server Error (${response.status})`);
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let assistantMessage = '';
            const loadingMsg = document.getElementById(loadingId);
            loadingMsg.innerHTML = ''; // Clear "Thinking..."

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                const chunk = decoder.decode(value, { stream: true });
                // Ollama returns multiple JSON objects in one stream
                const lines = chunk.split('\n').filter(line => line.trim());
                
                for (const line of lines) {
                    try {
                        const json = JSON.parse(line);
                        if (json.message && json.message.content) {
                            assistantMessage += json.message.content;
                            loadingMsg.innerHTML = formatAIResponse(assistantMessage);
                            scrollToBottom();
                        }
                    } catch (e) {
                        console.error("Error parsing JSON chunk:", e, line);
                    }
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
        // Regex to match our JSON action patterns
        const actionPattern = /\{[\s]*"action"[\s]*:[\s]*"[^"]+"[\s]*[:,][\s\S]*?\}[\s]*/g;
        
        // Remove JSON matches from the visible text
        let cleanText = text.replace(actionPattern, '').trim();

        // If only JSON was sent, provide a subtle confirmation instead of an empty bubble
        if (!cleanText && text.includes('"action"')) {
            return '<em class="text-muted small">Update complete.</em>';
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

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>
