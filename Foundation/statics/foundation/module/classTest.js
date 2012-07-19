/**
 * class_test.js
 * 测试类模块
 */
define( function ( require, exports, module ) {
    // 加载class模块，获得基类实例
    var Class = require('./class');

    var Maker = new Class();

    // 为Maker类增加方法
    Maker.prototype.init = function () {
        this.name = arguments[0];
        this.getName = function () {
            console.log(this.name);
        };
    };

    Maker.include({
        say : function ( somethind ) {
            console.log( somethind );
        }
    });

    var obj = new Maker('my name is class!');

    var C_Maker = new Class( Maker );

    var foo = new C_Maker('I am son!');
    console.log(foo);
    foo.say('hi');
});
