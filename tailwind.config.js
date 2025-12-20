/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./assets/js/**/*.js",
    "./inc/**/*.php",
    "./template-parts/**/*.php"
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: '#3b5dce',
        secondary: '#2d3237',
      }
    }
  },
  plugins: [],
}
