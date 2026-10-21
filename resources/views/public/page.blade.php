@extends('layouts.public')

@section('content')
<div class="space-y-16 py-8">

    <!-- Render Dynamic Content Widgets -->
    @if(!empty($page->content_widgets) && is_array($page->content_widgets))
        @foreach($page->content_widgets as $widget)
            @php $type = $widget['type'] ?? ''; @endphp

            {{-- 1. Hero Widget --}}
            @if($type === 'hero')
                <section class="max-w-7xl mx-auto px-6 py-12 md:py-20 relative overflow-hidden text-center">
                    <div class="max-w-3xl mx-auto space-y-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-indigo-500/10 text-indigo-400 font-bold text-xs border border-indigo-500/20">
                            <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                            Zero-Hardcoding Enterprise Assessment Platform
                        </div>
                        <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight leading-tight">
                            {{ $widget['headline'] ?? 'Enterprise Quiz & Examination Platform' }}
                        </h1>
                        <p class="text-sm md:text-base text-slate-400 leading-relaxed max-w-2xl mx-auto">
                            {{ $widget['subhead'] ?? 'Secure online assessments engineered with HTML5 canvas pixel buffer rendering and forensic watermarking to make AI solving and leaking impossible.' }}
                        </p>
                        <div class="pt-2 flex flex-wrap items-center justify-center gap-4">
                            @if(!empty($widget['button_text']))
                                <a href="{{ $widget['button_url'] ?? '#active-exams' }}" class="px-6 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xl shadow-indigo-600/30 transition">
                                    {{ $widget['button_text'] }} &rarr;
                                </a>
                            @endif
                            <a href="{{ route('public.page', 'rules') }}" class="px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 transition">
                                Review Integrity Code
                            </a>
                        </div>
                    </div>
                </section>

            {{-- 2. Features Grid Widget --}}
            @elseif($type === 'features')
                <section class="max-w-7xl mx-auto px-6 py-10">
                    @if(!empty($widget['title']))
                        <div class="text-center mb-12">
                            <h2 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">{{ $widget['title'] }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Next-generation multi-layer proctoring defenses</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @php
                            $rawItems = $widget['raw_items'] ?? '';
                            $lines = array_filter(explode("\n", str_replace("\r", "", $rawItems)));
                        @endphp
                        @foreach($lines as $line)
                            @php
                                $parts = explode('|', $line);
                                $featTitle = trim($parts[0] ?? 'Feature');
                                $featDesc = trim($parts[1] ?? '');
                            @endphp
                            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl hover:border-slate-700 transition space-y-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <h3 class="font-bold text-base text-white">{{ $featTitle }}</h3>
                                <p class="text-xs text-slate-400 leading-relaxed">{{ $featDesc }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

            {{-- 3. Rich Text & HTML Widget --}}
            @elseif($type === 'text_html')
                <section class="max-w-4xl mx-auto px-6 py-8">
                    <div class="prose prose-invert prose-indigo max-w-none text-xs leading-relaxed">
                        {!! $widget['content'] ?? '' !!}
                    </div>
                </section>

            {{-- 4. CTA Banner Widget --}}
            @elseif($type === 'cta')
                <section class="max-w-7xl mx-auto px-6 py-10">
                    <div class="p-10 rounded-3xl bg-gradient-to-r from-indigo-900/60 to-purple-900/60 border border-indigo-700/50 flex flex-col md:flex-row items-center justify-between gap-6 shadow-2xl">
                        <div>
                            <h3 class="text-xl md:text-2xl font-black text-white">{{ $widget['title'] ?? 'Ready to deploy proctored testing?' }}</h3>
                            <p class="text-xs text-indigo-200 mt-1 max-w-xl">{{ $widget['message'] ?? 'Configure examinations, manage question banks, and monitor active sessions in real time.' }}</p>
                        </div>
                        <a href="{{ route('admin.login') }}" class="px-6 py-3 rounded-xl bg-white text-indigo-950 font-extrabold text-xs shadow hover:bg-indigo-50 transition flex-shrink-0">
                            {{ $widget['button_text'] ?? 'Sign in to Admin CMS' }}
                        </a>
                    </div>
                </section>

            {{-- 5. Accordion Widget (FAQ / Rules) --}}
            @elseif($type === 'accordion')
                <section class="max-w-4xl mx-auto px-6 py-10 space-y-4">
                    @if(!empty($widget['title']))
                        <div class="text-center mb-8">
                            <h2 class="text-2xl font-extrabold text-white">{{ $widget['title'] }}</h2>
                        </div>
                    @endif

                    @php
                        $rawItems = $widget['raw_items'] ?? '';
                        $lines = array_filter(explode("\n", str_replace("\r", "", $rawItems)));
                    @endphp
                    @foreach($lines as $qIdx => $line)
                        @php
                            $parts = explode('|', $line);
                            $qTitle = trim($parts[0] ?? '');
                            $qAns = trim($parts[1] ?? '');
                        @endphp
                        <details class="group bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg [&_summary::-webkit-details-marker]:hidden cursor-pointer">
                            <summary class="flex items-center justify-between font-bold text-xs text-white">
                                <span>{{ $qTitle }}</span>
                                <span class="transition group-open:rotate-180 text-indigo-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                            </summary>
                            <p class="mt-3 text-xs text-slate-400 leading-relaxed pt-3 border-t border-slate-800">
                                {{ $qAns }}
                            </p>
                        </details>
                    @endforeach
                </section>

            {{-- 6. Standalone Form / Lead Embed Widget --}}
            @elseif($type === 'form_embed')
                <section class="max-w-3xl mx-auto px-6 py-10">
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $widget['heading'] ?? 'Candidate Inquiry & Support Form' }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Have questions regarding examination scheduling, accommodations, or technical requirements?</p>
                        </div>

                        @if(session('success'))
                            <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-700 text-emerald-300 text-xs font-semibold">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('public.lead.submit') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="form_name" value="{{ $widget['form_name'] ?? 'contact' }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="student_or_lead_name" required placeholder="John Doe"
                                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                                    <input type="email" name="email" required placeholder="j.doe@university.edu"
                                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Phone (Optional)</label>
                                    <input type="text" name="phone" placeholder="+1 (555) 000-0000"
                                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Subject</label>
                                    <input type="text" name="subject" placeholder="Examination inquiry..."
                                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Message / Inquiry <span class="text-rose-500">*</span></label>
                                <textarea name="message" rows="4" required placeholder="Describe your question or testing accommodation request..."
                                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>

                            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg transition">
                                Submit Inquiry &rarr;
                            </button>
                        </form>
                    </div>
                </section>
            @endif

        @endforeach
    @endif

    {{-- Available Assessments Section --}}
    @if(isset($activeExams) && $activeExams->count() > 0)
        <section id="active-exams" class="max-w-7xl mx-auto px-6 py-10">
            <div class="text-center mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400">Scheduled Assessments</span>
                <h2 class="text-3xl font-extrabold text-white mt-1">Available Proctored Examinations</h2>
                <p class="text-xs text-slate-400 mt-1">Select your course examination below to enter the synchronized waiting room</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activeExams as $exam)
                    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl flex flex-col justify-between hover:border-indigo-500/50 transition">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 font-mono text-xs font-bold">{{ $exam->course_code ?? 'EXAM' }}</span>
                                @if($exam->is_released)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-bold text-[10px] flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Released (Active)
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-bold text-[10px]">
                                        Waiting Room
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-extrabold text-base text-white mb-2 leading-snug">{{ $exam->title }}</h3>
                            <p class="text-xs text-slate-400 line-clamp-2 mb-6">{{ $exam->description }}</p>

                            <div class="grid grid-cols-3 gap-2 text-center text-xs mb-6">
                                <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                                    <span class="text-[10px] text-slate-500 block">Duration</span>
                                    <span class="font-bold text-white">{{ $exam->duration_minutes }}m</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                                    <span class="text-[10px] text-slate-500 block">Passing</span>
                                    <span class="font-bold text-emerald-400">{{ $exam->pass_percentage }}%</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                                    <span class="text-[10px] text-slate-500 block">Strikes</span>
                                    <span class="font-bold text-rose-400">{{ $exam->max_strikes }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('student.entry', $exam) }}" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 text-center transition flex items-center justify-center gap-1.5">
                            Enter Assessment Portal &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection
