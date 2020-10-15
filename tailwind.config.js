module.exports = {
  future: {
    removeDeprecatedGapUtilities: true,
    purgeLayersByDefault: true,
    defaultLineHeights: true,
    standardFontWeights: true
  },
  purge: ['./resources/views/**/*.blade.php', './resources/js/**/*.vue'],
  theme: {
    extend: {}
  },
  variants: {},
  plugins: []
}
