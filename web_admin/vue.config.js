const path = require('path');
const webpack = require('webpack');

module.exports = {
  lintOnSave: false,
  productionSourceMap: false,
  outputDir: path.resolve(__dirname, '../public/dist/admin'),
  publicPath: '/dist/admin/',
  indexPath: path.resolve(__dirname, '../resources/views/admin.blade.php'),
  transpileDependencies: [/\bvue-awesome\b/],
  devServer: {
    port: 8081,
    proxy: { '/api': { target: process.env.DEV_API_TARGET || 'http://127.0.0.1:8000', changeOrigin: true } }
  },
  configureWebpack: {
    plugins: [new webpack.ProvidePlugin({ 'window.Quill': 'quill/dist/quill.js', Quill: 'quill/dist/quill.js' })]
  },
  chainWebpack(config) {
    config.plugin('copy').tap(args => {
      args[0].patterns.forEach(pattern => {
        pattern.globOptions = pattern.globOptions || {};
        pattern.globOptions.ignore = [...(pattern.globOptions.ignore || []), '**/index_dev.html'];
      });
      return args;
    });
    config.resolve.alias.set('@', path.resolve(__dirname, 'src'));
    config.resolve.alias.set('~', path.resolve(__dirname, '../web_common'));
    config.resolve.alias.set('vue$', 'vue/dist/vue.esm.js');
    if (process.env.NODE_ENV === 'development') {
      config.plugin('html').tap(args => {
        args[0].template = path.resolve(__dirname, 'public/index_dev.html');
        return args;
      });
    }
  }
};
