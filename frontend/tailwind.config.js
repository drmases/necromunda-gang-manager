/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{ts,tsx}'],
  theme: {
    extend: {
      colors: {
        gold: {
          50:  '#fbf3df',
          100: '#f5e4b8',
          200: '#ecc978',
          300: '#dba83f',
          400: '#c28e28',
          500: '#a8761e',
          600: '#8f6217',
          700: '#6b4a12',
          800: '#4a330d',
          900: '#2f200a',
        },
        dark: {
          50:  '#171512',
          100: '#242320',
          200: '#3a3833',
          300: '#57544c',
          400: '#78746a',
          500: '#96917f',
          600: '#b7b2a2',
          700: '#d8d4c9',
          800: '#ece9e2',
          900: '#f8f7f3',
        },
        blood: {
          500: '#8b0000',
          600: '#6b0000',
        },
      },
      fontFamily: {
        display: ['Oswald', 'sans-serif'],
        sans:    ['Inter', 'sans-serif'],
        mono:    ['JetBrains Mono', 'monospace'],
      },
    },
  },
  plugins: [],
}

