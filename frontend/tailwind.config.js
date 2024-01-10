/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./src/**/*.blade.php', './src/**/*.ts', './src/**/*.vue'],
  safelist: [
    {
      pattern: /grid-cols-([1-9]?[1-9])/
    }
  ],
  darkMode: 'class',
  theme: {
    extend: {
      screens: {
        xs: '350px'
      },
      colors: {
        primary: 'var(--theme-primary)',
        'on-primary': 'var(--theme-on-primary)',
        'primary-container': 'var(--theme-primary-container)',
        'on-primary-container': 'var(--theme-on-primary-container)',
        secondary: 'var(--theme-secondary)',
        'on-secondary': 'var(--theme-on-secondary)',
        'secondary-container': 'var(--theme-secondary-container)',
        'on-secondary-container': 'var(--theme-on-secondary-container)',
        alert: 'var(--theme-alert)',
        'on-alert': 'var(--theme-on-alert)',
        'alert-container': 'var(--theme-alert-container)',
        'on-alert-container': 'var(--theme-on-alert-container)',
        error: 'var(--theme-error)',
        'on-error': 'var(--theme-on-error)',
        'error-container': 'var(--theme-error-container)',
        'on-error-container': 'var(--theme-on-error-container)',
        plus: 'var(--theme-plus)',
        'on-plus': 'var(--theme-on-plus)',
        minus: 'var(--theme-minus)',
        'on-minus': 'var(--theme-on-minus)',
        background: 'var(--theme-background)',
        'on-background': 'var(--theme-on-background)',
        surface: 'var(--theme-surface)',
        'on-surface': 'var(--theme-on-surface)',
        'on-link': 'var(--theme-on-link)',
        'outline-color': 'var(--theme-outline)',
        'surface-variant': 'var(--theme-surface-variant)',
        'on-surface-variant': 'var(--theme-on-surface-variant)',
        'achievement-normal': 'var(--theme-achievement-normal)',
        'achievement-bronze': 'var(--theme-achievement-bronze)',
        'achievement-silver': 'var(--theme-achievement-silver)',
        'achievement-gold': 'var(--theme-achievement-gold)'
      },
      outlineColor: 'outline-color',
      animation: {
        fadein: 'fadein 0.3s',
        'fadeout-3s': 'fadeout-3s 5.0s forwards',
        'slide-from-left': '2s ease-in-out 0s 1 normal both running slide-from-left'
      },
      keyframes: {
        fadein: {
          '0%': { opacity: 0 },
          '100%': { opacity: 1 }
        },
        fadeout: {
          '0%': { opacity: 1 },
          '100%': { opacity: 0 }
        },
        'fadeout-3s': {
          '0%': { opacity: 1 },
          '70%': { opacity: 1 },
          '100%': { opacity: 0 }
        },
        'slide-from-left': {
          '0%': {
            opacity: 0,
            transform: 'translateX(-120%) translateZ(0)'
          },
          '100%': {
            opacity: 1,
            transform: 'translateX(0) translateZ(0)'
          }
        }
      }
    }
  },
  plugins: []
}
