import colors from 'tailwindcss/colors'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    screens: {
      // Mobile-first breakpoints optimized for foldable devices
      'xs': '375px',      // Small phones
      'sm': '640px',      // Large phones / small tablets
      'md': '768px',      // Tablets / Galaxy Fold unfolded (653px)
      'lg': '1024px',     // Small laptops / landscape tablets
      'xl': '1280px',     // Desktops
      '2xl': '1536px',    // Large desktops
      
      // Custom breakpoints for foldable devices
      'fold': '653px',    // Galaxy Z Fold unfolded width
      'fold-sm': '280px', // Galaxy Z Fold folded width
    },
    extend: {
      fontFamily: {
        'montserrat': ['Montserrat', 'sans-serif'],
      },
      colors: {
        // Alias standard colors to enforce theme
        blue: colors.zinc,   // Remap Blue to Gray/Black
        orange: colors.yellow, // Remap Orange to Yellow

        brand: {
          dark: '#003399',
          yellow: '#FFD700',
          green: '#008000',
        },
        soboa: {
          blue: '#003399', // Bleu foncé
          'blue-dark': '#002266', // Bleu plus foncé
          'blue-light': '#0044CC', // Bleu plus clair
          orange: '#FFD700', // Gold/Yellow
          'orange-light': '#FDE047', // Yellow-300
          'orange-dark': '#EAB308', // Yellow-500
        },
      },
      animation: {
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'float': 'float 6s ease-in-out infinite',
        'bounce-slow': 'bounce 2s infinite',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-20px)' },
        }
      }
    },
  },
  plugins: [],
}
