const path = require('path');
const glob = require('glob');

module.exports = {
    mode: 'development',
    resolve: {
      alias: {
        '~': path.resolve(__dirname, 'node_modules/')
      }
    },
    entry: {
      'js/admin/scripts.js': glob.sync('./assets/js/admin/scripts/**/*.js')
    },
    output: {
      filename: '[name]',
      path: path.resolve(__dirname, 'views')
    }
};
