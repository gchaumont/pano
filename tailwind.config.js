module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            backgroundColor: {
                card: "var(--color-bg-card)",
                backdrop: "var(--backdropColor)",
                globsearch: "var(--navbarBackgroundColor)",

            },
            minWidth: {
                '55ch': '55ch',
                '35ch': '35ch',
            },
            fontSize: {
                '2xs' : '.60rem'
            }
        },
    },
    plugins: [],
}