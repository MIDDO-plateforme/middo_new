/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.html.twig",
    "./assets/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        'middo-primary': '#667eea',
        'middo-secondary': '#764ba2',
        'middo-accent': '#8b7ec8',
      },
    },
  },
  plugins: [],
}