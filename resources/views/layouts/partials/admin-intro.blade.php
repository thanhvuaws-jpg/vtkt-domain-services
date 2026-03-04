<!-- Admin Cyberpunk Intro Overlay -->
<div id="admin-intro-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; background: rgb(5, 5, 5); display: block; width: 100%; height: 100vh; overflow-x: hidden; overflow-y: hidden;">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Cyberpunk Admin Intro Styles */
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw !important;
        }
        
        body.admin-intro-active {
            overflow: hidden !important;
            height: 100vh !important;
            width: 100% !important;
            max-width: 100vw !important;
            position: fixed !important;
        }
        
        #admin-intro-overlay {
            overflow-x: hidden !important;
            overflow-y: hidden !important;
        }
        
        #admin-intro-overlay * {
            max-width: 100vw;
            box-sizing: border-box;
        }
        
        .glitch-text-admin {
            text-shadow: 2px 0 #f00, -2px 0 #0ff;
            animation: glitch-admin 0.15s linear infinite;
        }
        @keyframes glitch-admin {
            0% { text-shadow: 2px 0 #f00, -2px 0 #0ff; transform: translate(0) }
            20% { text-shadow: -2px 0 #f00, 2px 0 #0ff; transform: translate(-2px, 2px) }
            40% { text-shadow: 2px 0 #f00, -2px 0 #0ff; transform: translate(2px, -2px) }
            60% { text-shadow: -2px 0 #f00, 2px 0 #0ff; transform: translate(-2px, -2px) }
            80% { text-shadow: 2px 0 #f00, -2px 0 #0ff; transform: translate(2px, 2px) }
            100% { text-shadow: -2px 0 #f00, 2px 0 #0ff; transform: translate(0) }
        }
        .scanline-admin {
            background: linear-gradient(to bottom, rgba(255,0,0,0), rgba(255,0,0,0) 50%, rgba(255,0,0,0.1) 50%, rgba(255,0,0,0.1));
            background-size: 100% 4px;
            pointer-events: none;
        }
        .falling-item-admin {
            position: absolute;
            color: #ef4444;
            text-shadow: 0 0 8px #ef4444;
            font-weight: bold;
            opacity: 0;
            animation: fall-admin linear forwards;
            pointer-events: none;
            z-index: 10;
        }
        @keyframes fall-admin {
            0% { transform: translateY(-5vh); opacity: 0; }
            10% { opacity: 0.8; }
            80% { opacity: 0.8; }
            100% { transform: translateY(100vh); opacity: 0; }
        }
        @keyframes pulse-shield {
            0%, 100% { filter: drop-shadow(0 0 10px rgba(239,68,68,0.5)); transform: scale(1); }
            50% { filter: drop-shadow(0 0 25px rgba(239,68,68,1)); transform: scale(1.05); }
        }
        @keyframes shield-unlock {
            0% { transform: scale(1); opacity: 1; filter: brightness(1); }
            50% { transform: scale(1.5); opacity: 0.8; filter: brightness(2) drop-shadow(0 0 40px cyan); }
            100% { transform: scale(4); opacity: 0; filter: brightness(4); }
        }
        @keyframes flash-explode-admin {
            0% { transform: scale(0); opacity: 1; background-color: #ef4444; }
            50% { background-color: #06b6d4; }
            100% { transform: scale(15); opacity: 0; }
        }
    </style>

    <div class="text-red-500 font-mono flex flex-col items-center justify-center relative selection:bg-red-500 selection:text-white" style="width: 100%; height: 100vh; max-width: 100vw; padding: 0 5px; box-sizing: border-box; overflow: hidden;">
        <div class="absolute inset-0 scanline-admin z-50" style="pointer-events: none;"></div>
        <div id="matrix-rain-admin" class="absolute inset-0 z-10 overflow-hidden pointer-events-none" style="max-width: 100vw;"></div>
        
        <button id="init-btn-admin" class="relative z-50 px-4 py-2 md:px-8 md:py-4 bg-transparent border-2 border-red-500 text-red-500 hover:bg-red-600 hover:text-white transition-all font-bold tracking-widest text-base md:text-2xl shadow-[0_0_20px_rgba(239,68,68,0.4)] hover:shadow-[0_0_40px_rgba(239,68,68,0.8)]" style="max-width: calc(100vw - 20px);">
            [ ADMIN_LOGIN.EXE ]
        </button>
        
        <div id="terminal-admin" class="hidden relative z-40 w-full p-3 sm:p-6 md:p-8 flex-col items-center justify-center h-full" style="max-width: calc(100vw - 20px); box-sizing: border-box;">
            <div id="typewriter-admin" class="text-sm sm:text-lg md:text-2xl leading-relaxed whitespace-pre-wrap drop-shadow-[0_0_5px_rgba(239,68,68,0.8)] w-full text-left" style="max-width: 100%; overflow-wrap: break-word;"></div>
            
            <div id="cracking-phase-admin" class="hidden relative z-40 w-full flex-col items-center justify-center">
                <img src="https://media.giphy.com/media/YQitE4YNQNahy/giphy.gif" 
                     onerror="this.onerror=null; this.src='https://media1.tenor.com/m/8Z31lA61yC8AAAAd/anime-hacker.gif';"
                     alt="Admin Analyzing" 
                     style="filter: hue-rotate(240deg) saturate(2);"
                     class="w-32 h-32 sm:w-40 sm:h-40 md:w-56 md:h-56 rounded-full opacity-90 shadow-[0_0_30px_rgba(239,68,68,0.6)] border-4 border-red-500/80 mb-6 object-cover">
                
                <div class="relative w-20 h-20 sm:w-24 sm:h-24 mb-4" id="shield-wrapper-admin">
                    <svg id="shield-svg-admin" viewBox="0 0 100 100" class="w-full h-full fill-current text-red-500 transition-all origin-center" style="animation: pulse-shield 1s infinite;">
                        <path d="M50 5 L10 25 V50 C10 75 40 95 50 95 C60 95 90 75 90 50 V25 Z" fill="none" stroke="currentColor" stroke-width="6" stroke-linejoin="round"/>
                        <rect x="35" y="45" width="30" height="20" rx="4" fill="currentColor"/>
                        <path d="M42 45 V35 C42 25 58 25 58 35 V45" fill="none" stroke="currentColor" stroke-width="4"/>
                        <circle cx="50" cy="55" r="3" fill="#000"/>
                    </svg>
                    <div id="explosion-flash-admin" class="absolute inset-0 rounded-full scale-0 opacity-0 pointer-events-none z-50"></div>
                </div>
                
                <div class="w-full max-w-[16rem] bg-red-950/80 border border-red-500/50 p-2 rounded text-xs font-mono text-red-400 h-20 overflow-hidden relative shadow-[0_0_15px_rgba(239,68,68,0.3)]">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-red-950/90 z-10 pointer-events-none"></div>
                    <div id="hex-stream-admin" class="whitespace-pre-wrap opacity-80 break-all leading-tight"></div>
                </div>
                
                <p id="cracking-text-admin" class="mt-4 text-red-500 font-black animate-pulse text-lg sm:text-xl md:text-2xl drop-shadow-[0_0_8px_rgba(239,68,68,1)] text-center">
                    ĐANG XÁC MINH ROOT... <span id="cracking-progress-admin">0%</span>
                </p>
            </div>
            
            <div id="final-logo-admin" class="hidden mt-8 md:mt-12 w-full text-center flex-col items-center z-40">
                <p class="text-cyan-400 mb-2 md:mb-4 text-sm sm:text-lg md:text-2xl font-bold tracking-widest drop-shadow-[0_0_8px_rgba(34,211,238,0.8)] animate-pulse">
                    [ AUTHENTICATION SUCCESSFUL ]
                </p>
                
                <h1 class="text-3xl sm:text-5xl md:text-7xl lg:text-8xl font-black text-white uppercase italic tracking-tighter glitch-text-admin leading-tight drop-shadow-[0_0_20px_rgba(255,0,0,0.8)]">
                    HỆ THỐNG QUẢN TRỊ
                </h1>
                
                <div id="welcome-text-admin" class="opacity-0 transition-opacity duration-1000 mt-8 md:mt-12 text-center drop-shadow-[0_0_15px_rgba(0,0,0,0.8)] px-2">
                    <p class="text-base sm:text-xl md:text-3xl text-cyan-400 font-bold mb-2 leading-snug">
                        XIN CHÀO QUẢN TRỊ VIÊN TỐI CAO CỦA
                    </p>
                    <p class="text-2xl sm:text-4xl md:text-6xl text-white font-black tracking-widest" style="text-shadow: 0 0 15px cyan;">
                        VTKT.ONLINE
                    </p>
                </div>
                
                <button id="enter-site-btn-admin" class="opacity-0 transition-opacity duration-1000 mt-8 md:mt-12 px-5 py-3 sm:px-8 sm:py-4 bg-cyan-500 text-black font-black text-lg md:text-2xl hover:bg-white transition-all hover:scale-110 shadow-[0_0_30px_rgba(6,182,212,0.8)] border-4 border-cyan-300 cursor-pointer">
                    [ MỞ DASHBOARD ]
                </button>
            </div>
        </div>
        
        <div class="fixed bottom-3 left-0 right-0 z-50 flex flex-col items-center gap-2 w-full px-2" style="max-width: 100vw; box-sizing: border-box;">
            <button id="skip-btn-admin" class="hidden text-gray-500 hover:text-red-400 font-bold tracking-widest transition-colors text-xs md:text-base cursor-pointer">
                [ BYPASS >> ]
            </button>
            
            <div class="flex items-center justify-center gap-2 cursor-pointer hover:opacity-80 transition-opacity bg-black/80 px-2 py-2 rounded-lg border border-red-500/30 backdrop-blur-sm w-auto" style="max-width: calc(100vw - 20px); box-sizing: border-box;">
                <input type="checkbox" id="dont-show-again-admin" class="w-4 h-4 accent-red-600 cursor-pointer rounded flex-shrink-0" style="min-width: 16px;">
                <label for="dont-show-again-admin" class="text-gray-400 text-[10px] sm:text-xs md:text-sm cursor-pointer select-none leading-tight whitespace-nowrap">
                    Bỏ qua lần sau
                </label>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const adminIntroOverlay = document.getElementById('admin-intro-overlay');
    const initBtnAdmin = document.getElementById('init-btn-admin');
    const terminalAdmin = document.getElementById('terminal-admin');
    const typewriterAdmin = document.getElementById('typewriter-admin');
    const finalLogoAdmin = document.getElementById('final-logo-admin');
    const welcomeTextAdmin = document.getElementById('welcome-text-admin');
    const matrixRainAdmin = document.getElementById('matrix-rain-admin');
    const enterSiteBtnAdmin = document.getElementById('enter-site-btn-admin');
    const skipBtnAdmin = document.getElementById('skip-btn-admin');
    const dontShowCheckboxAdmin = document.getElementById('dont-show-again-admin');
    
    let rainIntervalAdmin, crackIntervalAdmin;
    let isSkippedAdmin = false;
    let audioCtxAdmin;
    
    document.body.classList.add('admin-intro-active');
    
    if (sessionStorage.getItem('skipAdminIntro') === 'true') {
        document.body.classList.remove('admin-intro-active');
        adminIntroOverlay.remove();
        return;
    }
    
    dontShowCheckboxAdmin.addEventListener('change', (e) => {
        if (e.target.checked) {
            sessionStorage.setItem('skipAdminIntro', 'true');
        } else {
            sessionStorage.removeItem('skipAdminIntro');
        }
    });
    
    function initAudioAdmin() {
        if (!audioCtxAdmin) {
            audioCtxAdmin = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtxAdmin.state === 'suspended') audioCtxAdmin.resume();
    }
    
    function playTypeSoundAdmin() {
        if(!audioCtxAdmin || isSkippedAdmin) return;
        const osc = audioCtxAdmin.createOscillator();
        const gain = audioCtxAdmin.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(300 + Math.random() * 200, audioCtxAdmin.currentTime);
        gain.gain.setValueAtTime(0.08, audioCtxAdmin.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtxAdmin.currentTime + 0.05);
        osc.connect(gain);
        gain.connect(audioCtxAdmin.destination);
        osc.start();
        osc.stop(audioCtxAdmin.currentTime + 0.05);
    }
    
    function playBassDropAdmin() {
        if(!audioCtxAdmin) return;
        const osc = audioCtxAdmin.createOscillator();
        const gain = audioCtxAdmin.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(80, audioCtxAdmin.currentTime);
        osc.frequency.exponentialRampToValueAtTime(5, audioCtxAdmin.currentTime + 2);
        gain.gain.setValueAtTime(0.7, audioCtxAdmin.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtxAdmin.currentTime + 2);
        osc.connect(gain);
        gain.connect(audioCtxAdmin.destination);
        osc.start();
        osc.stop(audioCtxAdmin.currentTime + 2);
    }
    
    function playExplosionSoundAdmin() {
        if(!audioCtxAdmin || isSkippedAdmin) return;
        const bufferSize = audioCtxAdmin.sampleRate * 2;
        const buffer = audioCtxAdmin.createBuffer(1, bufferSize, audioCtxAdmin.sampleRate);
        const data = buffer.getChannelData(0);
        for (let i = 0; i < bufferSize; i++) {
            data[i] = Math.random() * 2 - 1;
        }
        const noise = audioCtxAdmin.createBufferSource();
        noise.buffer = buffer;
        const filter = audioCtxAdmin.createBiquadFilter();
        filter.type = 'lowpass';
        filter.frequency.setValueAtTime(600, audioCtxAdmin.currentTime);
        filter.frequency.exponentialRampToValueAtTime(10, audioCtxAdmin.currentTime + 1.5);
        const gainNode = audioCtxAdmin.createGain();
        gainNode.gain.setValueAtTime(1, audioCtxAdmin.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtxAdmin.currentTime + 1.5);
        noise.connect(filter);
        filter.connect(gainNode);
        gainNode.connect(audioCtxAdmin.destination);
        noise.start();
        noise.stop(audioCtxAdmin.currentTime + 1.5);
    }
    
    const rainWordsAdmin = ["ROOT", "ADMIN", "SUDO_SU", "SYS_CTL", "VTKT.ONLINE", "0xFF0000", "ACCESS_LOG", "DASHBOARD"];
    
    function createRainDropAdmin() {
        const drop = document.createElement('div');
        drop.classList.add('falling-item-admin');
        drop.innerText = rainWordsAdmin[Math.floor(Math.random() * rainWordsAdmin.length)];
        drop.style.left = Math.random() * 100 + 'vw';
        drop.style.fontSize = (Math.random() * 1.5 + 0.8) + 'rem';
        
        if(document.getElementById('cracking-phase-admin').classList.contains('hidden')) {
            drop.style.color = '#06b6d4';
            drop.style.textShadow = '0 0 8px #06b6d4';
        }
        
        const duration = Math.random() * 2 + 1.5;
        drop.style.animationDuration = duration + 's';
        matrixRainAdmin.appendChild(drop);
        setTimeout(() => { drop.remove(); }, duration * 1000);
    }
    
    function showFinalScreenAdmin(playAudio, immediate = false) {
        finalLogoAdmin.classList.remove('hidden');
        finalLogoAdmin.classList.add('flex');
        if(playAudio && audioCtxAdmin) playBassDropAdmin();
        if(!rainIntervalAdmin) rainIntervalAdmin = setInterval(createRainDropAdmin, 60);
        
        if (immediate) {
            welcomeTextAdmin.classList.remove('opacity-0');
            welcomeTextAdmin.classList.add('opacity-100');
            enterSiteBtnAdmin.classList.remove('opacity-0');
            enterSiteBtnAdmin.classList.add('opacity-100');
        } else {
            setTimeout(() => {
                if(isSkippedAdmin) return;
                welcomeTextAdmin.classList.remove('opacity-0');
                welcomeTextAdmin.classList.add('opacity-100');
                setTimeout(() => {
                    if(isSkippedAdmin) return;
                    enterSiteBtnAdmin.classList.remove('opacity-0');
                    enterSiteBtnAdmin.classList.add('opacity-100');
                }, 1000);
            }, 1500);
        }
    }
    
    function skipIntroAdmin() {
        if (isSkippedAdmin) return;
        isSkippedAdmin = true;
        if(crackIntervalAdmin) clearInterval(crackIntervalAdmin);
        initBtnAdmin.style.display = 'none';
        typewriterAdmin.style.display = 'none';
        document.getElementById('cracking-phase-admin').classList.add('hidden');
        skipBtnAdmin.classList.add('hidden');
        terminalAdmin.classList.remove('hidden');
        terminalAdmin.classList.add('flex');
        showFinalScreenAdmin(true, true);
    }
    
    skipBtnAdmin.addEventListener('click', () => {
        initAudioAdmin();
        skipIntroAdmin();
    });
    
    const scriptLinesAdmin = [
        "Đang thiết lập kết nối mã hóa tới Cổng máy chủ... [OK]",
        "CẢNH BÁO: KHU VỰC DÀNH RIÊNG CHO QUẢN TRỊ VIÊN!",
        "Mọi hành vi xâm nhập trái phép sẽ bị log IP và vô hiệu hóa.",
        "Yêu cầu quyền truy cập cấp ROOT...",
        "Đang phân tích dữ liệu vân tay và sinh trắc học..."
    ];
    
    async function typeWriterEffectAdmin() {
        for (let i = 0; i < scriptLinesAdmin.length; i++) {
            if(isSkippedAdmin) return;
            let line = scriptLinesAdmin[i];
            for (let j = 0; j < line.length; j++) {
                if(isSkippedAdmin) return;
                typewriterAdmin.innerHTML += line.charAt(j);
                playTypeSoundAdmin();
                await new Promise(r => setTimeout(r, 15 + Math.random() * 25));
            }
            if(isSkippedAdmin) return;
            typewriterAdmin.innerHTML += "\n<br/>";
            await new Promise(r => setTimeout(r, 400));
        }
        
        if(isSkippedAdmin) return;
        setTimeout(() => {
            if(isSkippedAdmin) return;
            typewriterAdmin.style.display = 'none';
            const crackingPhaseAdmin = document.getElementById('cracking-phase-admin');
            const hexStreamAdmin = document.getElementById('hex-stream-admin');
            const progressAdmin = document.getElementById('cracking-progress-admin');
            const shieldSvgAdmin = document.getElementById('shield-svg-admin');
            const crackingTextAdmin = document.getElementById('cracking-text-admin');
            const flashAdmin = document.getElementById('explosion-flash-admin');
            
            crackingPhaseAdmin.classList.remove('hidden');
            crackingPhaseAdmin.classList.add('flex');
            
            rainIntervalAdmin = setInterval(createRainDropAdmin, 80);
            
            let percent = 0;
            crackIntervalAdmin = setInterval(() => {
                if(isSkippedAdmin) return clearInterval(crackIntervalAdmin);
                let fakeCode = "";
                for(let i=0; i<4; i++) fakeCode += "0x" + Math.random().toString(16).substr(2, 6).toUpperCase() + " ";
                hexStreamAdmin.innerText = fakeCode + "\n" + hexStreamAdmin.innerText;
                if(hexStreamAdmin.innerText.length > 200) hexStreamAdmin.innerText = hexStreamAdmin.innerText.substring(0, 200);
                
                percent += Math.floor(Math.random() * 3) + 1;
                if(percent >= 100) percent = 100;
                progressAdmin.innerText = percent + "%";
                
                if(percent > 80) shieldSvgAdmin.classList.replace('text-red-500', 'text-yellow-400');
            }, 40);
            
            setTimeout(() => {
                if(isSkippedAdmin) return;
                clearInterval(crackIntervalAdmin);
                progressAdmin.innerText = "100%";
                crackingTextAdmin.innerText = "ACCESS GRANTED. ROOT LEVEL.";
                crackingTextAdmin.classList.replace('text-red-500', 'text-cyan-400');
                playExplosionSoundAdmin();
                shieldSvgAdmin.style.animation = "shield-unlock 0.8s forwards";
                flashAdmin.style.animation = "flash-explode-admin 0.8s forwards";
                
                setTimeout(() => {
                    if(isSkippedAdmin) return;
                    crackingPhaseAdmin.classList.remove('flex');
                    crackingPhaseAdmin.classList.add('hidden');
                    skipBtnAdmin.classList.add('hidden');
                    clearInterval(rainIntervalAdmin);
                    rainIntervalAdmin = null;
                    showFinalScreenAdmin(true, false);
                }, 800);
            }, 2800);
        }, 500);
    }
    
    initBtnAdmin.addEventListener('click', () => {
        initAudioAdmin();
        initBtnAdmin.style.display = 'none';
        skipBtnAdmin.classList.remove('hidden');
        terminalAdmin.classList.remove('hidden');
        terminalAdmin.classList.add('flex');
        typeWriterEffectAdmin();
    });
    
    enterSiteBtnAdmin.addEventListener('click', () => {
        adminIntroOverlay.style.transition = 'opacity 1s';
        adminIntroOverlay.style.opacity = '0';
        setTimeout(() => {
            document.body.classList.remove('admin-intro-active');
            adminIntroOverlay.remove();
            if(rainIntervalAdmin) clearInterval(rainIntervalAdmin);
        }, 1000);
    });
})();
</script>
