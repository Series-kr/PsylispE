module.exports = {
  content: [
    "./templates/**/*.php",
    "./public/assets/js/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: '#2563EB',
          dark: '#1D4ED8'
        },
        accent: '#14B8A6',
        success: '#10B981',
        warning: '#F59E0B',
        danger: '#EF4444',
        background: '#F8FAFC',
        surface: '#FFFFFF',
        'text-primary': '#0F172A',
        'text-secondary': '#64748B',
        border: '#E2E8F0'
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        mono: ['Source Code Pro', 'monospace']
      }
    },
  },
  plugins: [],
}