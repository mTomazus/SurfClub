<!--
  MOLAS SURF CLUB - AI CHAT WIDGET
  
  HOW TO ADD TO YOUR SITE:
  ========================
  Option A: Include this file as a Trongate partial in your template:
    Add to templates/views/public.php (before </body>):
      <?= Template::partial('partials/chat_widget') ?>
    Then save this file as: templates/views/partials/chat_widget.php
  
  Option B: Copy everything below into the bottom of your template file.
  
  IMPORTANT: You need a small backend proxy to protect your API key.
  See the PHP proxy file (chat_proxy.php) included with this widget.
-->

<div id="molas-chat-root"></div>

<style>
  /* ─── CHAT WIDGET STYLES ─── */
  /* #molas-chat-root * { box-sizing: border-box; margin: 0; } */
  
  .mc-bubble {
    position: fixed; bottom: 24px; right: 24px; width: 64px; height: 64px;
    border-radius: 50%; border: none; cursor: pointer; z-index: 9999;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    background:transparent;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1);
    animation: mc-bubbleIn 0.5s cubic-bezier(0.34,1.56,0.64,1);
  }
  .mc-bubble:hover { transform: scale(1.1); background:transparent;}

  .mc-window {
    position: fixed; bottom: 24px; right: 24px;
    width: min(400px, calc(100vw - 32px));
    height: min(580px, calc(100vh - 48px));
    border-radius: 20px; overflow: hidden; z-index: 9999;
    box-shadow: 0 12px 48px rgba(0,0,0,0.25), 0 2px 8px rgba(0,0,0,0.1);
    display: none; flex-direction: column;
    background: #f0f4f8;
    animation: mc-chatOpen 0.35s cubic-bezier(0.34,1.56,0.64,1);
  }
  .mc-window.mc-open { display: flex;box-shadow: 0 0 5px black; }

  /* Header */
  .mc-header {
    background: linear-gradient(135deg, #2f78a8 0%, #114161 100%);
    padding: 0.5rem 1rem; display: flex; align-items: center;
    justify-content: space-between; flex-shrink: 0;
  }
  .mc-header-left { display: flex; align-items: center; gap: 12px; }
  .mc-avatar {
    width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center; font-size: 22px;
    & img { border-radius:50%;}
  }
  .mc-header-title { color: white; font-weight: 700; font-size: 15px; letter-spacing: 0.02em; }
  .mc-header-status { color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 1px; }
  .mc-status-dot {
    display: inline-block; width: 7px; height: 7px; background: #4ade80;
    border-radius: 50%; margin-right: 5px; vertical-align: middle;
  }
  .mc-close-btn {
    background: rgba(255,255,255,0.15); border: none; color: white;
    width: 32px; height: 32px; border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; margin:0;padding:0;transition: background 0.2s;
  }
  .mc-close-btn:hover { background: rgba(255,255,255,0.3); }

  /* Messages */
  .mc-body {
    flex: 1; overflow-y: auto; padding: 16px 16px 8px;
    display: flex; flex-direction: column; gap: 10px;
    background: linear-gradient(180deg, #e8f0f8 0%, #f5f7fa 100%);
  }
  .mc-msg-row { display: flex; animation: mc-msgIn 0.3s ease-out; }
  .mc-msg-row.mc-user { justify-content: flex-end; }
  .mc-msg-row.mc-bot { justify-content: flex-start; }
  
  .mc-bot-icon {
    width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg, #2f78a8, #114161);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0; margin-right: 8px; margin-top: 2px;
  }
  .mc-msg {
    max-width: 78%; padding: 10px 14px; font-size: 14px;
    line-height: 1.55; white-space: pre-wrap; word-break: break-word;
  }
  .mc-msg.mc-msg-user {
    border-radius: 16px 16px 4px 16px;
    background: linear-gradient(135deg, #2f78a8 0%, #1a5c8a 100%);
    color: white; box-shadow: 0 2px 8px rgba(47,120,168,0.25);
  }
  .mc-msg.mc-msg-bot {
    border-radius: 16px 16px 16px 4px;
    background: white; color: #1a2a3a;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  }

  /* Loading dots */
  .mc-loading { display: flex; gap: 5px; padding: 12px 18px; background: white;
    border-radius: 16px 16px 16px 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
  .mc-dot { width: 8px; height: 8px; border-radius: 50%; background: #2f78a8; opacity: 0.4; }
  .mc-dot:nth-child(1) { animation: mc-dotPulse 1.2s infinite 0s; }
  .mc-dot:nth-child(2) { animation: mc-dotPulse 1.2s infinite 0.2s; }
  .mc-dot:nth-child(3) { animation: mc-dotPulse 1.2s infinite 0.4s; }

  /* Quick questions */
  .mc-quick-wrap { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
  .mc-quick-btn {
    background: white; border: 1.5px solid #c8dcea; border-radius: 20px;
    padding: 7px 14px; font-size: 12.5px; color: #2f78a8; cursor: pointer;
    font-weight: 600; transition: all 0.2s; white-space: nowrap; font-family: inherit;
  }
  .mc-quick-btn:hover { background: #2f78a8; color: white; border-color: #2f78a8; }

  /* Input area */
  .mc-input-area {
    padding: 12px 16px; border-top: 1px solid #e2e8f0; background: white;
    display: flex; gap: 8px; align-items: flex-end; flex-shrink: 0;
  }
  .mc-input {
    flex: 1; border: 1.5px solid #d0dbe6; border-radius: 16px;
    padding: 10px 14px; font-size: 14px; resize: none; outline: none;
    font-family: inherit; line-height: 1.4; max-height: 100px;
    overflow-y: auto; background: #f8fafc; transition: border-color 0.2s;
  }
  .mc-input:focus { border-color: #2f78a8; }
  .mc-input::placeholder { color: #94a3b8; }
  .mc-send-btn {
    width: 40px; height: 40px; border-radius: 50%; border: none;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: all 0.2s;
  }
  .mc-send-btn.mc-active {
    background: linear-gradient(135deg, #2f78a8, #114161); transform: scale(1);
  }
  .mc-send-btn.mc-inactive {
    background: #cbd5e1; cursor: default; transform: scale(0.95);
  }

  /* Footer */
  .mc-footer {
    text-align: center; padding: 6px 0; font-size: 10.5px; color: #94a3b8;
    background: white; border-top: 1px solid #f1f5f9; flex-shrink: 0;
  }

  /* Animations */
  @keyframes mc-bubbleIn { from { transform: scale(0) rotate(-180deg); opacity: 0; } to { transform: scale(1) rotate(0); opacity: 1; } }
  @keyframes mc-chatOpen { from { transform: scale(0.5) translateY(40px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }
  @keyframes mc-msgIn { from { transform: translateY(8px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
  @keyframes mc-dotPulse { 0%,60%,100% { transform: scale(1); opacity: 0.4; } 30% { transform: scale(1.3); opacity: 1; } }
</style>

<script>
(function() {
  'use strict';

  // ═══════════════════════════════════════════════════
  // CONFIGURATION - Edit these values for your setup
  // ═══════════════════════════════════════════════════
  
  // Option 1: Use your own backend proxy (RECOMMENDED for production)
  // Create a PHP file on your server that forwards requests to the Claude API
  // const API_URL = '/chat_proxy.php';
  // const USE_PROXY = true;
  
  // Option 2: Direct API call (for testing only - exposes API key!)
  // To this (if using Trongate module):
  const API_URL = '<?= BASE_URL ?>chats/proxy';
  const USE_PROXY = true;
  
  // ═══════════════════════════════════════════════════

  const GREETING = 'Sveiki! 🏄 Aš esu Molas Surf Club asistentas. Kuo galiu padėti? Klauskite apie pamokas, stovyklą, nuomą ar renginius!';
  
  const QUICK_QUESTIONS = [
    'Kiek kainuoja pamokos?',
    'Lesson prices?',
    'Papasakok apie stovyklą',
    'Įrangos nuoma'
  ];

  const SYSTEM_PROMPT = `Tu esi Molas Surf Club virtualus asistentas. Tu padedi lankytojams sužinoti apie banglenčių pamokas, įrangos nuomą, vaikų vasaros stovyklą ir renginius Klaipėdoje, Melnragėje.

SVARBI INFORMACIJA:

PAMOKOS:
- Pamokų paketas: 150€ (dvi 1h pamokos)
- Privati pamoka: 85€ (1.5h trukmė)
- Grupinė pamoka: 40€
- Pamoka dviem: 120€
- Individuali Plus: 100€ (1.5h pamoka + 30 min asmeninė konsultacija)
- Komandos formavimas: nuo 200€ (6-12 žmonių)
- Dovanų kuponai: nuo 40€ (el pastu arba popieriniai kuponai)

ĮRANGOS NUOMA:
- Banglentė: nuo 15€/2h, 40€/diena
- Irklentė (SUP): 15€/1h, 40€/diena
- Hidrokostiumas: 10€/2h, 20€/diena
- Riedlentė: 15€/2h, 30€/diena
- Skim boardas: 10€/2h, 20€/diena
- Puslentė: 10€/2h, 25€/diena

STOVYKLA:
- Kaina: 300€ (įskaitant pamokas, įrangos nuomą, maitinimą ir renginius. kelios pamainos yra pigesnės)
- Nuo birželio vidurio
- 8-10 moksleivių grupės
- 5 dienos, 9:00-17:00
- Registracija: forma + 100€ avansas
- Avansas negrąžinamas

KONTAKTAI:
- VšĮ Banglentė, Vėtros g. 8, Klaipėda
- Tel: +370 686 02356
- El. paštas: info@surfclub.lt
- www.surfclub.lt

TAISYKLĖS:
- Atsakyk draugiškai ir trumpai
- Atsakyk ta kalba, kuria klausia
- Jei nežinai - siūlyk susisiekti tiesiogiai
- Būk entuziastingas dėl banglenčių sporto!`;

  // State
  let isOpen = false;
  let messages = [{ role: 'assistant', content: GREETING }];
  let isLoading = false;

  // ─── DOM CREATION ───
  const root = document.getElementById('molas-chat-root');

  function render() {
    root.innerHTML = '';
    
    if (!isOpen) {
      // Render bubble
      const bubble = document.createElement('button');
      bubble.className = 'mc-bubble';
      bubble.setAttribute('aria-label', 'Atidaryti pokalbį');
      bubble.innerHTML = `<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
        <path d="M12 2C6.48 2 2 5.58 2 10c0 2.24 1.12 4.26 2.94 5.66L4 22l4.73-2.73C9.77 19.73 10.86 20 12 20c5.52 0 10-3.58 10-8s-4.48-8-10-8z" fill="white"/>
        <circle cx="8" cy="10" r="1.2" fill="#2f78a8"/><circle cx="12" cy="10" r="1.2" fill="#2f78a8"/><circle cx="16" cy="10" r="1.2" fill="#2f78a8"/>
      </svg>`;
      bubble.onclick = () => { isOpen = true; render(); };
      root.appendChild(bubble);
      
      // Pulse effect
      setInterval(() => {
        bubble.classList.add('mc-pulse');
        setTimeout(() => bubble.classList.remove('mc-pulse'), 1500);
      }, 8000);
      return;
    }

    // Render chat window
    const win = document.createElement('div');
    win.className = 'mc-window mc-open';

    // Header
    win.innerHTML = `
      <div class="mc-header">
        <div class="mc-header-left">
          <div class="mc-avatar"><img src="images/seal.jpeg" alt="Molas Surf Club"></div>
          <div>
            <div class="mc-header-title">Molas Surf Club</div>
            <div class="mc-header-status"><span class="mc-status-dot"></span>Prisijungęs</div>
          </div>
        </div>
        <button class="mc-close-btn" aria-label="Uždaryti">✕</button>
      </div>
      <div class="mc-body" id="mc-body">
        <svg viewBox="0 0 400 30" style="width:100%;opacity:0.08;margin-bottom:4px;flex-shrink:0" preserveAspectRatio="none">
          <path d="M0,15 C100,30 200,0 300,15 S400,30 400,15 L400,30 L0,30 Z" fill="#2f78a8"/>
        </svg>
      </div>
      <div class="mc-input-area">
        <textarea class="mc-input" id="mc-input" placeholder="Rašykite žinutę..." rows="1"></textarea>
        <button class="mc-send-btn mc-inactive" id="mc-send" aria-label="Siųsti">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" fill="white"/></svg>
        </button>
      </div>
      <div class="mc-footer">Molas Surf Club • Klaipėda 🌊</div>
    `;

    root.appendChild(win);

    // Wire up close
    win.querySelector('.mc-close-btn').onclick = () => { isOpen = false; render(); };

    // Render messages
    const body = document.getElementById('mc-body');
    messages.forEach((msg, i) => {
      body.appendChild(createMsgEl(msg));
    });

    // Loading indicator
    if (isLoading) {
      const loadRow = document.createElement('div');
      loadRow.className = 'mc-msg-row mc-bot';
      loadRow.innerHTML = `<div class="mc-bot-icon">🌊</div><div class="mc-loading"><div class="mc-dot"></div><div class="mc-dot"></div><div class="mc-dot"></div></div>`;
      body.appendChild(loadRow);
    }

    // Quick questions (only at start)
    if (messages.length === 1 && !isLoading) {
      const qWrap = document.createElement('div');
      qWrap.className = 'mc-quick-wrap';
      QUICK_QUESTIONS.forEach(q => {
        const btn = document.createElement('button');
        btn.className = 'mc-quick-btn';
        btn.textContent = q;
        btn.onclick = () => sendMessage(q);
        qWrap.appendChild(btn);
      });
      body.appendChild(qWrap);
    }

    // Scroll to bottom
    body.scrollTop = body.scrollHeight;

    // Input handling
    const input = document.getElementById('mc-input');
    const sendBtn = document.getElementById('mc-send');
    
    input.focus();
    
    input.oninput = () => {
      sendBtn.className = input.value.trim() ? 'mc-send-btn mc-active' : 'mc-send-btn mc-inactive';
    };
    
    input.onkeydown = (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        if (input.value.trim()) sendMessage(input.value.trim());
      }
    };
    
    sendBtn.onclick = () => {
      if (input.value.trim()) sendMessage(input.value.trim());
    };
  }

  function createMsgEl(msg) {
    const row = document.createElement('div');
    row.className = 'mc-msg-row ' + (msg.role === 'user' ? 'mc-user' : 'mc-bot');
    
    if (msg.role === 'assistant') {
      row.innerHTML = `<div class="mc-bot-icon">🌊</div><div class="mc-msg mc-msg-bot">${escapeHtml(msg.content)}</div>`;
    } else {
      row.innerHTML = `<div class="mc-msg mc-msg-user">${escapeHtml(msg.content)}</div>`;
    }
    return row;
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  async function sendMessage(text) {
    if (isLoading) return;
    
    messages.push({ role: 'user', content: text });
    isLoading = true;
    render();

    try {
      const convMessages = messages.slice(1).map(m => ({ role: m.role, content: m.content }));
      
      const requestBody = {
        model: 'claude-sonnet-4-20250514',
        max_tokens: 1000,
        system: SYSTEM_PROMPT,
        messages: convMessages
      };

      const response = await fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(requestBody)
      });

      const data = await response.json();

      if (!response.ok) {
        console.error('Chat proxy error:', response.status, data);
      }

      const reply = data.content?.filter(b => b.type === 'text').map(b => b.text).join('')
        || 'Atsiprašau, kažkas nepavyko. Pabandykite dar kartą!';

      messages.push({ role: 'assistant', content: reply });
    } catch (err) {
      messages.push({ 
        role: 'assistant', 
        content: 'Atsiprašau, šiuo metu negaliu atsakyti. Susisiekite: +370 686 02356 arba info@surfclub.lt' 
      });
    } finally {
      isLoading = false;
      render();
    }
  }

  // Initial render
  render();
})();
</script>
