/**
 * root.js
 * 以视图的唯一DOM加载所需脚本
 */
define( function ( require, exports, module ) {
    var $ = require('jquery');

    // 加载 IE 模块
    if ( $.browser.msie ) {
        require.async('./module/ie/ie');
    }

    // index.html
    if ( $("#main").attr('view') === 'index' ) {
        require.async('./module/index');
    }

    // footer.html
    if ( $("#footer")[0] ) {
        require.async('./module/footer');
    }

}); // END root.js
