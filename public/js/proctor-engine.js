/**
 * Enterprise Anti-Cheat Proctoring Engine & HTML5 Canvas Renderer
 * Architecture:
 *  1. Dynamic Faint Watermark Canvas Layer (Timestamp, Name, ID, IP)
 *  2. Question & Code Snippet Pixel Buffer Canvas Renderer (Light Paper Mode)
 *  3. Client Hardening (Shortcuts, Context Menu, DevTools, Visibility, Blur)
 *  4. Strike Management, Heartbeat & Auto-Termination
 */

class ProctorEngine {
    constructor(config) {
        this.token = config.token;
        this.apiUrl = config.apiUrl || '/api/proctor';
        this.csrfToken = config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
        this.examTitle = config.examTitle || 'Examination';
        this.studentName = config.studentName || 'Candidate';
        this.studentId = config.studentId || 'ID-UNKNOWN';
        this.ipAddress = config.ipAddress || '127.0.0.1';
        this.maxStrikes = config.maxStrikes || 3;
        this.strikesCount = config.strikesCount || 0;
        this.remainingSeconds = config.remainingSeconds || 0;
        this.submitUrl = config.submitUrl || '';
        this.resultUrl = config.resultUrl || '';
        // Calm watermark opacity to prevent eye fatigue while remaining clear for photos
        this.watermarkOpacity = config.watermarkOpacity !== undefined ? config.watermarkOpacity : 0.05;
        this.enforceFullscreen = config.enforceFullscreen !== false;

        this.questions = [];
        this.currentIndex = 0;
        this.answers = {};
        this.isTerminated = false;
        this.isSubmitted = false;
        this.heartbeatTimer = null;
        this.countdownTimer = null;
        this.watermarkAnimFrame = null;
        this.lastInfractionTime = 0;

        this.init();
    }

    async init() {
        this.setupWatermarkCanvas();
        this.setupSecurityListeners();
        this.setupDevToolsDetection();
        await this.loadExamData();
        this.startCountdownTimer();
        this.startHeartbeat();
        this.setupNavigationControls();
    }

    /**
     * 1. HTML5 Canvas Dynamic Faint Watermark Layer
     * Renders a real-time updating diagonal forensic grid over the viewport.
     */
    setupWatermarkCanvas() {
        let canvas = document.getElementById('proctor-watermark-canvas');
        if (!canvas) {
            canvas = document.createElement('canvas');
            canvas.id = 'proctor-watermark-canvas';
            canvas.style.position = 'fixed';
            canvas.style.top = '0';
            canvas.style.left = '0';
            canvas.style.width = '100vw';
            canvas.style.height = '100vh';
            canvas.style.pointerEvents = 'none';
            canvas.style.zIndex = '999999';
            canvas.style.opacity = this.watermarkOpacity;
            document.body.appendChild(canvas);
        }

        this.watermarkCanvas = canvas;
        this.watermarkCtx = canvas.getContext('2d');

        const resizeWatermark = () => {
            const dpr = window.devicePixelRatio || 1;
            canvas.width = window.innerWidth * dpr;
            canvas.height = window.innerHeight * dpr;
            this.watermarkCtx.scale(dpr, dpr);
        };

        window.addEventListener('resize', resizeWatermark);
        resizeWatermark();

        // Render continuously with live ticking timestamp
        const drawWatermark = () => {
            if (this.isTerminated) return;

            const ctx = this.watermarkCtx;
            const w = window.innerWidth;
            const h = window.innerHeight;

            ctx.clearRect(0, 0, w, h);
            ctx.save();

            const now = new Date();
            const timeStr = now.toISOString().replace('T', ' ').substring(0, 19) + ' UTC';
            const watermarkText = `${this.studentName} • ${this.studentId} • ${this.ipAddress} • ${timeStr}`;

            ctx.font = 'bold 14px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, monospace';
            ctx.fillStyle = '#0f172a';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            const stepX = 380;
            const stepY = 190;
            const angle = -22 * Math.PI / 180;

            for (let y = -h; y < h * 2; y += stepY) {
                for (let x = -w; x < w * 2; x += stepX) {
                    ctx.save();
                    ctx.translate(x, y);
                    ctx.rotate(angle);
                    ctx.fillText(watermarkText, 0, 0);
                    ctx.restore();
                }
            }

            ctx.restore();
            setTimeout(() => {
                requestAnimationFrame(drawWatermark);
            }, 1000);
        };

        drawWatermark();
    }

