/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'adzu-blue': '#0A192F', // Ateneo Blue Dark
        'adzu-gold': '#D4AF37', // Ateneo Gold/Yellow
      }
    },
  },
  plugins: [],
}
