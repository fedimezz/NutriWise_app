<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot NutriWise</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>

        :root {
            --vert-profond: #1B4D1B;
            --vert-moyen: #2E7D32;
            --vert-principal: #4CAF50;
            --vert-clair: #81C784;
            --vert-pale: #C8E6C9;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Inter',sans-serif;
            background:#F5F9F2;
        }

        /*
        |--------------------------------------------------------------------------
        | BUTTON
        |--------------------------------------------------------------------------
        */

        .chatbot-button{

            position:fixed;

            bottom:30px;
            right:30px;

            width:60px;
            height:60px;

            border:none;

            border-radius:50%;

            background:var(--vert-principal);

            color:white;

            font-size:1.8rem;

            cursor:pointer;

            display:flex;
            align-items:center;
            justify-content:center;

            box-shadow:0 4px 15px rgba(0,0,0,.2);

            z-index:1000;

            transition:.3s;
        }

        .chatbot-button:hover{

            transform:scale(1.1);

            background:var(--vert-moyen);
        }

        /*
        |--------------------------------------------------------------------------
        | WINDOW
        |--------------------------------------------------------------------------
        */

        .chatbot-window{

            position:fixed;

            bottom:100px;
            right:30px;

            width:380px;
            height:550px;

            background:white;

            border-radius:20px;

            overflow:hidden;

            display:none;
            flex-direction:column;

            box-shadow:0 10px 40px rgba(0,0,0,.2);

            z-index:1001;
        }

        .chatbot-window.open{
            display:flex;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .chatbot-header{

            background:var(--vert-profond);

            color:white;

            padding:1rem;

            display:flex;
            align-items:center;
            justify-content:space-between;
        }

        .chatbot-header h3{

            display:flex;
            align-items:center;
            gap:8px;

            font-size:1rem;
        }

        .chatbot-header button{

            background:none;
            border:none;

            color:white;

            font-size:1.2rem;

            cursor:pointer;
        }

        /*
        |--------------------------------------------------------------------------
        | MESSAGES
        |--------------------------------------------------------------------------
        */

        .chatbot-messages{

            flex:1;

            overflow-y:auto;

            padding:1rem;

            display:flex;
            flex-direction:column;

            gap:10px;

            background:#f9faf9;
        }

        .message{

            max-width:85%;

            padding:.7rem 1rem;

            border-radius:15px;

            line-height:1.5;

            font-size:.85rem;
        }

        .message.user{

            align-self:flex-end;

            background:var(--vert-principal);

            color:white;

            border-bottom-right-radius:5px;
        }

        .message.bot{

            align-self:flex-start;

            background:white;

            color:#333;

            border-bottom-left-radius:5px;

            box-shadow:0 1px 2px rgba(0,0,0,.1);
        }

        /*
        |--------------------------------------------------------------------------
        | TYPING
        |--------------------------------------------------------------------------
        */

        .typing{

            display:none;

            padding:.7rem 1rem;

            margin-left:1rem;

            background:white;

            border-radius:15px;

            width:fit-content;
        }

        .typing span{

            width:8px;
            height:8px;

            border-radius:50%;

            background:#999;

            display:inline-block;

            margin:0 2px;

            animation:typing 1.2s infinite;
        }

        .typing span:nth-child(2){
            animation-delay:.2s;
        }

        .typing span:nth-child(3){
            animation-delay:.4s;
        }

        @keyframes typing{

            0%,100%{
                opacity:.3;
                transform:translateY(0);
            }

            50%{
                opacity:1;
                transform:translateY(-5px);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INPUT
        |--------------------------------------------------------------------------
        */

        .chatbot-input{

            padding:1rem;

            display:flex;

            gap:10px;

            border-top:1px solid var(--vert-pale);

            background:white;
        }

        .chatbot-input input{

            flex:1;

            border:1px solid var(--vert-pale);

            border-radius:25px;

            padding:.7rem 1rem;

            font-size:.85rem;

            font-family:'Inter',sans-serif;
        }

        .chatbot-input input:focus{

            outline:none;

            border-color:var(--vert-principal);
        }

        .chatbot-input button{

            width:42px;
            height:42px;

            border:none;

            border-radius:50%;

            background:var(--vert-principal);

            color:white;

            cursor:pointer;
        }

        /*
        |--------------------------------------------------------------------------
        | SUGGESTIONS
        |--------------------------------------------------------------------------
        */

        .suggestions{

            padding:.7rem 1rem;

            display:flex;
            flex-wrap:wrap;

            gap:8px;

            background:white;

            border-top:1px solid var(--vert-pale);
        }

        .suggestion-chip{

            background:var(--vert-pale);

            color:var(--vert-profond);

            padding:.4rem .8rem;

            border-radius:20px;

            font-size:.7rem;

            cursor:pointer;

            transition:.2s;
        }

        .suggestion-chip:hover{

            background:var(--vert-clair);
        }

        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media(max-width:500px){

            .chatbot-window{

                width:calc(100vw - 40px);

                right:20px;
            }
        }

    </style>
</head>
<body>

<!-- BUTTON -->

<button class="chatbot-button" id="chatbotToggle">

    <i class="fas fa-robot"></i>

</button>

<!-- WINDOW -->

<div class="chatbot-window" id="chatbotWindow">

    <!-- HEADER -->

    <div class="chatbot-header">

        <h3>

            <i class="fas fa-leaf"></i>

            NutriWise Assistant

        </h3>

        <button id="chatbotClose">

            <i class="fas fa-times"></i>

        </button>

    </div>

    <!-- MESSAGES -->

    <div class="chatbot-messages" id="chatMessages">

        <div class="message bot">

            👋 Bonjour !

            <br><br>

            Je suis l'assistant NutriWise 🥗

            <br><br>

            Exemples :

            <br>

            • Recette rapide

            <br>

            • Calories banane

            <br>

            • Recette végétarienne

        </div>

    </div>

    <!-- TYPING -->

    <div class="typing" id="typingIndicator">

        <span></span>
        <span></span>
        <span></span>

    </div>

    <!-- SUGGESTIONS -->

    <div class="suggestions">

        <span class="suggestion-chip"
              data-question="Recette rapide">

            ⚡ Recette rapide

        </span>

        <span class="suggestion-chip"
              data-question="Recette végétarienne">

            🥗 Recette végétarienne

        </span>

        <span class="suggestion-chip"
              data-question="Calories banane">

            🍌 Calories banane

        </span>

        <span class="suggestion-chip"
              data-question="Recette petit-déjeuner">

            🍳 Petit-déjeuner

        </span>

    </div>

    <!-- INPUT -->

    <div class="chatbot-input">

        <input type="text"
               id="chatInput"
               placeholder="Posez votre question...">

        <button id="chatSend">

            <i class="fas fa-paper-plane"></i>

        </button>

    </div>

</div>

<script>

const chatbotToggle =
    document.getElementById('chatbotToggle');

const chatbotWindow =
    document.getElementById('chatbotWindow');

const chatbotClose =
    document.getElementById('chatbotClose');

const chatInput =
    document.getElementById('chatInput');

const chatSend =
    document.getElementById('chatSend');

const chatMessages =
    document.getElementById('chatMessages');

const typingIndicator =
    document.getElementById('typingIndicator');

/*
|--------------------------------------------------------------------------
| OPEN / CLOSE
|--------------------------------------------------------------------------
*/

chatbotToggle.addEventListener('click', () => {

    chatbotWindow.classList.toggle('open');
});

chatbotClose.addEventListener('click', () => {

    chatbotWindow.classList.remove('open');
});

/*
|--------------------------------------------------------------------------
| SEND MESSAGE
|--------------------------------------------------------------------------
*/

async function sendMessage(){

    const message =
        chatInput.value.trim();

    if(!message) return;

    addMessage(message,'user');

    chatInput.value = '';

    typingIndicator.style.display = 'block';

    scrollToBottom();

    try{

        const response =
            await fetch(
                'index.php?page=chatbot_api',
                {
                    method:'POST',

                    headers:{
                        'Content-Type':'application/json'
                    },

                    body:JSON.stringify({
                        message:message
                    })
                }
            );

        const text =
            await response.text();

        console.log(text);

        let data;

        try{

            data = JSON.parse(text);

        }catch(e){

            throw new Error(text);
        }

        typingIndicator.style.display = 'none';

        addMessage(
            data.response,
            'bot'
        );

    }catch(error){

        console.error(error);

        typingIndicator.style.display = 'none';

        addMessage(
            '❌ Erreur serveur chatbot.',
            'bot'
        );
    }

    scrollToBottom();
}

/*
|--------------------------------------------------------------------------
| ADD MESSAGE
|--------------------------------------------------------------------------
*/

function addMessage(text,sender){

    const messageDiv =
        document.createElement('div');

    messageDiv.className =
        `message ${sender}`;

    messageDiv.innerHTML =
        text.replace(/\n/g,'<br>');

    chatMessages.appendChild(messageDiv);

    scrollToBottom();
}

/*
|--------------------------------------------------------------------------
| SCROLL
|--------------------------------------------------------------------------
*/

function scrollToBottom(){

    chatMessages.scrollTop =
        chatMessages.scrollHeight;
}

/*
|--------------------------------------------------------------------------
| EVENTS
|--------------------------------------------------------------------------
*/

chatSend.addEventListener(
    'click',
    sendMessage
);

chatInput.addEventListener(
    'keypress',
    (e)=>{

        if(e.key === 'Enter'){

            sendMessage();
        }
    }
);

/*
|--------------------------------------------------------------------------
| SUGGESTIONS
|--------------------------------------------------------------------------
*/

document
.querySelectorAll('.suggestion-chip')
.forEach(chip => {

    chip.addEventListener('click',()=>{

        chatInput.value =
            chip.dataset.question;

        sendMessage();
    });
});

</script>

</body>
</html>