    /**
     * 2. HTML5 Canvas Question & Code Snippet Pixel Buffer Renderer (Light Paper Mode)
     * Renders directly into canvas pixel buffer to prevent AI extension scraping.
     */
    renderQuestionOnCanvas(question) {
        const container = document.getElementById('canvas-question-container');
        if (!container) return;

        container.innerHTML = '';

        const canvas = document.createElement('canvas');
        canvas.id = 'active-question-canvas';
        canvas.className = 'w-full block';
        container.appendChild(canvas);

        const ctx = canvas.getContext('2d');
        const dpr = window.devicePixelRatio || 1;

        // Container display width
        const displayWidth = container.clientWidth || 800;

        // Padding and layout metrics
        const padding = 32;
        const contentWidth = displayWidth - (padding * 2);

        // Measure prompt lines
        const promptLines = this.wrapText(ctx, question.prompt, contentWidth, '600 18px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif');
        const promptLineHeight = 30;
        const promptHeight = promptLines.length * promptLineHeight;

        // Code snippet dimensions
        let codeHeight = 0;
        let codeLines = [];
        if (question.code_snippet) {
            codeLines = question.code_snippet.trim().split('\n');
            const codeLineHeight = 22;
            const codeHeaderHeight = 38;
            const codePadding = 24;
            codeHeight = codeHeaderHeight + (codeLines.length * codeLineHeight) + codePadding;
        }

        const headerHeight = 44;
        const totalHeight = headerHeight + promptHeight + (codeHeight ? codeHeight + 24 : 0) + (padding * 2);

        // High-DPI canvas sizing
        canvas.width = displayWidth * dpr;
        canvas.height = totalHeight * dpr;
        canvas.style.width = `${displayWidth}px`;
        canvas.style.height = `${totalHeight}px`;

        ctx.scale(dpr, dpr);

        // 1. Pure White Card Background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, displayWidth, totalHeight);

        // 2. Header Badges: Question Number & Points
        let curY = padding;

        // Subtle Royal Indigo Pill for Question tag
        ctx.fillStyle = '#eef2ff'; // Indigo 50
        this.drawRoundedRect(ctx, padding, curY, 115, 28, 6, true, false);
        ctx.fillStyle = '#4338ca'; // Indigo 700
        ctx.font = 'bold 12px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillText(`QUESTION ${question.index}`, padding + 14, curY + 18);

        // Subtle slate Pill for Points
        ctx.fillStyle = '#f8fafc'; // Slate 50
        this.drawRoundedRect(ctx, padding + 125, curY, 80, 28, 6, true, true, '#e2e8f0');
        ctx.fillStyle = '#475569'; // Slate 600
        ctx.font = '600 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillText(`${question.points} POINT${question.points > 1 ? 'S' : ''}`, padding + 137, curY + 18);

        curY += headerHeight;

        // 3. Question Prompt Text in Slate-900 (#0F172A)
        ctx.fillStyle = '#0f172a';
        ctx.font = '600 18px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        for (const line of promptLines) {
            ctx.fillText(line, padding, curY);
            curY += promptLineHeight;
        }

