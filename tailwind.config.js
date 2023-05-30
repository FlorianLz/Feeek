/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
    ],
    theme: {
        extend: {
            fontFamily: {
                'sans': ['Inter', 'sans-serif'],
                'clash': ['ClashDisplay', 'sans-serif'],
            },
            colors: {
                'primary': '#3603EC',
                'secondary': '#FFCD29',
                'grey': '#EAEBED',
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}