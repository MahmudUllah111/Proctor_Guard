@extends('layouts.public')

@section('content')
<div class="space-y-16 py-8">

    <!-- Render Dynamic Content Widgets -->
    @if(!empty($page->content_widgets) && is_array($page->content_widgets))
        @foreach($page->content_widgets as $wIdx => $widget)
            @php $type = $widget['type'] ?? ''; @endphp

            {{-- 1. Hero Widget --}}
            @if($type === 'hero')
                <section class="max-w-7xl mx-auto px-6 py-12 md:py-20 relative overflow-hidden text-center animate-fade-up">
                    <div class="max-w-3xl mx-auto space-y-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 text-indigo-700 font-semibold text-xs border border-indigo-200/80 shadow-soft">
                            <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                            National Proctored Examination Center
                        </div>
                        <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            {{ $widget['headline'] ?? 'Enterprise Assessment Portal' }}
                        </h1>
                        <p class="text-sm md:text-base text-slate-600 leading-relaxed max-w-2xl mx-auto">
                            {{ $widget['subhead'] ?? 'Secure online assessments engineered with HTML5 canvas pixel buffer rendering and forensic watermarking to protect examination integrity.' }}
                        </p>
                        <div class="pt-2 flex flex-wrap items-center justify-center gap-4">
                            <a href="#active-exams" class="px-6 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm hover:shadow-md transition-all duration-200 active:scale-95">
                                {{ $widget['button_text'] ?? 'View Scheduled Exams' }} &rarr;
                            </a>
                            <a href="{{ route('public.page', 'rules') }}" class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs border border-slate-200/80 shadow-soft transition-all duration-200">
                                Review Integrity Code
                            </a>
                        </div>
                    </div>
                </section>

            {{-- 2. Features Grid Widget --}}
            @elseif($type === 'features')
                <section class="max-w-7xl mx-auto px-6 py-10 animate-fade-up delay-150">
                    @if(!empty($widget['title']))
                        <div class="text-center mb-12">
                            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $widget['title'] }}</h2>
                            <p class="text-xs text-slate-500 mt-2">Next-generation multi-layer proctoring defenses ensuring equity</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @php
                            $rawItems = $widget['raw_items'] ?? '';
                            $lines = array_filter(explode("\n", str_replace("\r", "", $rawItems)));
                        @endphp
                        @foreach($lines as $fIdx => $line)
                            @php
                                $parts = explode('|', $line);
                                $featTitle = trim($parts[0] ?? 'Feature');
                                $featDesc = trim($parts[1] ?? '');
                            @endphp
                            <div class="stagger-enter stagger-{{ ($fIdx % 6) + 1 }} p-6 rounded-2xl bg-white border border-slate-200/80 shadow-[0_2px_8px_rgba(0,0,0,0.04)] hover:-translate-y-0.5 hover:shadow-md hover:border-indigo-300 transition-all duration-200 ease-out space-y-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <h3 class="font-bold text-base text-slate-900">{{ $featTitle }}</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $featDesc }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

            {{-- 3. Rich Text & HTML Widget --}}
            @elseif($type === 'text_html')
                <section class="max-w-4xl mx-auto px-6 py-8 animate-fade-up">
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-8 shadow-soft">
                        <div class="prose prose-slate prose-indigo max-w-none text-xs leading-relaxed">
                            {!! $widget['content'] ?? '' !!}
                        </div>
                    </div>
                </section>

            {{-- 4. CTA Banner Widget (Student Support & Guidance) --}}
            @elseif($type === 'cta')
                <section class="max-w-7xl mx-auto px-6 py-10 animate-fade-up">
                    <div class="p-10 rounded-3xl bg-indigo-50/80 border border-indigo-100 flex flex-col md:flex-row items-center justify-between gap-6 shadow-soft">
                        <div>
                            <h3 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">{{ $widget['title'] ?? 'Need Testing Accommodations or Candidate Support?' }}</h3>
                            <p class="text-xs text-slate-600 mt-1 max-w-xl leading-relaxed">{{ $widget['message'] ?? 'Reach out to the University Examination Board for accommodation requests, hardware checks, or scheduling questions.' }}</p>
                        </div>
                        <a href="#candidate-support" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm hover:shadow-md transition-all duration-200 flex-shrink-0 active:scale-95">
                            {{ $widget['button_text'] ?? 'Submit Candidate Inquiry' }} &rarr;
                        </a>
                    </div>
                </section>

            {{-- 5. Accordion Widget (FAQ / Rules) --}}
            @elseif($type === 'accordion')
                <section class="max-w-4xl mx-auto px-6 py-10 space-y-4 animate-fade-up">
                    @if(!empty($widget['title']))
                        <div class="text-center mb-8">
                            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $widget['title'] }}</h2>
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
                        <details class="group bg-white border border-slate-200/80 rounded-2xl p-5 shadow-soft hover:border-slate-300 transition [&_summary::-webkit-details-marker]:hidden cursor-pointer">
                            <summary class="flex items-center justify-between font-bold text-xs text-slate-900">
                                <span>{{ $qTitle }}</span>
                                <span class="transition-transform duration-200 group-open:rotate-180 text-indigo-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                            </summary>
                            <p class="mt-3 text-xs text-slate-600 leading-relaxed pt-3 border-t border-slate-100">
                                {{ $qAns }}
                            </p>
                        </details>
                    @endforeach
                </section>

            {{-- 6. Standalone Form / Lead Embed Widget --}}
            @elseif($type === 'form_embed')
                <section id="candidate-support" class="max-w-3xl mx-auto px-6 py-10 animate-fade-up">
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-soft space-y-6">
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-semibold mb-2">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Candidate Support Center
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">{{ $widget['heading'] ?? 'Candidate Inquiry & Accommodation Request' }}</h3>
                            <p class="text-xs text-slate-500 mt-1">Submit inquiries regarding examination scheduling, accommodations, or technical requirements.</p>
                        </div>

                        @if(session('success'))
                            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('public.lead.submit') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="form_name" value="{{ $widget['form_name'] ?? 'contact' }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Candidate Full Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="student_or_lead_name" required placeholder="Jane Doe"
                                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Institutional Email <span class="text-rose-500">*</span></label>
                                    <input type="email" name="email" required placeholder="j.doe@university.edu"
                                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Phone (Optional)</label>
                                    <input type="text" name="phone" placeholder="+1 (555) 000-0000"
                                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Subject</label>
                                    <input type="text" name="subject" placeholder="Examination inquiry..."
                                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Message / Inquiry <span class="text-rose-500">*</span></label>
                                <textarea name="message" rows="4" required placeholder="Describe your question or testing accommodation request..."
                                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"></textarea>
                            </div>

                            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow-md transition-all duration-200 active:scale-95">
                                Submit Inquiry &rarr;
                            </button>
                        </form>
                    </div>
                </section>
            @endif

        @endforeach
    @endif

    {{-- Scheduled Assessments Section --}}
    @if(isset($activeExams) && $activeExams->count() > 0)
        <section id="active-exams" class="max-w-7xl mx-auto px-6 py-10 animate-fade-up delay-200">
            <div class="text-center mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Examination Schedule</span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Available Proctored Assessments</h2>
                <p class="text-xs text-slate-500 mt-1">Select your enrolled examination below to verify your seat in the synchronized waiting room</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activeExams as $exam)
                    <div class="stagger-enter stagger-{{ ($loop->index % 6) + 1 }} p-6 rounded-2xl bg-white border border-slate-200/80 shadow-[0_2px_8px_rgba(0,0,0,0.04)] hover:-translate-y-0.5 hover:shadow-md hover:border-indigo-300 transition-all duration-200 ease-out flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-bold border border-slate-200/60">{{ $exam->course_code ?? 'EXAM' }}</span>
                                @if($exam->is_released)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold text-[10px] flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                        Questions Released
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-semibold text-[10px] flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Waiting Room Open
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-bold text-base text-slate-900 mb-2 leading-snug">{{ $exam->title }}</h3>
                            <p class="text-xs text-slate-600 line-clamp-2 mb-6">{{ $exam->description }}</p>

                            <div class="grid grid-cols-3 gap-2 text-center text-xs mb-6">
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                                    <span class="text-[10px] text-slate-500 block uppercase font-medium">Duration</span>
                                    <span class="font-bold text-slate-900">{{ $exam->duration_minutes }}m</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                                    <span class="text-[10px] text-slate-500 block uppercase font-medium">Passing</span>
                                    <span class="font-bold text-emerald-700">{{ $exam->pass_percentage }}%</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                                    <span class="text-[10px] text-slate-500 block uppercase font-medium">Strikes</span>
                                    <span class="font-bold text-rose-700">{{ $exam->max_strikes }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('student.entry', $exam) }}" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow-md text-center transition-all duration-200 flex items-center justify-center gap-1.5 active:scale-95">
                            Enter Assessment Portal &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection
