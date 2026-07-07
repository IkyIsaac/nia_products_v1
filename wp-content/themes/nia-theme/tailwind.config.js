/**
 * Phase 1 scaffold only. Content paths point at where theme templates and
 * plugin blocks will live once Phase 2 (theme scaffold) and the Nia Core
 * plugin exist. `theme.extend` is intentionally empty here — the full
 * DESIGN_SYSTEM.md token set (colors, type scale, spacing, radius) is a
 * Phase 2 task (PROJECT_PLAN.md Phase 2), not Phase 1 infrastructure.
 */
module.exports = {
  content: [
    './**/*.php',
    './src/**/*.js',
    '../../plugins/nia-core/blocks/**/*.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
