/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{ts,tsx}'],
  theme: {
    extend: {
      colors: {
        gold: {
          50:  '#fdf8e1',
          100: '#faf0b8',
          200: '#f5e07a',
          300: '#eec93d',
          400: '#e5b41f',
          500: '#c9990d',
          600: '#a87c0a',
          700: '#855f09',
          800: '#63460a',
          900: '#42300b',
        },
        dark: {
          50:  '#f5f5f5',
          100: '#e0e0e0',
          200: '#b0b0b0',
          300: '#808080',
          400: '#505050',
          500: '#303030',
          600: '#202020',
          700: '#181818',
          800: '#111111',
          900: '#0a0a0a',
        },
        blood: {
          500: '#8b0000',
          600: '#6b0000',
        },
      },
      fontFamily: {
        display: ['Cinzel', 'serif'],
        sans:    ['Inter', 'sans-serif'],
        mono:    ['JetBrains Mono', 'monospace'],
      },
    },
  },
  plugins: [],
}

