import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    prefix: "tw-",

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Be Vietnam Pro"', ...defaultTheme.fontFamily.sans],
                "be-vietnam": ['"Be Vietnam Pro"', "sans-serif"],
                bevietnam: ['"Be Vietnam Pro"', "sans-serif"],
            },
            colors: {
                vlbcgreen: "#16a249",
                vlbcGreen1: "#1C6B42",
                vlbcGreen2: "#2A7C4A",
                vlbcReady: "#5ED27A",
                vlbcBeige: "#F5F3EF",
            },
        },
    },

    plugins: [forms],
};
