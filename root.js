/**
 * root.js
 * 以页面的唯一DOM加载所需脚本
 */
define( function ( require, exports, module ) {
    var $ = require('jquery');

    // 加载 IE 模块
    if ( $.browser.msie ) {
        require.async('./module/ie/ie');
    }

    // 加载测试"class"模块
    if ( $("#test-class")[0] ) {
        require.async('./module/classTest');
    }

    // 测试bootstrap模块
    require('./module/bootstrap/js/bootstrap.min');

}); // END root.js
