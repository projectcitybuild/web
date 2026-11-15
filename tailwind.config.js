/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/front/**/*.blade.php",
    "./resources/views/errors/**/*.blade.php",
    "./resources/views/vendor/pagination/**/*.blade.php",
    "./resources/js/front/**/*.js",
    "./resources/js/front/**/*.vue",
    "./node_modules/flowbite/**/*.js",
  ],
  theme: {
    extend: {},
    fontFamily: {
      'display': ['Bebas Neue'],
      'body': ['Inter'],
    }
  },
  plugins: [
      require('@tailwindcss/forms'),
      require('flowbite/plugin'),
  ],
}

