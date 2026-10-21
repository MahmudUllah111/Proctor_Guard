@extends('layouts.admin')

@section('title', 'System Settings')
@section('page_title', 'System Settings & Dynamic SMTP')
@section('page_subtitle', 'Dynamic runtime mailer, global SEO tags, and anti-cheat defaults without code modifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Dynamic SMTP Configuration -->
    <div class="card p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-semibold text-sm text-slate-900 flex items-center gap-2">
                    <x-icon name="mail" class="w-4 h-4 text-indigo-600" />
                    <span>Dynamic SMTP Email Configuration</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Dispatches student score reports and instant administrative cheat strike alerts</p>
            </div>
            <span class="pill-emerald">Zero Code Config</span>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="settings_group" value="smtp">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Mail Driver / Mailer</label>
                    <x-select name="mail_mailer">
                        <option value="smtp" @selected($settings['mail_mailer'] === 'smtp')>SMTP</option>
                        <option value="log" @selected($settings['mail_mailer'] === 'log')>Log (Testing / Local)</option>
                    </x-select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">SMTP Host <span class="text-rose-500">*</span></label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" required class="input">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">SMTP Port <span class="text-rose-500">*</span></label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}" required class="input num">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">SMTP Username</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}" placeholder="username / api key" class="input">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">SMTP Password</label>
                    <input type="password" name="mail_password" value="{{ old('mail_password', $settings['mail_password']) }}" placeholder="••••••••••••" class="input">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Encryption</label>
                    <x-select name="mail_encryption">
                        <option value="tls" @selected($settings['mail_encryption'] === 'tls')>TLS</option>
                        <option value="ssl" @selected($settings['mail_encryption'] === 'ssl')>SSL</option>
                        <option value="none" @selected(in_array($settings['mail_encryption'], ['none', '', null]))>None</option>
                    </x-select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">From Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" required class="input">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">From Sender Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}" required class="input">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Admin Alert Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="admin_alert_email" value="{{ old('admin_alert_email', $settings['admin_alert_email']) }}" required class="input">
                </div>
            </div>

            <div class="flex items-center justify-end pt-3">
                <button type="submit" class="btn-primary">
                    Save Dynamic SMTP Settings
                </button>
            </div>
        </form>

        <!-- SMTP Connectivity Test Tool -->
        <div class="pt-4 border-t border-slate-100 bg-slate-50/70 p-4 rounded-xl border border-slate-200/60">
            <h4 class="text-xs font-semibold text-slate-900 mb-2 flex items-center gap-1.5">
                <x-icon name="circle-check" class="w-4 h-4 text-emerald-600" />
                <span>Verify Dynamic SMTP Transmission</span>
            </h4>
            <form action="{{ route('admin.settings.test-email') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-2.5">
                @csrf
                <input type="email" name="test_email" value="{{ auth()->user()->email ?? 'admin@examcms.test' }}" required placeholder="recipient email..."
                    class="input w-full sm:w-80">
                <button type="submit" class="btn-secondary whitespace-nowrap">
                    Send Test Email &rarr;
                </button>
            </form>
        </div>
    </div>

    <!-- Global SEO Configuration -->
    <div class="card p-6 space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h3 class="font-semibold text-sm text-slate-900 flex items-center gap-2">
                <x-icon name="globe" class="w-4 h-4 text-indigo-600" />
                <span>Global SEO & Social Graph Metadata</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Default search engine parameters and OpenGraph tags used across all landing pages</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="settings_group" value="seo">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Portal Platform Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" required class="input">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Robots Directive</label>
                    <x-select name="seo_robots">
                        <option value="index, follow" @selected($settings['seo_robots'] === 'index, follow')>index, follow (Allow Search Engines)</option>
                        <option value="noindex, nofollow" @selected($settings['seo_robots'] === 'noindex, nofollow')>noindex, nofollow (Private Assessment Portal)</option>
                    </x-select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Meta Title <span class="text-rose-500">*</span></label>
                <input type="text" name="seo_title_default" value="{{ old('seo_title_default', $settings['seo_title_default']) }}" required class="input">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Meta Description <span class="text-rose-500">*</span></label>
                <textarea name="seo_description_default" rows="2" required
                    class="block w-full p-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-900 placeholder-slate-400 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">{{ old('seo_description_default', $settings['seo_description_default']) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Meta Keywords</label>
                    <input type="text" name="seo_keywords_default" value="{{ old('seo_keywords_default', $settings['seo_keywords_default']) }}" class="input">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default OpenGraph Image URL</label>
                    <input type="text" name="seo_og_image" value="{{ old('seo_og_image', $settings['seo_og_image']) }}" placeholder="https://..." class="input">
                </div>
            </div>

            <div class="flex items-center justify-end pt-3">
                <button type="submit" class="btn-primary">
                    Save Global SEO Parameters
                </button>
            </div>
        </form>
    </div>

    <!-- Proctoring Engine Defaults -->
    <div class="card p-6 space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h3 class="font-semibold text-sm text-slate-900 flex items-center gap-2">
                <x-icon name="shield-alert" class="w-4 h-4 text-rose-600" />
                <span>Proctoring Engine Global Defaults</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Applied automatically to newly created assessments</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="settings_group" value="proctoring">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Max Strikes</label>
                    <input type="number" name="default_max_strikes" value="{{ old('default_max_strikes', $settings['default_max_strikes']) }}" min="1" max="20" required class="input num">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Strictness</label>
                    <x-select name="default_strictness">
                        <option value="strict" @selected($settings['default_strictness'] === 'strict')>Strict</option>
                        <option value="medium" @selected($settings['default_strictness'] === 'medium')>Medium</option>
                        <option value="lenient" @selected($settings['default_strictness'] === 'lenient')>Lenient</option>
                    </x-select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Canvas Watermark Opacity (0.01 - 1.0)</label>
                    <input type="number" step="0.01" min="0.01" max="1.0" name="watermark_opacity" value="{{ old('watermark_opacity', $settings['watermark_opacity']) }}" required class="input num">
                </div>
            </div>

            <div class="flex items-center justify-end pt-3">
                <button type="submit" class="btn-primary">
                    Save Proctoring Defaults
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
