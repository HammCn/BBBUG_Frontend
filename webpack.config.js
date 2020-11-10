const resolve = require('path').resolve
const webpack = require('webpack')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const url = require('url')
const publicPath = ''
module.exports = (options = {}) => ({
  entry: {
    vendor: './src/vendor',
    index: './src/main.js'
  },
  output: {
    path: resolve(__dirname, 'dist'),
    filename: options.dev ? '[name].js' : '[name].js?[chunkhash]',
    chunkFilename: '[id].js?[chunkhash]',
    publicPath: options.dev ? '/assets/' : publicPath
  },
  module: {
    rules: [{
      test: /\.vue$/,
      use: ['vue-loader']
    },
    {
      test: /\.js$/,
      use: ['babel-loader'],
      exclude: /node_modules/
    },
    {
      test: /\.css$/,
      use: ['style-loader', 'css-loader', 'postcss-loader']
    },
    {
      test: /\.(png|jpg|jpeg|gif|eot|ttf|woff|woff2|svg|svgz)(\?.+)?$/,
      use: [{
        loader: 'url-loader',
        options: {
          limit: 10000
        }
      }]
    }
    ]
  },
  plugins: [
    new webpack.optimize.CommonsChunkPlugin({
      names: ['vendor', 'manifest'],
      compress: {     //压缩代码
        dead_code: true,    //移除没被引用的代码
        warnings: false,     //当删除没有用处的代码时，显示警告
        loops: true //当do、while 、 for循环的判断条件可以确定是，对其进行优化
      },
      except: ['$super', '$', 'exports', 'require']    //混淆,并排除关键字
    }),
    new HtmlWebpackPlugin({
      template: 'src/index.html'
    }),
  ],
  resolve: {
    alias: {
      '~': resolve(__dirname, 'src')
    },
    extensions: ['.js', '.vue', '.json', '.css']
  },
  devServer: {
    disableHostCheck: true,
    host: '127.0.0.1',
    port: 8010,
    proxy: {
      '/api/': {
        target: 'http://127.0.0.1:8080',
        changeOrigin: true,
        pathRewrite: {
          '^/api': ''
        }
      }
    },
    historyApiFallback: {
      index: url.parse(options.dev ? '/assets/' : publicPath).pathname
    }
  },
  devtool: options.dev ? '#eval-source-map' : '#source-map'
})