        // 4. Code Snippet Pixel Buffer Rendering
        if (question.code_snippet) {
            curY += 12;
            const codeX = padding;
            const codeW = contentWidth;
            const codeH = codeHeight;

            // Terminal container
            ctx.fillStyle = '#0f172a'; // Deep slate 900
            this.drawRoundedRect(ctx, codeX, curY, codeW, codeH, 10, true, true, '#1e293b');

            // Window dots (Subtle muted red, amber, green)
            ctx.fillStyle = '#f87171';
            this.drawCircle(ctx, codeX + 16, curY + 19, 4.5);
            ctx.fillStyle = '#fbbf24';
            this.drawCircle(ctx, codeX + 30, curY + 19, 4.5);
            ctx.fillStyle = '#34d399';
            this.drawCircle(ctx, codeX + 44, curY + 19, 4.5);

            // Language badge
            ctx.fillStyle = '#94a3b8';
            ctx.font = 'bold 11px "JetBrains Mono", monospace';
            ctx.fillText((question.language || 'code').toUpperCase(), codeX + codeW - 60, curY + 23);

            // Divider line
            ctx.strokeStyle = '#1e293b';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(codeX, curY + 38);
            ctx.lineTo(codeX + codeW, curY + 38);
            ctx.stroke();

            // Code lines rendering with syntax accents
            let codeLineY = curY + 60;
            const codeLineHeight = 22;

            for (let i = 0; i < codeLines.length; i++) {
                const lineNum = (i + 1).toString().padStart(2, ' ');
                const lineContent = codeLines[i];

                // Gutter line number
                ctx.fillStyle = '#475569';
                ctx.font = '13px "JetBrains Mono", Consolas, monospace';
                ctx.fillText(lineNum, codeX + 16, codeLineY);

                // Code line text
                this.renderSyntaxLine(ctx, lineContent, codeX + 50, codeLineY);
                codeLineY += codeLineHeight;
            }
        }
    }

    renderSyntaxLine(ctx, line, x, y) {
        ctx.font = '13px "JetBrains Mono", Consolas, monospace';
        const keywords = ['function', 'class', 'public', 'private', 'return', 'const', 'let', 'var', 'if', 'else', 'async', 'await', 'import', 'from', 'new', 'try', 'catch', 'throw', 'foreach', 'as', 'use'];

        const parts = line.split(/(\s+|[(){}[\];,.<>=!+\-*/&|:])/);
        let curX = x;

        for (const part of parts) {
            if (!part) continue;

            if (keywords.includes(part.trim())) {
                ctx.fillStyle = '#38bdf8'; // Sky blue keyword
            } else if (/^['"].*['"]$/.test(part.trim()) || part.includes("'") || part.includes('"')) {
                ctx.fillStyle = '#34d399'; // Emerald string
            } else if (/^\d+$/.test(part.trim())) {
                ctx.fillStyle = '#fbbf24'; // Amber number
            } else if (part.startsWith('//') || part.startsWith('#')) {
                ctx.fillStyle = '#64748b'; // Slate comment
            } else if (/[(){}[\];,]/.test(part)) {
                ctx.fillStyle = '#94a3b8'; // Grey punctuation
            } else {
                ctx.fillStyle = '#f8fafc'; // Crisp white text
            }

            ctx.fillText(part, curX, y);
            curX += ctx.measureText(part).width;
        }
    }

    wrapText(ctx, text, maxWidth, font) {
        ctx.font = font;
        const words = text.split(' ');
        const lines = [];
        let currentLine = words[0] || '';

        for (let i = 1; i < words.length; i++) {
            const word = words[i];
            const width = ctx.measureText(currentLine + ' ' + word).width;
            if (width < maxWidth) {
                currentLine += ' ' + word;
            } else {
                lines.push(currentLine);
                currentLine = word;
            }
        }
        if (currentLine) lines.push(currentLine);
        return lines;
    }

    drawRoundedRect(ctx, x, y, width, height, radius, fill = true, stroke = false, strokeColor = '#cbd5e1') {
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
        if (fill) ctx.fill();
        if (stroke) {
            ctx.strokeStyle = strokeColor;
            ctx.lineWidth = 1;
            ctx.stroke();
        }
    }

    drawCircle(ctx, x, y, radius) {
        ctx.beginPath();
        ctx.arc(x, y, radius, 0, Math.PI * 2);
        ctx.fill();
    }

    /**
     * 3. Robust Client-Side Enforcement Listeners
     */
    setupSecurityListeners() {
        document.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            this.flagInfraction('context_menu_attempt', 'low', 'Right-click context menu prevented.');
            return false;
        });

        document.addEventListener('selectstart', (e) => {
            if (!['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
                e.preventDefault();
                return false;
            }
        });

        document.addEventListener('dragstart', (e) => {
            e.preventDefault();
            return false;
        });

        ['copy', 'cut', 'paste'].forEach((eventName) => {
            document.addEventListener(eventName, (e) => {
                e.preventDefault();
                this.flagInfraction('tamper_attempt', 'medium', `Clipboard action (${eventName}) intercepted.`);
                return false;
            });
        });

        window.addEventListener('keydown', (e) => {
            const isCtrlOrCmd = e.ctrlKey || e.metaKey;

            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                this.flagInfraction('inspect_shortcut_pressed', 'high', 'F12 Developer Tools shortcut pressed.');
                return false;
            }

            if (isCtrlOrCmd && e.shiftKey && ['I', 'i', 'J', 'j', 'C', 'c'].includes(e.key)) {
                e.preventDefault();
                this.flagInfraction('inspect_shortcut_pressed', 'high', `Shortcut ${e.key.toUpperCase()} DevTools intercepted.`);
                return false;
            }

            if (isCtrlOrCmd && ['U', 'u'].includes(e.key)) {
                e.preventDefault();
                this.flagInfraction('inspect_shortcut_pressed', 'medium', 'View Source shortcut intercepted.');
                return false;
            }

            if (isCtrlOrCmd && ['S', 's', 'P', 'p'].includes(e.key)) {
                e.preventDefault();
                this.flagInfraction('tamper_attempt', 'low', `Shortcut ${e.key.toUpperCase()} prevented.`);
                return false;
            }
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.flagInfraction('visibility_hidden', 'high', 'Candidate switched away from examination tab.');
            }
        });

        window.addEventListener('blur', () => {
            this.flagInfraction('window_blur', 'high', 'Window lost focus to another desktop application or monitor.');
        });

        if (this.enforceFullscreen) {
            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement) {
                    this.flagInfraction('fullscreen_exit', 'high', 'Candidate exited fullscreen mode.');
                    this.showFullscreenRecoveryPrompt();
                }
            });
        }
    }

    setupDevToolsDetection() {
        const threshold = 160;
        let isDevToolsOpen = false;

        const checkDimensions = () => {
            const widthDiff = window.outerWidth - window.innerWidth > threshold;
            const heightDiff = window.outerHeight - window.innerHeight > threshold;

            if ((widthDiff || heightDiff) && !isDevToolsOpen) {
                isDevToolsOpen = true;
                this.flagInfraction('devtools_docked', 'critical', 'DevTools docked panel detected via window boundary shift.');
            } else if (!widthDiff && !heightDiff) {
                isDevToolsOpen = false;
            }
        };

        window.addEventListener('resize', checkDimensions);
        setInterval(checkDimensions, 2000);

        const element = new Image();
        Object.defineProperty(element, 'id', {
            get: () => {
                this.flagInfraction('devtools_opened', 'critical', 'Developer console inspection trap triggered.');
            }
        });
        setInterval(() => {
            console.log('%c', element);
            console.clear();
        }, 5000);
    }

    requestFullscreen() {
        const elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen().catch(err => console.log('Fullscreen failed:', err));
        }
    }

    showFullscreenRecoveryPrompt() {
        if (this.isTerminated) return;
        const modal = document.getElementById('fullscreen-recovery-modal');
        if (modal) modal.classList.remove('hidden');
    }

    async flagInfraction(type, severity, details) {
        if (this.isTerminated || this.isSubmitted) return;

        const now = Date.now();
        if (now - this.lastInfractionTime < 2500) {
            return;
        }
        this.lastInfractionTime = now;

        try {
            const response = await fetch(`${this.apiUrl}/infraction/${this.token}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken || '',
                },
                body: JSON.stringify({
                    infraction_type: type,
                    severity: severity,
                    details: details,
                    client_timestamp: new Date().toISOString(),
                })
            });

            const data = await response.json();

            this.strikesCount = data.strikes_count;
            this.updateStrikesBadge();

            if (data.terminated) {
                this.triggerDisqualification(data.reason || 'Exceeded maximum permitted anti-cheat infraction strikes.');
            } else {
                this.showWarningAlert(data.warning_message || `Warning Strike ${this.strikesCount}/${this.maxStrikes}: Please return to exam window.`);
            }
        } catch (err) {
            console.error('Infraction telemetry dispatch error:', err);
        }
    }

    triggerDisqualification(reason) {
        this.isTerminated = true;
        clearInterval(this.heartbeatTimer);
        clearInterval(this.countdownTimer);

        const examArea = document.getElementById('exam-active-area');
        if (examArea) examArea.style.display = 'none';

        const lockout = document.getElementById('proctor-lockout-screen');
        if (lockout) {
            lockout.classList.remove('hidden');
            const reasonElem = document.getElementById('lockout-reason-text');
            if (reasonElem) reasonElem.innerText = reason;
        }

        setTimeout(() => {
            window.location.href = this.resultUrl;
        }, 3500);
    }

    showWarningAlert(message) {
        const alertBox = document.getElementById('proctor-warning-alert');
        const alertMsg = document.getElementById('proctor-warning-msg');
        if (alertBox && alertMsg) {
            alertMsg.innerText = message;
            alertBox.classList.remove('hidden');
            setTimeout(() => {
                alertBox.classList.add('hidden');
            }, 6000);
        }
    }

    updateStrikesBadge() {
        const badge = document.getElementById('strikes-counter-badge');
        if (badge) {
            badge.innerText = `${this.strikesCount} of ${this.maxStrikes}`;
            if (this.strikesCount >= this.maxStrikes - 1 && this.strikesCount > 0) {
                badge.className = 'px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-bold text-xs font-mono animate-pulse';
            } else if (this.strikesCount > 0) {
                badge.className = 'px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-bold text-xs font-mono';
            } else {
                badge.className = 'px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 font-semibold text-xs font-mono';
            }
        }
    }

    async loadExamData() {
        try {
            const res = await fetch(`${this.apiUrl}/canvas-data/${this.token}`);
            const data = await res.json();

            if (data.error) {
                if (data.status === 'terminated_cheat') {
                    this.triggerDisqualification('Your session was previously disqualified.');
                    return;
                }
                alert(data.error);
                return;
            }

            this.questions = data.questions || [];
            this.remainingSeconds = data.remaining_seconds;
            this.maxStrikes = data.max_strikes;
            this.strikesCount = data.strikes_count;
            this.answers = data.saved_answers || {};

            this.updateStrikesBadge();
            this.renderQuestionNav();
            this.loadQuestion(0);
        } catch (e) {
            console.error('Error fetching exam canvas data:', e);
        }
    }

    loadQuestion(index) {
        if (!this.questions[index]) return;
        this.currentIndex = index;

        const q = this.questions[index];

        // 1. Render question text & code on Canvas
        this.renderQuestionOnCanvas(q);

        // 2. Render options buttons in DOM (Clean Light SaaS Interactive Buttons)
        this.renderOptions(q);

        // 3. Update Question indicator
        const counter = document.getElementById('current-question-num');
        if (counter) {
            const currentStr = String(index + 1).padStart(2, '0');
            const totalStr = String(this.questions.length).padStart(2, '0');
            counter.innerText = `Question ${currentStr} of ${totalStr}`;
        }

        // 4. Update Nav Buttons
        this.updateNavButtons();
    }

    /**
     * Clean Light Mode Multiple Choice Interactive Selectors
     */
    renderOptions(question) {
        const container = document.getElementById('question-options-list');
        if (!container) return;

        container.innerHTML = '';
        const currentAnswer = this.answers[question.id];

        const letters = ['A', 'B', 'C', 'D', 'E', 'F'];

        question.options.forEach((opt, idx) => {
            const letter = letters[idx] || (idx + 1);
            const isSelected = currentAnswer == opt.id;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `group w-full text-left p-4 md:p-5 rounded-2xl border transition-all duration-200 ease-out flex items-center justify-between gap-4 select-none ${
                isSelected
                    ? 'border-indigo-600 bg-indigo-50/60 text-indigo-950 font-medium ring-2 ring-indigo-600/20 shadow-sm'
                    : 'border-slate-200/80 bg-white hover:-translate-y-0.5 hover:shadow-md hover:border-indigo-300 text-slate-800 shadow-[0_2px_8px_rgba(0,0,0,0.04)]'
            }`;

            btn.innerHTML = `
                <div class="flex items-center gap-3.5 flex-1 min-w-0">
                    <span class="flex-shrink-0 w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs transition-colors duration-200 ${
                        isSelected
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'bg-slate-100 text-slate-600 border border-slate-200 group-hover:bg-indigo-50 group-hover:text-indigo-600 group-hover:border-indigo-200'
                    }">${letter}</span>
                    <span class="text-xs md:text-sm leading-relaxed">${opt.text}</span>
                </div>
                <div class="flex-shrink-0 flex items-center justify-center">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center transition-all duration-200 ${
                        isSelected
                            ? 'bg-indigo-600 text-white scale-100 opacity-100 shadow-sm'
                            : 'border-2 border-slate-200 text-transparent scale-90 opacity-40 group-hover:border-indigo-300'
                    }">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </div>
            `;

            btn.onclick = () => {
                this.answers[question.id] = opt.id;
                this.renderOptions(question);
                this.renderQuestionNav();
            };

            container.appendChild(btn);
        });
    }

    renderQuestionNav() {
        const grid = document.getElementById('question-palette-grid');
        if (!grid) return;

        grid.innerHTML = '';
        this.questions.forEach((q, idx) => {
            const isAnswered = this.answers[q.id] !== undefined;
            const isCurrent = this.currentIndex === idx;

            const item = document.createElement('button');
            item.type = 'button';
            item.className = `w-9 h-9 rounded-lg font-bold text-xs flex items-center justify-center transition ${
                isCurrent
                    ? 'bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-300'
                    : isAnswered
                    ? 'bg-emerald-600 text-white'
                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200/80'
            }`;
            item.innerText = idx + 1;
            item.onclick = () => this.loadQuestion(idx);
            grid.appendChild(item);
        });
    }

    setupNavigationControls() {
        const prevBtn = document.getElementById('prev-question-btn');
        const nextBtn = document.getElementById('next-question-btn');
        const submitBtn = document.getElementById('submit-exam-btn');

        if (prevBtn) {
            prevBtn.onclick = () => {
                if (this.currentIndex > 0) this.loadQuestion(this.currentIndex - 1);
            };
        }

        if (nextBtn) {
            nextBtn.onclick = () => {
                if (this.currentIndex < this.questions.length - 1) this.loadQuestion(this.currentIndex + 1);
            };
        }

        if (submitBtn) {
            submitBtn.onclick = () => {
                const total = this.questions.length;
                const answered = Object.keys(this.answers).length;
                const confirmMsg = `Are you sure you want to finish and submit your exam?\n\nAnswered: ${answered} of ${total} questions.`;

                if (confirm(confirmMsg)) {
                    this.submitFinalExam();
                }
            };
        }
    }

    updateNavButtons() {
        const prevBtn = document.getElementById('prev-question-btn');
        const nextBtn = document.getElementById('next-question-btn');

        if (prevBtn) prevBtn.disabled = this.currentIndex === 0;
        if (nextBtn) {
            if (this.currentIndex === this.questions.length - 1) {
                nextBtn.classList.add('hidden');
            } else {
                nextBtn.classList.remove('hidden');
            }
        }
    }

    /**
     * Authoritative Server Countdown Timer & Heartbeat
     */
    startCountdownTimer() {
        const timerElem = document.getElementById('timer-display');
        const timerBadge = document.getElementById('timer-badge-container');

        const updateDisplay = () => {
            if (this.remainingSeconds <= 0) {
                if (timerElem) timerElem.innerText = '00:00:00';
                this.submitFinalExam('Time expired. Submitting examination automatically.');
                return;
            }

            const hours = Math.floor(this.remainingSeconds / 3600);
            const minutes = Math.floor((this.remainingSeconds % 3600) / 60);
            const seconds = this.remainingSeconds % 60;

            const format = (n) => n.toString().padStart(2, '0');
            if (timerElem) {
                timerElem.innerText = `${format(hours)}:${format(minutes)}:${format(seconds)}`;

                // Shifts to warm amber alert only when under 5 minutes remain
                if (this.remainingSeconds <= 300) {
                    timerElem.classList.remove('text-slate-900');
                    timerElem.classList.add('text-amber-600', 'animate-pulse');
                    if (timerBadge) {
                        timerBadge.classList.add('bg-amber-50', 'border-amber-200', 'ring-2', 'ring-amber-500/10');
                        timerBadge.classList.remove('bg-slate-50', 'border-slate-200');
                    }
                } else {
                    timerElem.classList.remove('text-amber-600', 'animate-pulse');
                    timerElem.classList.add('text-slate-900');
                    if (timerBadge) {
                        timerBadge.classList.remove('bg-amber-50', 'border-amber-200', 'ring-2', 'ring-amber-500/10');
                        timerBadge.classList.add('bg-slate-50', 'border-slate-200');
                    }
                }
            }

            this.remainingSeconds--;
        };

        updateDisplay();
        this.countdownTimer = setInterval(updateDisplay, 1000);
    }

    startHeartbeat() {
        this.heartbeatTimer = setInterval(async () => {
            if (this.isTerminated || this.isSubmitted) return;

            try {
                const res = await fetch(`${this.apiUrl}/heartbeat/${this.token}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken || '',
                    },
                    body: JSON.stringify({ answers: this.answers })
                });

                const data = await res.json();

                if (data.terminated) {
                    this.triggerDisqualification(data.reason);
                } else if (!data.active && data.status === 'completed') {
                    window.location.href = data.redirect_url;
                } else if (data.remaining_seconds !== undefined) {
                    this.remainingSeconds = data.remaining_seconds;
                    this.strikesCount = data.strikes_count;
                    this.updateStrikesBadge();
                }
            } catch (err) {
                console.warn('Heartbeat synchronization pending...');
            }
        }, 10000);
    }

    submitFinalExam(notice = null) {
        if (this.isSubmitted) return;
        this.isSubmitted = true;

        clearInterval(this.heartbeatTimer);
        clearInterval(this.countdownTimer);

        if (notice) alert(notice);

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.submitUrl;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = this.csrfToken || '';
        form.appendChild(csrfInput);

        const answersInput = document.createElement('input');
        answersInput.type = 'hidden';
        answersInput.name = 'answers';
        answersInput.value = JSON.stringify(this.answers);
        form.appendChild(answersInput);

        document.body.appendChild(form);
        form.submit();
    }
}

window.ProctorEngine = ProctorEngine;
