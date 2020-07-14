
const path = require('path');
const webpack = require('webpack');

function resolve (...dir) {
  return path.join(__dirname, '..', ...dir)
}

module.exports = {
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    alias: {
      'vue$': 'vue/dist/vue.esm.js',
      '@': resolve('src'),
      '~': resolve('../../card/web_common'),
      '~admin': resolve('../../card/web_admin/src'),
      '~merchant': resolve('../../card/web_merchant/src'),
    },
    modules: [resolve('node_modules')]
  },
  resolveLoader: {
    modules: [resolve('node_modules')],
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': process.env
    }),
    new webpack.ProvidePlugin({
      'window.Quill': 'quill/dist/quill.js',
      'Quill': 'quill/dist/quill.js',
    })
  ]
};
