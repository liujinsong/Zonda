/**
 * 数据验证模块 verification.js
 * author : Degas
 *
 * API:
 *  check { Function } : 方法；验证数据，接受并返回以下参数；函数
 *      input { Array/Object } : 接受参数；待验证数据，对象包括以下两个成员；对象或数组
 *          input[i].ruler { String/JSON } : 接受参数；验证规则；JSON字符串
 *          input[i].data { String/Other } : 接受参数；待验证参数；各种格式
 *      output { Object } : 返回参数；验证结果，包括以下两个成员；对象
 *          output.status { Number } : 返回参数；验证通过为‘1’，失败为‘0’；对象成员，数字
 *          output.info { String } : 返回参数；验证信息；对象成员，字符串
 *  getData { Function } : 方法；收集数据函数，返回所有需要验证的表单对象；函数；返回值：数组
 *
 *  验证模块使用方法：
 *
 * 验证规则以JSON字符串的形式写在HTML或TPL中
 *  在需要被验证的input/select的节点上加上属性verification，示例如下：
 *  verification = "{'required':true,'type':'email'}"
 *  或
 *  verification = "{'required':true,'type':'select','vacancy':'_0'}"
 *
 *  参数说明：
 *  required 值为true或false，若为true，则此项为必填写项
 *  vacancy 值为某个制定的字符串或者数字，表示此表单已填写的默认值
 *  type 值为email，number，分别验证是否为电子邮箱和数字（数字允许出现‘-’）
 *  type 值为select，则将验证该select是否选择，
 *      此时将当前表单已经填写的数据对比vacancy的值，若值为vacancy默认值，
 *      则表示此select未选择，验证不通过
 */

define( function ( require, exports, module ) {
    // 加载Underscore模块
    var _ = require('underscore');
    
    // 加载jQuery模块
    var $ = require('jquery');

    // 检查待验证数据格式，为单个数据或一组数据
    exports.check = function ( input ) {
        // 返回结果
        var result = {};
        var tmp = {};

        try {
            // 一组数据
            if ( _.isArray( input ) ) {
                for ( var i = 0; i < input.length; i++ ) {
                    // 调用rulerCheck检测
                    tmp = rulerCheck( input[i].name, input[i].data, input[i].ruler, input[i].title );

                    if ( tmp.status === 0 ) {
                        return tmp;
                    } else {
                        result[ input[i].name ] = input[i].data;
                    }
                } // END for

            // 单个数据
            } else if ( _.isObject( input ) ) {
                // 调用rulerCheck检测
                tmp = rulerCheck( input.name, input.data, input.ruler, input.title );

                if ( tmp.status === 0 ) {
                    return tmp;
                } else {
                    result = input;
                }

            } else if ( _.isEmpty( input ) ) {
                throw new Error('待验证数据为空!');
            } else {
                throw new Error('待验证数据格式不正确!');
            }
        } catch (e) {
            if ( typeof console === undefined ) {
                console.error( e.message );
            } else {
                console = {};
                alert( e.message );
            }
        }

        return result;
    };// END check 检查数据

    // 验证规则
    var rulerCheck = function ( name, data, ruler, title ) {
        var result = {};
        ruler = eval( '(' + ruler + ')' );

        // 是否允许为空
        if ( ruler.required && /^\s*$/.test( data ) ) {
            result.info = ' " ' + title + ' " ' + '不能为空';
            result.status = 0;
            return result;
        }

        // 数据类型为数字
        if ( ruler.type === 'number' && !/^(\d{1,}-){0,}\d*$/.test( data ) ) {
            result.info = ' " ' + title + ' " ' + '格式不是数字';
            result.status = 0;
            return result;
        }

        // 数据类型为邮箱
        if ( ruler.type === 'email' && !/^\w{1,}@.{1,}\.{1,}\w{1,}$/.test( data ) ) {
            result.info = ' " ' + title + ' " ' + '格式不正确';
            result.status = 0;
            return result;
        }

        // 验证select表单
        if ( ruler.type === 'select' && ruler.vacancy === data ) {
            result.info = ' " ' + title + ' " ' + '未选择';
            result.status = 0;
            return result;
        }

        // 验证全部通过
        result.status = 1;
        result.info = '"' + title + '"' + '通过验证';

        return result;
    };// END check

    // 验证信息
    exports.output = {};

    // 收集当前页面上所有需要验证的表单值
    exports.getInputData = function () {
        // 数据队列
        var data = [];

        $("[verification]").each(function(i){
            data.push({
                name  : $(this).attr('name'),
                data  : $(this).val(),
                ruler : $(this).attr('verification'),
                title : $(this).attr('title')
            });
        });

        return data;
    };// END getData
});
