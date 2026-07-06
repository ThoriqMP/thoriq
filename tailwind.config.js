import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        {
            pattern: /(bg|text|border|ring)-(indigo|emerald|rose|violet|amber|sky)-(50|100|300|500|600|700|800)/,
            variants: ['dark', 'peer-checked', 'hover', 'peer-checked:dark', 'dark:peer-checked'],
        },
        {
            pattern: /bg-(indigo|emerald|rose|violet|amber|sky)-950\/[25]0/,
            variants: ['dark'],
        }
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
