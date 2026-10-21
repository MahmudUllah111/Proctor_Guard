@extends('layouts.admin')

@section('title', 'System Settings')
@section('page_title', 'System Settings & Dynamic SMTP')
@section('page_subtitle', 'Dynamic runtime mailer, global SEO tags, and anti-cheat defaults without code modifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <!-- Dynamic SMTP Configuration -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h3 class="font-bold text-sm text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Dynamic SMTP Email Configuration
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Dispatches student score reports and instant administrative cheat strike alerts</p>
            </div>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-bold text-[10px]">Zero Code Config</span>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="settings_group" value="smtp">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Mail Driver / Mailer</label>
                    <select name="mail_mailer" class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="smtp" {{ $settings['mail_mailer'] === 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="log" {{ $settings['mail_mailer'] === 'log' ? 'selected' : '' }}>Log (Testing / Local)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">SMTP Host <span class="text-rose-500">*</span></label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">SMTP Port <span class="text-rose-500">*</span></label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">SMTP Username</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}" placeholder="username / api key"
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">SMTP Password</label>
                    <input type="password" name="mail_password" value="{{ old('mail_password', $settings['mail_password']) }}" placeholder="••••••••••••"
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Encryption</label>
                    <select name="mail_encryption" class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="tls" {{ $settings['mail_encryption'] === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ $settings['mail_encryption'] === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ in_array($settings['mail_encryption'], ['none', '', null]) ? 'selected' : '' }}>None</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">From Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">From Sender Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Admin Security Alert Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="admin_alert_email" value="{{ old('admin_alert_email', $settings['admin_alert_email']) }}" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>
            </div>

            <div class="flex items-center justify-end pt-3">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                    Save Dynamic SMTP Settings
                </button>
            </div>
        </form>

        <!-- SMTP Connectivity Test Tool -->
        <div class="pt-5 border-t border-slate-800/80 bg-slate-950 p-4 rounded-xl">
            <h4 class="text-xs font-bold text-white mb-2 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Verify Dynamic SMTP Transmission
            </h4>
            <form action="{{ route('admin.settings.test-email') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3">
                @csrf
                <input type="email" name="test_email" value="{{ auth()->user()->email ?? 'admin@examcms.test' }}" required placeholder="recipient email..."
                    class="w-full sm:w-80 px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-800 hover:bg-slate-700 text-emerald-400 font-bold text-xs rounded-xl border border-slate-700 transition">
                    Send Test Email &rarr;
                </button>
            </form>
        </div>
    </div>

    <!-- Global SEO Configuration -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="border-b border-slate-800 pb-4">
            <h3 class="font-bold text-sm text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Global SEO & Social Graph Metadata
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Default search engine parameters and OpenGraph tags used across all landing pages</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="settings_group" value="seo">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Portal Platform Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Robots Directive</label>
                    <select name="seo_robots" class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="index, follow" {{ $settings['seo_robots'] === 'index, follow' ? 'selected' : '' }}>index, follow (Allow Search Engines)</option>
                        <option value="noindex, nofollow" {{ $settings['seo_robots'] === 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow (Private Assessment Portal)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default Meta Title <span class="text-rose-500">*</span></label>
                <input type="text" name="seo_title_default" value="{{ old('seo_title_default', $settings['seo_title_default']) }}" required
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default Meta Description <span class="text-rose-500">*</span></label>
                <textarea name="seo_description_default" rows="2" required
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">{{ old('seo_description_default', $settings['seo_description_default']) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default Meta Keywords</label>
                    <input type="text" name="seo_keywords_default" value="{{ old('seo_keywords_default', $settings['seo_keywords_default']) }}"
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default OpenGraph Image URL</label>
                    <input type="text" name="seo_og_image" value="{{ old('seo_og_image', $settings['seo_og_image']) }}" placeholder="https://..."
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>
            </div>

            <div class="flex items-center justify-end pt-3">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                    Save Global SEO Parameters
                </button>
            </div>
        </form>
    </div>

    <!-- Proctoring Engine Defaults -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="border-b border-slate-800 pb-4">
            <h3 class="font-bold text-sm text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Proctoring Engine Global Defaults
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Applied automatically to newly created assessments</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="settings_group" value="proctoring">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default Max Strikes</label>
                    <input type="number" name="default_max_strikes" value="{{ old('default_max_strikes', $settings['default_max_strikes']) }}" min="1" max="20" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default Strictness</label>
                    <select name="default_strictness" class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="strict" {{ $settings['default_strictness'] === 'strict' ? 'selected' : '' }}>Strict</option>
                        <option value="medium" {{ $settings['default_strictness'] === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="lenient" {{ $settings['default_strictness'] === 'lenient' ? 'selected' : '' }}>Lenient</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Canvas Watermark Opacity (0.01 - 1.0)</label>
                    <input type="number" step="0.01" min="0.01" max="1.0" name="watermark_opacity" value="{{ old('watermark_opacity', $settings['watermark_opacity']) }}" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>
            </div>

            <div class="flex items-center justify-end pt-3">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                    Save Proctoring Defaults
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
