/**
 * IE debug 模块
 * author Degas
 */
    define( function ( require, exports, module ) {
        // 加载 ie.css
        require('./ie.css#');

        // 加载jQuery 模块
        var $ = require('jquery');

        // 加载 Underscore 模块
        var _ = require('underscore');

        // 方法，是否为IE6
        var IE6 = function () {
            if ( $.browser.msie && $.browser.version.slice(0, 3) === "6.0") {
                return true;
            }
        }; // END IE6
        
        // 为IE6+的数组加上indexOf
        if ( ![].indexOf ) {
            Array.prototype.indexOf = function ( src ) {
                for ( var i in this ) {
                    if ( this[i] === src ) {
                        return i;
                    }//end if
                }//end for
            };
        } // END indexOf

        // PNG Fix in IE6
        if ( IE6() ) {
            require.async('pngFix', function ( DD_belatedPNG ) {
                this.DD_belatedPNG = DD_belatedPNG;

                DD_belatedPNG.fix(
                    '#header .menu,' +
                    '#big-slide .slide-page,' +
                    '#big-slide .slide-page .on,' +
                    '#big-slide .slide-page li,' +
                    '#big-slide .slide-page li:hover,' +
                    '.big-board .check,' +
                    '.big-board .donate,' +
                    '#footer .link .cell,' +
                    '#footer .more-list,' +
                    '#main .news-thanks-project .menu,' +
                    '#main .news-thanks-project .menu .cell,' +
                    '#main .news-thanks-project .menu .cell .sub-title,' +
                    '#main .news-thanks-project,' +
                    '.sub-breadcrumb,' +
                    '.sub-content .content-body .sub-list li .time,' +
                    '#header .first-menu-cell'
                );

                $(".apps img").each( function () {
                    DD_belatedPNG.fixPng( $(this)[0] );
                });

            });
        } // END png fix

        // 菜单first-child
        if ( $("#header .menu")[0] ) {
            $("#header .menu a").eq(0).addClass('first-menu-cell');
        }

        // fixSlideTitle 内部方法
        // slide 标题背景长度 IE6 BUG
        // 等待200毫秒执行一次，直到没有class='no-fix'的title存在
        var fixSlideTitle = function () {
            if ( $(".no-fix")[0] ) {
                $('#big-slide .title:visible').each( function () {
                    var length = $(this).outerWidth();

                    $(this).find('.bg').css('width', length + 'px');
                    $(this).removeClass('no-fix');
                });
            } else {
                // 无class='on'的元素后，停止本函数
                return false;
            }

            setTimeout( fixSlideTitle, 200 );
        };

        // 过滤
        if ( $('#big-slide .title') && IE6() ) {
            // 为所有的title加上class='no-fix'
            $("#big-slide .title").each( function () {
                $(this).addClass('no-fix');
            });

            fixSlideTitle();
        } // END if

        // IE6 支持最小高度
        if ( $("#main .wrap")[0] && IE6() ) {
            if ( $("#main .wrap").outerHeight() < 600 ) {
                $("#main .wrap").css( 'height', '600px' );
            }
        }

    });// END define
