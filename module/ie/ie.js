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

    });// END define
