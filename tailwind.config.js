import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './public/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            boxShadow: {
                'ambient': '0 1px 3px rgba(15, 23, 42, 0.03), 0 2px 8px rgba(15, 23, 42, 0.04)',
                'card': '0 1px 3px 0 rgba(15, 23, 42, 0.03), 0 6px 16px -2px rgba(15, 23, 42, 0.04)',
                'elevation': '0 10px 30px -4px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.03)',
            },
            animation: {
                'live-ping': 'livePing 2s cubic-bezier(0, 0, 0.2, 1) infinite',
                'fade-up': 'fadeInSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards',
            },
            keyframes: {
                livePing: {
                    '75%, 100%': {
                        transform: 'scale(2)',
                        opacity: '0',
                    },
                },
                fadeInSlideUp: {
                    from: {
                        opacity: '0',
                        transform: 'translateY(12px)',
                    },
                    to: {
                        opacity: '1',
                        transform: 'translateY(0)',
                    },
                },
            },
        },
    },
    plugins: [],
};
