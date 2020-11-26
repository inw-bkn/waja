const colors = require('tailwindcss/colors')

module.exports = {
    future: {
        removeDeprecatedGapUtilities: true,
        purgeLayersByDefault: true,
        defaultLineHeights: true,
        standardFontWeights: true
    },
    purge: ['./resources/views/**/*.blade.php', './resources/js/**/*.vue'],
    theme: {
    extend: {
        colors: {
            teal: colors.teal
        }
    }
    },
    variants: {},
    plugins: []
}
