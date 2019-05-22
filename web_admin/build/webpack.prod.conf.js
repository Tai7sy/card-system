const merge = require('webpack-merge');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');


module.exports = merge(require('./webpack.config'), {
  plugins: [
    new UglifyJsPlugin({
      uglifyOptions: {
        beautify: false, // 最紧凑的输出
        comments: false, // 删除所有的注释
        compress: {
          // 在UglifyJs删除没有用到的代码时不输出警告
          warnings: false,
          // 删除所有的 `console` 语句,还可以兼容ie浏览器
          drop_console: true,
          drop_debugger: true,
          // 内嵌定义了但是只用到一次的变量
          collapse_vars: true,
          // 提取出出现多次但是没有定义成变量去引用的静态值
          reduce_vars: true,

          properties: true,
          sequences: true,
          dead_code: true,
          conditionals: true,
          comparisons: true,
          evaluate: true,
          booleans: true,
          unused: true,
          loops: true,
          hoist_funs: true,
          if_return: true,
          join_vars: true,
          unsafe: true,
          hoist_vars: true,
          negate_iife: true
          // side_effects: true
        },
        // 混淆
        mangle: {
          toplevel: true, // 混淆在最高作用域中声明的变量名
          eval: true //  混淆在eval 或 with作用域出现的变量名
        }
      },
      sourceMap: false,
      parallel: true
    })
  ]
});

