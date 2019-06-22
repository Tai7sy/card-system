const path = require('path');
const { exec } = require('child_process');

function resolve (...dir) {
  return path.join(__dirname, ...dir)
}

if (process.platform === 'win32') {
  // 完全同步于 card/web_admin
  // src/store
  // src/views/layout
  exec(`xcopy /I /E /Y ${resolve('../../card/web_admin/src/store')} ${resolve('src/store')}`);
  exec(`xcopy /I /E /Y ${resolve('../../card/web_admin/src/views/layout')} ${resolve('src/views/layout')}`);
} else {
  exec(`cp -R ${resolve('../../card/web_admin/src/store/')} ${resolve('src/store/')}`);
  exec(`cp -R ${resolve('../../card/web_admin/src/views/layout/')} ${resolve('src/views/layout/')}`);
}

module.exports = {
  lintOnSave: false,
  productionSourceMap: false,
  outputDir: path.resolve(__dirname, '../public/dist'),
  publicPath: '/dist/',
  indexPath: path.resolve(__dirname, '../resources/views/admin.blade.php'),
  transpileDependencies: [
    /\bvue-awesome\b/
  ],
  chainWebpack: config => {
    if (process.env.NODE_ENV === 'development') {
      config.plugin('html').tap(args => {
        args[0].template = path.resolve(__dirname, 'public/index_dev.html');
        return args
      })
    }
  },
  configureWebpack: config => {
    // console.log(JSON.stringify(config, null, 2));
    // process.exit();

    function filter_loader (rules) {
      rules.forEach(rule => {
        if (rule.use) {
          rule.use.forEach(use => {
            if (use.loader === 'postcss-loader') {
              Object.assign(use.options, {
                config: {
                  path: path.resolve(__dirname)
                }
              });
            }
          })
        }
        if (rule.oneOf) {
          filter_loader(rule.oneOf)
        }
      });
    }

    filter_loader(config.module.rules);
    // process.exit();
    return require(path.resolve(__dirname, 'build/webpack.' + (process.env.NODE_ENV === 'production' ? 'prod' : 'dev') + '.conf.js'))
  },

};
