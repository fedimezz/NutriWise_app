<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot NutriWise - Assistant nutrition</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --vert-profond: #1B4D1B;
            --vert-moyen: #2E7D32;
            --vert-principal: #4CAF50;
            --vert-clair: #81C784;
            --vert-pale: #C8E6C9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F5F9F2;
        }

        /* Chatbot Button */
        .chatbot-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: var(--vert-principal);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s;
            z-index: 1000;
            border: none;
            color: white;
            font-size: 1.8rem;
        }

        .chatbot-button:hover {
            transform: scale(1.1);
            background: var(--vert-moyen);
        }

        /* Chatbot Window */
        .chatbot-window {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 380px;
            height: 550px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 1001;
            animation: slideUp 0.3s ease;
        }

        .chatbot-window.open {
            display: flex;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Chatbot Header */
        .chatbot-header {
            background: var(--vert-profond);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chatbot-header h3 {
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chatbot-header button {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }

        /* Chat Messages */
        .chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #f9faf9;
        }

        .message {
            max-width: 85%;
            padding: 0.6rem 1rem;
            border-radius: 15px;
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .message.user {
            background: var(--vert-principal);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }

        .message.bot {
            background: white;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .message.bot strong {
            color: var(--vert-profond);
        }

        /* Typing indicator */
        .typing {
            background: white;
            padding: 0.6rem 1rem;
            border-radius: 15px;
            align-self: flex-start;
            display: none;
        }

        .typing span {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #999;
            border-radius: 50%;
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }

        .typing span:nth-child(2) { animation-delay: 0.2s; }
        .typing span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
            30% { transform: translateY(-10px); opacity: 1; }
        }

        /* Chat Input */
        .chatbot-input {
            padding: 1rem;
            border-top: 1px solid var(--vert-pale);
            display: flex;
            gap: 10px;
            background: white;
        }

        .chatbot-input input {
            flex: 1;
            padding: 0.6rem;
            border: 1px solid var(--vert-pale);
            border-radius: 25px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
        }

        .chatbot-input input:focus {
            outline: none;
            border-color: var(--vert-principal);
        }

        .chatbot-input button {
            background: var(--vert-principal);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
        }

        .chatbot-input button:hover {
            background: var(--vert-moyen);
        }

        /* Suggestions */
        .suggestions {
            padding: 0.5rem 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            border-top: 1px solid var(--vert-pale);
            background: white;
        }

        .suggestion-chip {
            background: var(--vert-pale);
            color: var(--vert-profond);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .suggestion-chip:hover {
            background: var(--vert-clair);
        }

        /* Responsive */
        @media (max-width: 500px) {
            .chatbot-window {
                width: calc(100vw - 40px);
                right: 20px;
                bottom: 80px;
            }
        }
    </style>
</head>
<body>

<!-- Chatbot Button -->
<button class="chatbot-button" id="chatbotToggle">
    <i class="fas fa-robot"></i>
</button>

<!-- Chatbot Window -->
<div class="chatbot-window" id="chatbotWindow">
    <div class="chatbot-header">
        <h3>
            <i class="fas fa-leaf"></i>
            NutriWise Assistant
        </h3>
        <button id="chatbotClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="chatbot-messages" id="chatMessages">
        <div class="message bot">
            👋 Bonjour ! Je suis l'assistant NutriWise.<br>
            Je peux vous aider à trouver des <strong>recettes</strong> et des informations sur les <strong>aliments</strong>.<br><br>
            Par exemple :<br>
            • "Donne-moi une recette de petit-déjeuner"<br>
            • "Combien de calories dans une banane ?"<br>
            • "Recette végétarienne facile"
        </div>
    </div>

    <div class="typing" id="typingIndicator">
        <span></span><span></span><span></span>
    </div>

    <div class="suggestions">
        <span class="suggestion-chip" data-question="Recette petit-déjeuner">🍳 Recette petit-déjeuner</span>
        <span class="suggestion-chip" data-question="Recette végétarienne">🥗 Recette végétarienne</span>
        <span class="suggestion-chip" data-question="Calories avocat">🥑 Calories avocat</span>
        <span class="suggestion-chip" data-question="Recette rapide">⚡ Recette rapide</span>
    </div>

    <div class="chatbot-input">
        <input type="text" id="chatInput" placeholder="Posez votre question..." autocomplete="off">
        <button id="chatSend">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
    const chatbotToggle = document.getElementById('chatbotToggle');
    const chatbotWindow = document.getElementById('chatbotWindow');
    const chatbotClose = document.getElementById('chatbotClose');
    const chatInput = document.getElementById('chatInput');
    const chatSend = document.getElementById('chatSend');
    const chatMessages = document.getElementById('chatMessages');
    const typingIndicator = document.getElementById('typingIndicator');

    // Open/Close Chatbot
    chatbotToggle.addEventListener('click', () => {
        chatbotWindow.classList.toggle('open');
    });

    chatbotClose.addEventListener('click', () => {
        chatbotWindow.classList.remove('open');
    });

    // Send message
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        // Add user message
        addMessage(message, 'user');
        chatInput.value = '';

        // Show typing indicator
        typingIndicator.style.display = 'flex';
        scrollToBottom();

        // Send to backend
        fetch('index.php?page=chatbot_api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            typingIndicator.style.display = 'none';
            addMessage(data.response, 'bot');
            scrollToBottom();
        })
        .catch(error => {
            typingIndicator.style.display = 'none';
            addMessage("❌ Désolé, une erreur s'est produite. Veuillez réessayer.", 'bot');
            scrollToBottom();
        });
    }

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        messageDiv.innerHTML = text.replace(/\n/g, '<br>');
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    chatSend.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    // Suggestions
    document.querySelectorAll('.suggestion-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            chatInput.value = chip.dataset.question;
            sendMessage();
        });
    });
</script>

</body>
</html>