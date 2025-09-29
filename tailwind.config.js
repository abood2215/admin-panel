/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [require("daisyui")], // تأكد من وجود DaisyUI هنا
  daisyui: {
    themes: ["light", "dark"], // (اختياري) حدد السمات التي تريدها
  },
};