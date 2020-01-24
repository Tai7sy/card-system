const merge = require('webpack-merge');
const TerserPlugin = require('terser-webpack-plugin');


module.exports = merge(require('./webpack.config'), {
  optimization: {
    minimize: true,
    minimizer: [
      new TerserPlugin({
        chunkFilter: (chunk) => {
          // Exclude uglification for the `vendor` chunk
          if (chunk.name === 'chunk-vendors') {
            return false;
          }

          return true;
        },
        sourceMap: false,
        terserOptions: {
          ecma: undefined,
          warnings: false,
          parse: {},
          compress: {
            drop_console: true,
            drop_debugger: true,
          },
          mangle: true, // Note `mangle.properties` is `false` by default.
          module: false,
          output: null,
          toplevel: false,
          nameCache: null,
          ie8: false,
          keep_classnames: undefined,
          keep_fnames: false,
          safari10: false,
        },
      })
    ],
  },
});

