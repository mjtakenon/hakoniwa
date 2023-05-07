/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.ts",
        "./resources/**/*.vue"
    ],
    theme: {
        extend: {
            screens: {
                xs: '350px'
            },
            colors: {
                primary: {
                    light: "#7df6de",
                    DEFAULT: "#00d1b2",
                    dark: "#4b9389"
                },
                link: "#485fc7",
                success: {
                    light: "#b6ddff",
                    DEFAULT: "#3e8ed0",
                    dark: "#1b598c"
                },
                info: {
                    light: "#aff8d7",
                    DEFAULT: "#48c78e",
                    dark: "#1a7e51"
                },
                warning: {
                    light: "#e8dcc5",
                    DEFAULT: "#ffe08a",
                    dark: "#fb8c00"
                },
                danger: {
                    light: "#ffabbe",
                    DEFAULT: "#f14668",
                    dark: "#a62640"
                }
            },
            animation: {
                "fadein": "fadein 0.3s",
                "slide-in-left": "slide-in-left 1.2s"
            },
            keyframes: {
                "fadein": {
                    "0%": {opacity: 0},
                    "100%": {opacity: 1}
                },
                "fadeout": {
                    "0%": {opacity: 1},
                    "100%": {opacity: 0}
                },
                "slide-in-left": {
                    "0%": {
                        transform: "translateX(-20%)",
                        opacity: 0,
                    },
                    "100%": {
                        transform: "translateX(0%)",
                        opacity: 1
                    }
                }
            }
        },
    },
    plugins: [],
}

