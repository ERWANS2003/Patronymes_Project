import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
                secondary: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                },
                accent: {
                    50: '#fdf4ff',
                    100: '#fae8ff',
                    200: '#f5d0fe',
                    300: '#f0abfc',
                    400: '#e879f9',
                    500: '#d946ef',
                    600: '#c026d3',
                    700: '#a21caf',
                    800: '#86198f',
                    900: '#701a75',
                },
                // Burkina Faso subtle brand palette
                bfGreen: {
                    50: '#ecf8f0',
                    100: '#d7f1e1',
                    200: '#afe3c3',
                    300: '#87d5a5',
                    400: '#5fc887',
                    500: '#37ba69',
                    600: '#0faa4e', // primary green
                    700: '#0d8a41',
                    800: '#0b6b34',
                    900: '#084d26',
                },
                bfRed: {
                    50: '#feecec',
                    100: '#fdd9d9',
                    200: '#fbb3b3',
                    300: '#f88d8d',
                    400: '#f56767',
                    500: '#f24141',
                    600: '#ef2b2d', // flag red
                    700: '#c22224',
                    800: '#951a1c',
                    900: '#691314',
                },
                bfGold: {
                    50: '#fffae6',
                    100: '#fff4cc',
                    200: '#ffe999',
                    300: '#ffdd66',
                    400: '#ffd233',
                    500: '#ffc700',
                    600: '#f7bf00', // warm gold (near #FCD116)
                    700: '#d0a100',
                    800: '#a98300',
                    900: '#826500',
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            animation: {
                'fade-in': 'fadeIn 0.4s ease-out',
                'fade-in-up': 'fadeInUp 0.6s ease-out',
                'slide-in-right': 'slideInRight 0.5s ease-out',
                'bounce-slow': 'bounce 2s infinite',
                'pulse-slow': 'pulse 3s infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(20px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
            },
            boxShadow: {
                'glow': '0 0 20px rgba(59, 130, 246, 0.3)',
                'glow-lg': '0 0 40px rgba(59, 130, 246, 0.4)',
                'inner-lg': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.1)',
            },
            backdropBlur: {
                xs: '2px',
            },
        },
    },

    plugins: [
        forms,
        typography,
        function({ addUtilities }) {
            const newUtilities = {
                '.text-gradient': {
                    'background': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    '-webkit-background-clip': 'text',
                    '-webkit-text-fill-color': 'transparent',
                    'background-clip': 'text',
                },
                '.glass': {
                    'background': 'rgba(255, 255, 255, 0.1)',
                    'backdrop-filter': 'blur(10px)',
                    'border': '1px solid rgba(255, 255, 255, 0.2)',
                },
                '.glass-dark': {
                    'background': 'rgba(0, 0, 0, 0.1)',
                    'backdrop-filter': 'blur(10px)',
                    'border': '1px solid rgba(255, 255, 255, 0.1)',
                },
            }
            addUtilities(newUtilities)
        }
    ],
};
