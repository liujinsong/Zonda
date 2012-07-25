/**
 * footer.js
 * 对应View footer
 */
define( function ( require, exports, module ) {
    var $ = require('jquery');

    // 点击友情链接，toggle菜单
    $("#footer .more").click( function () {
        $("#footer .more-list").fadeToggle('fast');
    });

    // 当点击的对象并非友情链接时，收起列表
    $( document.documentElement ).click( function ( event ) {
        if ( event.target !== $("#footer .more-list")[0] && event.target !== $("#footer .more")[0] ) {
            $("#footer .more-list").fadeOut();
        }
    });
});
