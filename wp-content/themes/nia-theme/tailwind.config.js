/**
 * Full token set transcribed from DESIGN_SYSTEM.md §1-3 (single source —
 * see that file before changing any value here). Content paths point at
 * the theme's own PHP templates and the Nia Core plugin's blocks, once
 * that plugin exists.
 */
module.exports = {
  content: [
    './**/*.php',
    './src/**/*.js',
    '../../plugins/nia-core/blocks/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#E07A10',
        'on-primary': '#2E1A00',
        'primary-container': '#FFD8A8',
        'on-primary-container': '#241400',
        'primary-fixed': '#FFE8CC',
        'on-primary-fixed': '#241400',
        'primary-fixed-dim': '#CC6A08',
        'inverse-primary': '#F5A03A',
        'surface-tint': '#E07A10',

        secondary: '#D4AF37',
        'on-secondary': '#241A00',
        'secondary-container': '#FCE28A',
        'on-secondary-container': '#241A00',
        'secondary-fixed': '#FFF0B5',
        'on-secondary-fixed': '#1F1500',
        'secondary-fixed-dim': '#E0BA40',
        'on-secondary-fixed-variant': '#4A3A00',

        tertiary: '#6B3A6B',
        'on-tertiary': '#FFFFFF',
        'tertiary-container': '#F0D0F0',
        'on-tertiary-container': '#2A1A2A',
        'tertiary-fixed': '#F8E0F8',
        'on-tertiary-fixed': '#1C0E1C',
        'tertiary-fixed-dim': '#D0B0D0',
        'on-tertiary-fixed-variant': '#4A284A',

        surface: '#FFFCF5',
        'surface-bright': '#FFFCF5',
        'surface-dim': '#E8DEC8',
        'surface-container-lowest': '#FFFFFF',
        'surface-container-low': '#FEF8EC',
        'surface-container': '#F8F0E0',
        'surface-container-high': '#F0E8D4',
        'surface-container-highest': '#E8DEC8',
        'surface-variant': '#F2ECE0',
        background: '#FFFCF5',
        'on-background': '#1E1A10',
        'on-surface': '#1E1A10',
        'on-surface-variant': '#4D4534',
        'inverse-surface': '#2E2A1E',
        'inverse-on-surface': '#F5EFE2',

        outline: '#B89840',
        'outline-variant': '#D8C88A',

        error: '#BA1A1A',
        'on-error': '#FFFFFF',
        'error-container': '#FFDAD6',
        'on-error-container': '#93000A',

        'off-white': '#FFFFFF',
        'warm-grey': '#EAE0CC',
        'warm-ivory': '#FDF8EC',
      },
      borderRadius: {
        DEFAULT: '0.25rem',
        lg: '0.5rem',
        xl: '0.75rem',
        full: '9999px',
      },
      spacing: {
        'container-max': '1440px',
        'section-gap': '120px',
        'margin-mobile': '20px',
        'margin-desktop': '80px',
        gutter: '24px',
        base: '8px',
      },
      fontFamily: {
        'display-lg': ['Playfair Display', 'serif'],
        'display-lg-mobile': ['Playfair Display', 'serif'],
        'headline-lg': ['Playfair Display', 'serif'],
        'headline-lg-mobile': ['Playfair Display', 'serif'],
        'headline-md': ['Playfair Display', 'serif'],
        'headline-sm': ['Playfair Display', 'serif'],
        'body-lg': ['Montserrat', 'sans-serif'],
        'body-md': ['Montserrat', 'sans-serif'],
        'label-lg': ['Montserrat', 'sans-serif'],
        'label-md': ['Montserrat', 'sans-serif'],
      },
      fontSize: {
        'display-lg': ['64px', { lineHeight: '1.1', letterSpacing: '-0.02em', fontWeight: '700' }],
        'display-lg-mobile': ['40px', { lineHeight: '1.2', fontWeight: '700' }],
        'headline-lg': ['48px', { lineHeight: '1.2', fontWeight: '600' }],
        'headline-lg-mobile': ['32px', { lineHeight: '1.2', fontWeight: '600' }],
        'headline-md': ['32px', { lineHeight: '1.3', fontWeight: '500' }],
        // headline-sm: DESIGN_SYSTEM.md §2 Drift #2 — undefined in every
        // mockup, client-approved value retained (24px/1.3/500).
        'headline-sm': ['24px', { lineHeight: '1.3', fontWeight: '500' }],
        'body-lg': ['18px', { lineHeight: '1.6', fontWeight: '400' }],
        'body-md': ['16px', { lineHeight: '1.6', fontWeight: '400' }],
        'label-lg': ['14px', { lineHeight: '1.2', letterSpacing: '0.1em', fontWeight: '600' }],
        'label-md': ['12px', { lineHeight: '1.2', letterSpacing: '0.05em', fontWeight: '500' }],
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
};
