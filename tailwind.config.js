/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#101827',
                muted: '#667085',
                canvas: '#f7f8fb',
                coral: '#ff6b4a',
            },
            fontFamily: {
                sans: ['Manrope', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                display: ['DM Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            boxShadow: {
                card: '0 10px 35px rgba(16, 24, 39, 0.06)',
            },
        },
    },
    plugins: [],
};
