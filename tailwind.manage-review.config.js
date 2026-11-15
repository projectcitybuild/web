/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/manage/**/*.blade.php",
    "./resources/views/review/**/*.blade.php",
    "./resources/views/vendor/pagination/**/*.blade.php",
    "./resources/js/manage/**/*.js",
    "./resources/js/manage/**/*.vue",
    "./resources/js/review/**/*.js",
    "./resources/js/review/**/*.vue",
    "./resources/js/shared/**/*.js",
    "./resources/js/shared/**/*.vue",
    "./node_modules/flowbite/**/*.js",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        background: 'var(--color-background)',
        surface: 'var(--color-surface)',
        'surface-muted': 'var(--color-surface-muted)',
        'surface-hover': 'var(--color-surface-hover)',
        border: {
          DEFAULT: 'var(--color-border)',
          strong: 'var(--color-border-strong)',
        },
        foreground: 'var(--color-text)',
        muted: 'var(--color-text-muted)',
        subtle: 'var(--color-text-subtle)',
        primary: {
          DEFAULT: 'var(--color-primary)',
          500: 'var(--color-primary)',
          600: 'var(--color-primary-hover)',
          hover: 'var(--color-primary-hover)',
          active: 'var(--color-primary-active)',
          foreground: 'var(--color-primary-foreground)',
        },
        disabled: 'var(--color-disabled)',
        'disabled-foreground': 'var(--color-disabled-foreground)',
        success: {
          DEFAULT: 'var(--color-success)',
          foreground: 'var(--color-success-foreground)',
        },
        warning: {
          DEFAULT: 'var(--color-warning)',
          foreground: 'var(--color-warning-foreground)',
        },
        danger: {
          DEFAULT: 'var(--color-danger)',
          hover: 'var(--color-danger-hover)',
          foreground: 'var(--color-danger-foreground)',
        },
        info: {
          DEFAULT: 'var(--color-info)',
          foreground: 'var(--color-info-foreground)',
        },
        focus: 'var(--color-focus-ring)',
      },
      fontFamily: {
        sans: ['var(--font-family-sans)'],
        body: ['var(--font-family-sans)'],
        display: ['var(--font-family-display)'],
        mono: ['var(--font-family-mono)'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('flowbite/plugin'),
  ],
}
