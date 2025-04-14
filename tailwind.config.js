import theme from 'tailwindcss/defaultTheme';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                bebas: ['Bebas Kai', 'sans-serif'],
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
    
    // module.exports = {
    //     theme: {
    //         extend: {
    //             fontFamily: {
    //                 sans: ['Bebas Kai', 'sans-serif'],
    //             },
    //         },
    //     },
    // },
};
