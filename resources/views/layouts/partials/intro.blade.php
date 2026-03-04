<!-- Cyberpunk Intro Overlay -->
<div id="intro-overlay" class="fixed inset-0 z-[9999] bg-gray-900 overflow-hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; background: #111827; display: block;">
    <!-- Load Tailwind CSS cho intro -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Khóa scroll khi intro đang chạy */
        body.intro-active {
            overflow: hidden !important;
            height: 100vh !important;
        }
        
        /* Cyberpunk Intro Styles */
        .glitch-text {
            text-shadow: 2px 0 #0ff, -2px 0 #f0f;
            animation: glitch 0.2s linear infinite;
        }
        @keyframes glitch {
            0% { text-shadow: 2px 0 #0ff, -2px 0 #f0f; transform: translate(0) }
            20% { text-shadow: -2px 0 #0ff, 2px 0 #f0f; transform: translate(-2px, 2px) }
            40% { text-shadow: 2px 0 #0ff, -2px 0 #f0f; transform: translate(2px, -2px) }
            60% { text-shadow: -2px 0 #0ff, 2px 0 #f0f; transform: translate(-2px, -2px) }
            80% { text-shadow: 2px 0 #0ff, -2px 0 #f0f; transform: translate(2px, 2px) }
            100% { text-shadow: -2px 0 #0ff, 2px 0 #f0f; transform: translate(0) }
        }
        .scanline {
            background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,0) 50%, rgba(0,0,0,0.2) 50%, rgba(0,0,0,0.2));
            background-size: 100% 4px;
            pointer-events: none;
        }
        .falling-item {
            position: absolute;
            color: #22c55e;
            text-shadow: 0 0 8px #22c55e;
            font-weight: bold;
            opacity: 0;
            animation: fall linear forwards;
            pointer-events: none;
            z-index: 10;
        }
        @keyframes fall {
            0% { transform: translateY(-5vh); opacity: 0; }
            10% { opacity: 0.8; }
            80% { opacity: 0.8; }
            100% { transform: translateY(100vh); opacity: 0; }
        }
        @keyframes shake-padlock {
            0%, 100% { transform: translate(0, 0) rotate(0); }
            25% { transform: translate(-3px, 3px) rotate(-10deg); }
            50% { transform: translate(3px, -3px) rotate(10deg); }
            75% { transform: translate(-3px, -3px) rotate(-10deg); }
        }
        @keyframes padlock-explode {
            0% { transform: scale(1); opacity: 1; filter: brightness(1); }
            50% { transform: scale(3) rotate(15deg); opacity: 0.8; filter: brightness(2) drop-shadow(0 0 30px red); }
            100% { transform: scale(6) rotate(-15deg); opacity: 0; filter: brightness(4); }
        }
        @keyframes flash-explode {
            0% { transform: scale(0); opacity: 1; }
            100% { transform: scale(10); opacity: 0; }
        }
    </style>

    <div class="text-green-400 font-mono flex flex-col items-center justify-center min-h-screen overflow-hidden relative selection:bg-green-500 selection:text-black">
        <!-- Hiệu ứng sọc màn hình CRT -->
        <div class="absolute inset-0 scanline z-50"></div>
        
        <!-- Lớp chứa Cơn mưa Matrix -->
        <div id="matrix-rain" class="absolute inset-0 z-10 overflow-hidden pointer-events-none"></div>
        
        <!-- Nút Khởi động -->
        <button id="init-btn" class="relative z-50 px-6 py-3 md:px-8 md:py-4 bg-transparent border-2 border-green-500 text-green-500 hover:bg-green-500 hover:text-black transition-all font-bold tracking-widest text-lg md:text-2xl shadow-[0_0_20px_rgba(34,197,94,0.4)] hover:shadow-[0_0_40px_rgba(34,197,94,0.8)]">
            [ INIT_SYSTEM.EXE ]
        </button>
        
        <!-- Khu vực Terminal -->
        <div id="terminal" class="hidden relative z-40 w-full max-w-4xl p-4 sm:p-6 md:p-8 flex-col items-center justify-center h-full">
            <!-- Chữ chạy tự động -->
            <div id="typewriter" class="text-base sm:text-lg md:text-2xl leading-relaxed whitespace-pre-wrap drop-shadow-[0_0_5px_rgba(74,222,128,0.8)] w-full text-left"></div>
            
            <!-- PHA BẺ KHÓA -->
            <div id="cracking-phase" class="hidden relative z-40 w-full flex-col items-center justify-center">
                <img src="https://media.giphy.com/media/YQitE4YNQNahy/giphy.gif" 
                     onerror="this.onerror=null; this.src='https://media1.tenor.com/m/8Z31lA61yC8AAAAd/anime-hacker.gif';"
                     alt="Hacker Typing" 
                     class="w-32 h-32 sm:w-40 sm:h-40 md:w-56 md:h-56 rounded-lg opacity-90 shadow-[0_0_20px_rgba(34,197,94,0.5)] border-2 border-green-500/80 mb-6 object-cover">
                
                <div class="relative w-20 h-20 sm:w-24 sm:h-24 mb-4" id="padlock-wrapper">
                    <svg id="padlock-svg" viewBox="0 0 100 100" class="w-full h-full fill-current text-yellow-500 drop-shadow-[0_0_15px_rgba(234,179,8,0.8)] transition-all origin-center">
                        <path d="M30 40 V 30 C 30 15, 70 15, 70 30 V 40 Z" fill="none" stroke="currentColor" stroke-width="10" stroke-linecap="round"/>
                        <rect x="20" y="40" width="60" height="40" rx="8" />
                        <circle cx="50" cy="55" r="5" fill="#333"/>
                        <path d="M48 55 L 45 65 L 55 65 L 52 55 Z" fill="#333"/>
                        <path id="padlock-crack" d="M 30 40 L 45 50 L 35 60 L 60 75" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 transition-opacity duration-200"/>
                    </svg>
                    <div id="explosion-flash" class="absolute inset-0 bg-white rounded-full scale-0 opacity-0 pointer-events-none z-50"></div>
                </div>
                
                <div class="w-full max-w-[16rem] bg-black/80 border border-green-500/30 p-2 rounded text-xs font-mono text-green-400 h-20 overflow-hidden relative shadow-[0_0_10px_rgba(34,197,94,0.2)]">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/80 z-10 pointer-events-none"></div>
                    <div id="hex-stream" class="whitespace-pre-wrap opacity-70 break-all leading-tight"></div>
                </div>
                
                <p id="cracking-text" class="mt-4 text-red-500 font-bold animate-pulse text-lg sm:text-xl md:text-2xl drop-shadow-[0_0_5px_rgba(239,68,68,0.8)] text-center">
                    ĐANG BẺ KHÓA... <span id="cracking-progress">0%</span>
                </p>
            </div>
            
            <!-- LOGO & TEXT CHỐT HẠ -->
            <div id="final-logo" class="hidden mt-8 md:mt-12 w-full text-center flex-col items-center z-40">
                <h1 class="text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-black text-white uppercase italic tracking-tighter glitch-text leading-tight">
                    THANHVU V4 .NET
                </h1>
                <p class="text-cyan-400 mt-2 md:mt-4 text-sm sm:text-lg md:text-2xl font-bold tracking-widest drop-shadow-[0_0_8px_rgba(34,211,238,0.8)] animate-pulse">
                    >> KHO TÊN MIỀN VIP ĐÃ MỞ <<
                </p>
                
                <div id="welcome-text" class="opacity-0 transition-opacity duration-1000 mt-8 md:mt-12 text-center drop-shadow-[0_0_15px_rgba(0,0,0,0.8)] px-2">
                    <p class="text-lg sm:text-xl md:text-3xl text-green-400 font-bold mb-2 leading-snug">
                        CHÀO MỪNG TỚI TRANG WEB TÊN MIỀN TỐI THƯỢNG
                    </p>
                    <p class="text-4xl sm:text-5xl md:text-7xl text-white font-black glitch-text tracking-widest">
                        VTKT.ONLINE
                    </p>
                </div>
                
                <button id="enter-site-btn" class="opacity-0 transition-opacity duration-1000 mt-8 md:mt-12 px-6 py-3 sm:px-8 sm:py-4 bg-green-500 text-black font-black text-xl md:text-2xl hover:bg-white hover:text-black transition-all hover:scale-110 shadow-[0_0_30px_rgba(34,197,94,0.8)] border-4 border-green-400 cursor-pointer">
                    [ VÀO TRANG CHỦ ]
                </button>
            </div>
        </div>
        
        <!-- Thanh Công Cụ Dưới Cùng -->
        <div class="absolute bottom-4 md:bottom-6 z-50 flex flex-col items-center gap-2 md:gap-3 w-full px-3">
            <button id="skip-btn" class="hidden text-gray-500 hover:text-green-400 font-bold tracking-widest transition-colors text-xs md:text-base cursor-pointer">
                [ BỎ QUA TIẾN TRÌNH >> ]
            </button>
            
            <div class="flex items-center gap-2 cursor-pointer hover:opacity-80 transition-opacity bg-black/80 px-3 py-2 rounded-lg border border-green-500/30 backdrop-blur-sm max-w-[90%] md:max-w-none">
                <input type="checkbox" id="dont-show-again" class="w-4 h-4 md:w-5 md:h-5 accent-green-500 cursor-pointer rounded flex-shrink-0">
                <label for="dont-show-again" class="text-gray-400 text-[11px] md:text-sm cursor-pointer select-none leading-tight">
                    Không hiển thị lại trong phiên làm việc này
                </label>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // Các biến DOM
    const introOverlay = document.getElementById('intro-overlay');
    const initBtn = document.getElementById('init-btn');
    const terminal = document.getElementById('terminal');
    const typewriter = document.getElementById('typewriter');
    const finalLogo = document.getElementById('final-logo');
    const welcomeText = document.getElementById('welcome-text');
    const matrixRain = document.getElementById('matrix-rain');
    const enterSiteBtn = document.getElementById('enter-site-btn');
    const skipBtn = document.getElementById('skip-btn');
    const dontShowCheckbox = document.getElementById('dont-show-again');
    
    let rainInterval, crackInterval;
    let isSkipped = false;
    let audioCtx;
    
    // Khóa scroll ngay khi load
    document.body.classList.add('intro-active');
    
    // Kiểm tra sessionStorage
    if (sessionStorage.getItem('skipIntroSession') === 'true') {
        document.body.classList.remove('intro-active');
        introOverlay.remove();
        return;
    }
    
    // Checkbox handler
    dontShowCheckbox.addEventListener('change', (e) => {
        if (e.target.checked) {
            sessionStorage.setItem('skipIntroSession', 'true');
        } else {
            sessionStorage.removeItem('skipIntroSession');
        }
    });
    
    // Audio functions
    function initAudio() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') audioCtx.resume();
    }
    
    function playTypeSound() {
        if(!audioCtx || isSkipped) return;
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'square';
        osc.frequency.setValueAtTime(400 + Math.random() * 400, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.05, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.05);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.05);
    }
    
    function playBassDrop() {
        if(!audioCtx) return;
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(100, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(10, audioCtx.currentTime + 1.5);
        gain.gain.setValueAtTime(0.5, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.5);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 1.5);
    }
    
    function playExplosionSound() {
        if(!audioCtx || isSkipped) return;
        const bufferSize = audioCtx.sampleRate * 2;
        const buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
        const data = buffer.getChannelData(0);
        for (let i = 0; i < bufferSize; i++) {
            data[i] = Math.random() * 2 - 1;
        }
        const noise = audioCtx.createBufferSource();
        noise.buffer = buffer;
        const filter = audioCtx.createBiquadFilter();
        filter.type = 'lowpass';
        filter.frequency.setValueAtTime(800, audioCtx.currentTime);
        filter.frequency.exponentialRampToValueAtTime(10, audioCtx.currentTime + 1);
        const gainNode = audioCtx.createGain();
        gainNode.gain.setValueAtTime(0.8, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 1);
        noise.connect(filter);
        filter.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        noise.start();
        noise.stop(audioCtx.currentTime + 1);
    }
    
    // Matrix rain
    const rainWords = ["VTKT.ONLINE", "0101100", "1011011", "VIP_DOMAIN", "ACCESS_GRANTED", "0x00FF", "SYSTEM.SYS", "vtkt.online", "101010", "ANIME.VN", "THANHVU_V4"];
    
    function createRainDrop() {
        const drop = document.createElement('div');
        drop.classList.add('falling-item');
        drop.innerText = rainWords[Math.floor(Math.random() * rainWords.length)];
        drop.style.left = Math.random() * 100 + 'vw';
        drop.style.fontSize = (Math.random() * 1.5 + 0.8) + 'rem';
        const duration = Math.random() * 2 + 1.5;
        drop.style.animationDuration = duration + 's';
        matrixRain.appendChild(drop);
        setTimeout(() => { drop.remove(); }, duration * 1000);
    }
    
    function showFinalScreen(playAudio, immediate = false) {
        finalLogo.classList.remove('hidden');
        finalLogo.classList.add('flex');
        if(playAudio && audioCtx) playBassDrop();
        if(!rainInterval) rainInterval = setInterval(createRainDrop, 60);
        
        if (immediate) {
            welcomeText.classList.remove('opacity-0');
            welcomeText.classList.add('opacity-100');
            enterSiteBtn.classList.remove('opacity-0');
            enterSiteBtn.classList.add('opacity-100');
        } else {
            setTimeout(() => {
                if(isSkipped) return;
                welcomeText.classList.remove('opacity-0');
                welcomeText.classList.add('opacity-100');
                setTimeout(() => {
                    if(isSkipped) return;
                    enterSiteBtn.classList.remove('opacity-0');
                    enterSiteBtn.classList.add('opacity-100');
                }, 1000);
            }, 1500);
        }
    }
    
    function skipIntro() {
        if (isSkipped) return;
        isSkipped = true;
        if(crackInterval) clearInterval(crackInterval);
        initBtn.style.display = 'none';
        typewriter.style.display = 'none';
        document.getElementById('cracking-phase').classList.add('hidden');
        skipBtn.classList.add('hidden');
        terminal.classList.remove('hidden');
        terminal.classList.add('flex');
        showFinalScreen(true, true);
    }
    
    skipBtn.addEventListener('click', () => {
        initAudio();
        skipIntro();
    });
    
    const scriptLines = [
        "Đang thiết lập kết nối mã hóa... [OK]",
        "Vượt tường lửa hệ thống thành công... [OK]",
        "Đang dò tìm không gian mạng ảo (Cyberspace)...",
        "CẢNH BÁO: Phát hiện nguồn năng lượng lớn!",
        "Giải mã kho dữ liệu: TÊN MIỀN VIP..."
    ];
    
    async function typeWriterEffect() {
        for (let i = 0; i < scriptLines.length; i++) {
            if(isSkipped) return;
            let line = scriptLines[i];
            for (let j = 0; j < line.length; j++) {
                if(isSkipped) return;
                typewriter.innerHTML += line.charAt(j);
                playTypeSound();
                await new Promise(r => setTimeout(r, 20 + Math.random() * 30));
            }
            if(isSkipped) return;
            typewriter.innerHTML += "\n<br/>";
            await new Promise(r => setTimeout(r, 400));
        }
        
        if(isSkipped) return;
        setTimeout(() => {
            if(isSkipped) return;
            typewriter.style.display = 'none';
            const crackingPhase = document.getElementById('cracking-phase');
            const hexStream = document.getElementById('hex-stream');
            const progress = document.getElementById('cracking-progress');
            const padlockSvg = document.getElementById('padlock-svg');
            const padlockCrack = document.getElementById('padlock-crack');
            const crackingText = document.getElementById('cracking-text');
            const flash = document.getElementById('explosion-flash');
            
            crackingPhase.classList.remove('hidden');
            crackingPhase.classList.add('flex');
            
            let percent = 0;
            crackInterval = setInterval(() => {
                if(isSkipped) return clearInterval(crackInterval);
                let fakeCode = "";
                for(let i=0; i<4; i++) fakeCode += "0x" + Math.random().toString(16).substr(2, 6).toUpperCase() + " ";
                hexStream.innerText = fakeCode + "\n" + hexStream.innerText;
                if(hexStream.innerText.length > 200) hexStream.innerText = hexStream.innerText.substring(0, 200);
                
                percent += Math.floor(Math.random() * 4) + 1;
                if(percent >= 100) percent = 100;
                progress.innerText = percent + "%";
                
                if(percent > 30) padlockSvg.style.animation = "shake-padlock 0.3s infinite";
                if(percent > 60) {
                    padlockSvg.style.animation = "shake-padlock 0.1s infinite";
                    padlockCrack.classList.remove('opacity-0');
                }
                if(percent > 85) padlockCrack.style.strokeWidth = "6";
            }, 50);
            
            setTimeout(() => {
                if(isSkipped) return;
                clearInterval(crackInterval);
                progress.innerText = "100%";
                crackingText.innerText = "ACCESS GRANTED!";
                crackingText.classList.replace('text-red-500', 'text-green-500');
                playExplosionSound();
                padlockSvg.style.animation = "padlock-explode 0.6s forwards";
                flash.style.animation = "flash-explode 0.6s forwards";
                
                setTimeout(() => {
                    if(isSkipped) return;
                    crackingPhase.classList.remove('flex');
                    crackingPhase.classList.add('hidden');
                    skipBtn.classList.add('hidden');
                    showFinalScreen(true, false);
                }, 600);
            }, 2500);
        }, 500);
    }
    
    initBtn.addEventListener('click', () => {
        initAudio();
        initBtn.style.display = 'none';
        skipBtn.classList.remove('hidden');
        terminal.classList.remove('hidden');
        terminal.classList.add('flex');
        typeWriterEffect();
    });
    
    // Nút VÀO TRANG CHỦ - Fade mượt
    enterSiteBtn.addEventListener('click', () => {
        introOverlay.style.transition = 'opacity 1s';
        introOverlay.style.opacity = '0';
        setTimeout(() => {
            document.body.classList.remove('intro-active');
            introOverlay.remove();
            if(rainInterval) clearInterval(rainInterval);
        }, 1000);
    });
})();
</script>
