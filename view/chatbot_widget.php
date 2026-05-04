<!-- NutriBot IA - Widget Chatbot -->
<style>
.chatbot-toggle { position:fixed; bottom:30px; right:30px; width:60px; height:60px; border-radius:50%; background:#2e7d32; color:#fff; border:none; cursor:pointer; font-size:28px; box-shadow:0 4px 15px rgba(0,0,0,0.3); z-index:9999; transition:transform 0.3s; }
.chatbot-toggle:hover { transform:scale(1.1); }
.chatbot-window { position:fixed; bottom:100px; right:30px; width:380px; max-height:500px; background:#fff; border-radius:16px; box-shadow:0 8px 30px rgba(0,0,0,0.2); z-index:9999; display:none; flex-direction:column; overflow:hidden; }
.chatbot-window.open { display:flex; }
.chatbot-header { background:linear-gradient(135deg,#2e7d32,#1b5e20); color:#fff; padding:15px; display:flex; align-items:center; gap:10px; }
.chatbot-header img, .chatbot-header .bot-avatar { width:40px; height:40px; border-radius:50%; background:#4CAF50; display:flex; align-items:center; justify-content:center; font-size:20px; }
.chatbot-header .bot-info h4 { margin:0; font-size:15px; }
.chatbot-header .bot-info small { opacity:0.8; font-size:11px; }
.chatbot-header .close-btn { margin-left:auto; background:none; border:none; color:#fff; font-size:22px; cursor:pointer; }
.chatbot-messages { flex:1; overflow-y:auto; padding:15px; max-height:280px; min-height:200px; }
.chat-msg { margin-bottom:12px; max-width:85%; }
.chat-msg.bot { margin-right:auto; }
.chat-msg.user { margin-left:auto; }
.chat-msg .bubble { padding:10px 14px; border-radius:12px; font-size:13px; line-height:1.5; }
.chat-msg.bot .bubble { background:#e8f5e9; color:#1b5e20; border-bottom-left-radius:4px; }
.chat-msg.user .bubble { background:#2e7d32; color:#fff; border-bottom-right-radius:4px; }
.chat-suggestions { padding:8px 15px; display:flex; flex-wrap:wrap; gap:6px; }
.chat-suggestions button { background:#e8f5e9; border:1px solid #a5d6a7; color:#2e7d32; padding:5px 10px; border-radius:20px; font-size:11px; cursor:pointer; transition:all 0.2s; }
.chat-suggestions button:hover { background:#2e7d32; color:#fff; }
.chatbot-input { display:flex; padding:10px; border-top:1px solid #e0e0e0; gap:8px; }
.chatbot-input input { flex:1; border:1px solid #ccc; border-radius:25px; padding:8px 15px; font-size:13px; outline:none; }
.chatbot-input input:focus { border-color:#2e7d32; }
.chatbot-input button { width:40px; height:40px; border-radius:50%; background:#2e7d32; color:#fff; border:none; cursor:pointer; font-size:16px; }
.typing-dots { display:inline-flex; gap:4px; padding:10px 14px; }
.typing-dots span { width:8px; height:8px; background:#a5d6a7; border-radius:50%; animation:typing 1.4s infinite; }
.typing-dots span:nth-child(2) { animation-delay:0.2s; }
.typing-dots span:nth-child(3) { animation-delay:0.4s; }
@keyframes typing { 0%,100%{opacity:0.3;transform:scale(0.8)} 50%{opacity:1;transform:scale(1.2)} }
</style>

<button class="chatbot-toggle" id="chatbot-toggle" title="NutriBot IA">💬</button>

<div class="chatbot-window" id="chatbot-window">
    <div class="chatbot-header">
        <div class="bot-avatar">🤖</div>
        <div class="bot-info">
            <h4>NutriBot IA</h4>
            <small>Assistant nutrition intelligent (Gemini AI)</small>
        </div>
        <button class="close-btn" id="chatbot-close">✕</button>
    </div>
    <div class="chatbot-messages" id="chatbot-messages">
        <div class="chat-msg bot">
            <div class="bubble">Bonjour ! 👋 Je suis <b>NutriBot</b>, votre assistant nutrition propulsé par l'<b>intelligence artificielle</b>. Posez-moi n'importe quelle question sur l'alimentation et la nutrition, je vous répondrai de manière personnalisée ! 🍎 🌿</div>
        </div>
    </div>
    <div class="chat-suggestions" id="chatbot-suggestions"></div>
    <div class="chatbot-input">
        <input type="text" id="chatbot-input" placeholder="Posez votre question..." autocomplete="off">
        <button id="chatbot-send">➤</button>
    </div>
</div>

<script>
(function() {
    function resolveChatbotEndpoint() {
        const path = window.location.pathname;
        let basePath = '';

        if (path.indexOf('/controller/') !== -1) {
            basePath = path.split('/controller/')[0];
        } else if (path.indexOf('/view/') !== -1) {
            basePath = path.split('/view/')[0];
        } else {
            basePath = path.replace(/\/+$/, '');
        }

        if (!basePath) {
            return window.location.origin + '/controller/index.php?controller=chatbot';
        }

        return window.location.origin + basePath + '/controller/index.php?controller=chatbot';
    }

    const chatbotEndpoint = resolveChatbotEndpoint();
    const conversationHistory = [];
    const maxHistoryItems = 8;

    const allSuggestions = [
        'Aliments riches en fer ?', 'Comment manger équilibré ?', 'Bienfaits du jeûne intermittent ?',
        'Je suis végétarien, que manger ?', 'Aliments anti-stress ?', 'Petit-déjeuner idéal ?',
        'Quels aliments pour mieux dormir ?', 'Comment booster mon immunité ?',
        'Menu semaine équilibré ?', 'Aliments pour la peau ?', 'Quels sont les dangers du sucre ?',
        'Comment lire une étiquette nutritionnelle ?'
    ];

    function showRandomSuggestions() {
        const container = document.getElementById('chatbot-suggestions');
        const shuffled = allSuggestions.sort(() => 0.5 - Math.random()).slice(0, 3);
        container.innerHTML = shuffled.map(s => '<button onclick="sendChatMessage(\'' + s.replace(/'/g, "\\'") + '\')">' + s + '</button>').join('');
    }

    function pushHistory(role, content) {
        conversationHistory.push({ role: role, content: content });
        if (conversationHistory.length > maxHistoryItems) {
            conversationHistory.splice(0, conversationHistory.length - maxHistoryItems);
        }
    }

    window.sendChatMessage = async function(msg) {
        const messagesDiv = document.getElementById('chatbot-messages');
        
        // Message utilisateur
        messagesDiv.innerHTML += '<div class="chat-msg user"><div class="bubble">' + msg.replace(/</g,'&lt;') + '</div></div>';
        pushHistory('user', msg);
        
        // Animation de frappe
        const typingId = 'typing-' + Date.now();
        messagesDiv.innerHTML += '<div class="chat-msg bot" id="' + typingId + '"><div class="typing-dots"><span></span><span></span><span></span></div></div>';
        messagesDiv.scrollTop = messagesDiv.scrollHeight;

        try {
            const response = await fetch(chatbotEndpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: msg, history: conversationHistory })
            });

            const raw = await response.text();
            let data;
            try {
                data = JSON.parse(raw);
            } catch (parseError) {
                data = { response: '⚠️ Réponse serveur invalide. Vérifiez la configuration PHP/Apache.' };
            }

            document.getElementById(typingId).remove();
            messagesDiv.innerHTML += '<div class="chat-msg bot"><div class="bubble">' + data.response + '</div></div>';
            pushHistory('assistant', (data && data.response) ? String(data.response).replace(/<[^>]*>/g, ' ') : '');
        } catch(e) {
            document.getElementById(typingId).remove();
            messagesDiv.innerHTML += '<div class="chat-msg bot"><div class="bubble">⚠️ Erreur de connexion. Réessayez.</div></div>';
        }
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        showRandomSuggestions();
    };

    document.getElementById('chatbot-toggle').addEventListener('click', function() {
        document.getElementById('chatbot-window').classList.toggle('open');
    });
    document.getElementById('chatbot-close').addEventListener('click', function() {
        document.getElementById('chatbot-window').classList.remove('open');
    });
    document.getElementById('chatbot-send').addEventListener('click', function() {
        const input = document.getElementById('chatbot-input');
        if(input.value.trim()) { sendChatMessage(input.value.trim()); input.value = ''; }
    });
    document.getElementById('chatbot-input').addEventListener('keypress', function(e) {
        if(e.key === 'Enter' && this.value.trim()) { sendChatMessage(this.value.trim()); this.value = ''; }
    });

    showRandomSuggestions();
})();
</script>
