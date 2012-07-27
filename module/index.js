/**
 * index.js
 * 对应View index
 */
define( function ( require, exports, module ) {
    var _ = require('underscore');
    var $ = require('jquery');

    // 首页幻灯片，调用slide模块
    seajs.use('/statics/foundation/module/slide.module', function ( slide ) {
        // 配置slide模块的实例
        slide.config = {
            slideDOM : $("#big-slide .slide-ul")[0],
            pageDOM  : $("#big-slide .slide-page ul")[0]
        };

        // 初始化slide实例
        slide.init();
    });

    /**
     * 最新捐赠slide
     */
    $(".news-thanks-project .main .thanks .side").each( function () {
        var _this = this;

        seajs.use('/statics/foundation/module/slide.module', function ( slide ) {
            slide.config = {
                slideDOM  : $(_this).find('.slide-ul')[0],
                pageDOM   : $(_this).find('ul.page')[0],
                cutover   : true,
                animation : 'no',
                speed     : 3500
            };

            slide.init();
        });
    });

    /**
     * 推荐项目slide
     */
    seajs.use('/statics/foundation/module/slide.module', function ( slide ) {
        var project = $("#main .news-thanks-project .project");
        slide.config = {
            slideDOM  : project.find('.slide-ul')[0],
            pageDOM   : project.find('ul.page')[0],
            cutover   : true,
            animation : 'no',
            speed     : 4300
        };

        slide.init();
    });

    /**
     * 基金会公告，最新捐赠的tab切换
     */
    // 信息切换
    var checkTab = function ( type, side ) {
        $(".news-thanks-project .main ." + type + ' .side').hide();
        $(".news-thanks-project .main ." + type + ' .side-' + side ).show();
    };

    // 为菜单绑定切换tab的事件
    $(".news-thanks-project .menu .cell").each( function () {
        var _this = this;
        var type = $(this).attr('cell_type');

        $(this).find('.sub-title').click( function () {
            var side = $(this).attr('side');

            $(_this).find('.on').removeClass('on');
            $(this).addClass('on');

            checkTab( type, side );
        });

    });// END bind event

}); // END index.js
