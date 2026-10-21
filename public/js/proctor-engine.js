/**
 * Enterprise Anti-Cheat Proctoring Engine & HTML5 Canvas Renderer
 * Architecture:
 *  1. Dynamic Watermark Canvas Layer (Timestamp, Name, ID, IP)
 *  2. Question & Code Snippet Pixel Buffer Canvas Renderer
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
        this.watermarkOpacity = config.watermarkOpacity || 0.12;
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
     * 1. HTML5 Canvas Dynamic Semi-Transparent Watermark Layer
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

            ctx.font = 'bold 15px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, monospace';
            ctx.fillStyle = '#0f172a';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            const stepX = 360;
            const stepY = 180;
            const angle = -25 * Math.PI / 180;

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
     * 2. HTML5 Canvas Question & Code Snippet Pixel Buffer Renderer
     * Completely prevents browser AI extensions (ChatGPT, Monica, Copilot) from scraping DOM text!
     */
    renderQuestionOnCanvas(question) {
        const container = document.getElementById('canvas-question-container');
        if (!container) return;

        container.innerHTML = '';

        const canvas = document.createElement('canvas');
        canvas.id = 'active-question-canvas';
        canvas.className = 'w-full rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900';
        container.appendChild(canvas);

        const ctx = canvas.getContext('2d');
        const dpr = window.devicePixelRatio || 1;

        // Container display width
        const displayWidth = container.clientWidth || 800;

        // Prepare text wrapping & measure heights
        const padding = 28;
        const contentWidth = displayWidth - (padding * 2);

        // Calculate prompt lines
        const promptLines = this.wrapText(ctx, question.prompt, contentWidth, '600 18px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif');
        const promptLineHeight = 28;
        const promptHeight = promptLines.length * promptLineHeight;

        // Code snippet dimensions if present
        let codeHeight = 0;
        let codeLines = [];
        if (question.code_snippet) {
            codeLines = question.code_snippet.trim().split('\n');
            const codeLineHeight = 22;
            const codeHeaderHeight = 36;
            const codePadding = 24;
            codeHeight = codeHeaderHeight + (codeLines.length * codeLineHeight) + codePadding;
        }

        // Header info bar
        const headerHeight = 44;
        const totalHeight = headerHeight + promptHeight + (codeHeight ? codeHeight + 24 : 0) + (padding * 2);

        // Set high-dpi canvas size
        canvas.width = displayWidth * dpr;
        canvas.height = totalHeight * dpr;
        canvas.style.width = `${displayWidth}px`;
        canvas.style.height = `${totalHeight}px`;

        ctx.scale(dpr, dpr);

        // 1. Background fill
        const isDark = document.documentElement.classList.contains('dark');
        ctx.fillStyle = isDark ? '#0f172a' : '#ffffff';
        ctx.fillRect(0, 0, displayWidth, totalHeight);

        // 2. Header Badge: Question Number & Points
        let curY = padding;

        // Question tag
        ctx.fillStyle = '#6366f1'; // Indigo 500
        this.drawRoundedRect(ctx, padding, curY, 110, 28, 6, true, false);
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 12px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillText(`QUESTION ${question.index}`, padding + 12, curY + 18);

        // Points tag
        ctx.fillStyle = isDark ? '#334155' : '#f1f5f9';
        this.drawRoundedRect(ctx, padding + 120, curY, 80, 28, 6, true, false);
        ctx.fillStyle = isDark ? '#94a3b8' : '#475569';
        ctx.font = '600 12px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillText(`${question.points} POINT${question.points > 1 ? 'S' : ''}`, padding + 130, curY + 18);

        curY += headerHeight;

        // 3. Question Prompt Text Lines
        ctx.fillStyle = isDark ? '#f8fafc' : '#0f172a';
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

            // Terminal background card
            ctx.fillStyle = '#0b1120'; // Slate 950
            this.drawRoundedRect(ctx, codeX, curY, codeW, codeH, 10, true, false);

            // Terminal window dots (Red, Yellow, Green)
            ctx.fillStyle = '#ef4444';
            this.drawCircle(ctx, codeX + 16, curY + 18, 5);
            ctx.fillStyle = '#f59e0b';
            this.drawCircle(ctx, codeX + 32, curY + 18, 5);
            ctx.fillStyle = '#10b981';
            this.drawCircle(ctx, codeX + 48, curY + 18, 5);

            // Language badge
            ctx.fillStyle = '#64748b';
            ctx.font = 'bold 11px monospace';
            ctx.fillText((question.language || 'code').toUpperCase(), codeX + codeW - 60, curY + 22);

            // Divider line
            ctx.strokeStyle = '#1e293b';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(codeX, curY + 36);
            ctx.lineTo(codeX + codeW, curY + 36);
            ctx.stroke();

            // Code lines rendering with syntax accents
            let codeLineY = curY + 58;
            const codeLineHeight = 22;

            for (let i = 0; i < codeLines.length; i++) {
                const lineNum = (i + 1).toString().padStart(2, ' ');
                const lineContent = codeLines[i];

                // Gutter line number
                ctx.fillStyle = '#475569';
                ctx.font = '13px "JetBrains Mono", Consolas, "Courier New", monospace';
                ctx.fillText(lineNum, codeX + 16, codeLineY);

                // Code line text
                this.renderSyntaxLine(ctx, lineContent, codeX + 50, codeLineY);
                codeLineY += codeLineHeight;
            }
        }
    }

    /**
     * Render line of code with syntax token highlights into canvas pixels.
     */
    renderSyntaxLine(ctx, line, x, y) {
        ctx.font = '13px "JetBrains Mono", Consolas, "Courier New", monospace';
        const keywords = ['function', 'class', 'public', 'private', 'return', 'const', 'let', 'var', 'if', 'else', 'async', 'await', 'import', 'from', 'new', 'try', 'catch', 'throw', 'foreach', 'as', 'use'];

        const parts = line.split(/(\s+|[(){}[\];,.<>=!+\-*/&|:])/);
        let curX = x;

        for (const part of parts) {
            if (!part) continue;

            if (keywords.includes(part.trim())) {
                ctx.fillStyle = '#38bdf8'; // Sky blue keyword
            } else if (/^['"].*['"]$/.test(part.trim()) || part.includes("'") || part.includes('"')) {
                ctx.fillStyle = '#34d399'; // Emerald green string
            } else if (/^\d+$/.test(part.trim())) {
                ctx.fillStyle = '#f59e0b'; // Amber number
            } else if (part.startsWith('//') || part.startsWith('#')) {
                ctx.fillStyle = '#64748b'; // Slate comment
            } else if (/[(){}[\];,]/.test(part)) {
                ctx.fillStyle = '#94a3b8'; // Grey punctuation
            } else {
                ctx.fillStyle = '#e2e8f0'; // Light text
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

    drawRoundedRect(ctx, x, y, width, height, radius, fill = true, stroke = false) {
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
        if (stroke) ctx.stroke();
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
        // Prevent Right-Click Context Menu
        document.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            this.flagInfraction('context_menu_attempt', 'low', 'Right-click context menu prevented.');
            return false;
        });

        // Prevent Text Selection
        document.addEventListener('selectstart', (e) => {
            // Allow selection inside input fields if any, otherwise block
            if (!['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
                e.preventDefault();
                return false;
            }
        });

        // Prevent Dragging
        document.addEventListener('dragstart', (e) => {
            e.preventDefault();
            return false;
        });

        // Prevent Copy, Cut, Paste
        ['copy', 'cut', 'paste'].forEach((eventName) => {
            document.addEventListener(eventName, (e) => {
                e.preventDefault();
                this.flagInfraction('tamper_attempt', 'medium', `Clipboard action (${eventName}) intercepted.`);
                return false;
            });
        });

        // Prevent Keyboard Inspection Shortcuts
        window.addEventListener('keydown', (e) => {
            const isCtrlOrCmd = e.ctrlKey || e.metaKey;

            // F12
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                this.flagInfraction('inspect_shortcut_pressed', 'high', 'F12 Developer Tools shortcut pressed.');
                return false;
            }

            // Ctrl/Cmd + Shift + I/J/C (DevTools)
            if (isCtrlOrCmd && e.shiftKey && ['I', 'i', 'J', 'j', 'C', 'c'].includes(e.key)) {
                e.preventDefault();
                this.flagInfraction('inspect_shortcut_pressed', 'high', `Shortcut ${e.key.toUpperCase()} DevTools intercepted.`);
                return false;
            }

            // Ctrl/Cmd + U (View Source)
            if (isCtrlOrCmd && ['U', 'u'].includes(e.key)) {
                e.preventDefault();
                this.flagInfraction('inspect_shortcut_pressed', 'medium', 'View Source shortcut intercepted.');
                return false;
            }

            // Ctrl/Cmd + S / P
            if (isCtrlOrCmd && ['S', 's', 'P', 'p'].includes(e.key)) {
                e.preventDefault();
                this.flagInfraction('tamper_attempt', 'low', `Shortcut ${e.key.toUpperCase()} prevented.`);
                return false;
            }
        });

        // Visibility Change Listener (Tab Switch or Minimized)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.flagInfraction('visibility_hidden', 'high', 'Candidate switched away from examination tab.');
            }
        });

        // Window Blur Listener (Application Focus Lost)
        window.addEventListener('blur', () => {
            this.flagInfraction('window_blur', 'high', 'Window lost focus to another desktop application or monitor.');
        });

        // Fullscreen Exit Detection
        if (this.enforceFullscreen) {
            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement) {
                    this.flagInfraction('fullscreen_exit', 'high', 'Candidate exited fullscreen mode.');
                    this.showFullscreenRecoveryPrompt();
                }
            });
        }
    }

    /**
     * DevTools Docking & Opening Detection
     */
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

        // Console Trap / Debugger Timing Check
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

    /**
     * Request Fullscreen Helper
     */
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

    /**
     * Dispatch Infraction Event to Server & Handle Strikes
     */
    async flagInfraction(type, severity, details) {
        if (this.isTerminated || this.isSubmitted) return;

        // Throttle identical rapid-fire events (within 2.5s)
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

    /**
     * Red-Screen Lockout & Automatic Zero-Score Termination
     */
    triggerDisqualification(reason) {
        this.isTerminated = true;
        clearInterval(this.heartbeatTimer);
        clearInterval(this.countdownTimer);

        // Hide exam wrapper
        const examArea = document.getElementById('exam-active-area');
        if (examArea) examArea.style.display = 'none';

        // Render full-screen Red Security Lockout Banner
        const lockout = document.getElementById('proctor-lockout-screen');
        if (lockout) {
            lockout.classList.remove('hidden');
            const reasonElem = document.getElementById('lockout-reason-text');
            if (reasonElem) reasonElem.innerText = reason;
        }

        // Auto-redirect to result after 4 seconds
        setTimeout(() => {
            window.location.href = this.resultUrl;
        }, 4000);
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
            badge.innerText = `${this.strikesCount} / ${this.maxStrikes}`;
            if (this.strikesCount >= this.maxStrikes - 1) {
                badge.className = 'px-3 py-1 bg-red-600 text-white rounded-full font-bold text-xs animate-pulse';
            } else if (this.strikesCount > 0) {
                badge.className = 'px-3 py-1 bg-amber-500 text-white rounded-full font-bold text-xs';
            }
        }
    }

    /**
     * Load Question Data from Backend
     */
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

        // 2. Render options buttons in DOM
        this.renderOptions(q);

        // 3. Update Question indicator
        const counter = document.getElementById('current-question-num');
        if (counter) counter.innerText = `${index + 1} of ${this.questions.length}`;

        // 4. Update Nav Buttons
        this.updateNavButtons();
    }

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
            btn.className = `w-full text-left p-4 rounded-xl border-2 transition-all flex items-start gap-4 ${
                isSelected
                    ? 'border-indigo-600 bg-indigo-50/80 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200'
                    : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300 dark:hover:border-slate-700 text-slate-700 dark:text-slate-300'
            }`;

            btn.innerHTML = `
                <span class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm ${
                    isSelected ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'
                }">${letter}</span>
                <span class="pt-1 font-medium leading-relaxed">${opt.text}</span>
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
            item.className = `w-9 h-9 rounded-lg font-bold text-xs flex items-center justify-center transition-all ${
                isCurrent
                    ? 'ring-2 ring-indigo-500 bg-indigo-600 text-white'
                    : isAnswered
                    ? 'bg-emerald-500 text-white'
                    : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200'
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

                if (this.remainingSeconds <= 300) {
                    timerElem.classList.add('text-red-600', 'animate-pulse');
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
                    // Sync countdown with authoritative server time
                    this.remainingSeconds = data.remaining_seconds;
                    this.strikesCount = data.strikes_count;
                    this.updateStrikesBadge();
                }
            } catch (err) {
                console.warn('Heartbeat synchronization pending...');
            }
        }, 10000); // Sync every 10 seconds
    }

    /**
     * Submit Examination
     */
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
