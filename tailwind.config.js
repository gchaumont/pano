module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            backgroundColor: {
                body: "var(--color-bg-body)",
                card: "var(--color-bg-card)",
                backdrop: "var(--backdropColor)",
                globsearch: "var(--navbarBackgroundColor)",

            },
            minWidth: {
                '55ch': '55ch',
                '35ch': '35ch',
            },
            fontSize: {
                '2xs': '.60rem'
            },
            colors: {
                slate: {
                    850: '#1B2230',
                },
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms')
    ],
}