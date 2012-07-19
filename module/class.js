/**
 * class.js
 * 类 实现模块
 */
define( function ( require, exports, module ) {
    exports.Class = function ( parent ) {
        var _class = function () {
            this.init.apply( this, arguments );
        };

        // 以父类parent的原型作为_class的原型
        if ( parent ) {
            var SubClass = function () {};
            SubClass.prototype = parent.prototype;
            _class.prototype = new SubClass();
        }

        // 扩张新的类
        _class.prototype.init = function () {};

        // prototype 设置别称 fn
        _class.fn = _class.prototype;

        _class.fn.parent = _class;

        // TODO
        //_class._super = _class.__proto__;

        // 给类添加静态成员
        _class.extend = function ( obj ) {
            var extended = _class.extended;

            // 拷贝obj的所有属性给类
            for( var i in obj ) {
                _class[i] = obj[i];
            }

            if ( extended ) {
                // 回调函数 extended，完成extend后调用
                extended( _class );
            }
        };

        // 给类的原型添加属性
        _class.include = function ( obj ) {
            var included = obj.included;

            // 拷贝obj的所有属性给类的原型
            for ( var i in obj ) {
                _class.fn[i] = obj[i];
            }

            if ( included ) {
                // 回调函数 included，完成include后调用
                included( _class );
            }
        };

        // 返回构造函数
        return _class;
    };

    return exports.Class;
}); // END class.js
