/** @type {import('tailwindcss').Config} */
export default {
  content: ["./resources/**/*.blade.php", "./resources/**/*.js"],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#0056d2',
          container: '#0040a1',
          fixed: '#e8f0fe',
        },
        secondary: {
          DEFAULT: '#006c47',
          container: '#8df7c1',
          fixed: '#e0f9ef',
        },
        surface: {
          DEFAULT: '#f8f9fb',
          low: '#f3f4f6',
          lowest: '#ffffff',
          high: '#eef0f4',
          'on-surface': '#191c1e',
          'on-surface-variant': '#424654',
        },
        outline: {
          DEFAULT: '#c3c6d6',
          variant: '#d1d4e0',
        },
      },
      fontFamily: {
        display: ['Manrope', 'sans-serif'],
        body: ['Inter', 'sans-serif'],
      },
      boxShadow: {
        ambient: '0 20px 40px rgba(25, 28, 30, 0.06)',
        glass: '0 8px 20px rgba(0, 0, 0, 0.03)',
      },
      backdropBlur: {
        xs: '2px',
        sm: '8px',
        md: '12px',
        lg: '20px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}