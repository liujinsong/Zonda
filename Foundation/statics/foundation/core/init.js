/**
 * SeaJS 入口文件
 * author shiyang@epptime.com
 * copyright epp.studio
 */
// 配置loader
seajs.config({
    //顶级标识
    base : '/statics/foundation',

    // 配置别称
    alias : {
        'underscore' : './lib/underscore-min.js',
        'jquery' : './lib/jquery-1.7.2.min.js',
        'backbone' : './lib/backbone-min.js',
        'pngFix' : './lib/DD_belatedPNG_0.0.8a-min.js'
    },

    // 预加载
    preload : [
        'plugin-text',
        this.JSON ? '' : '../lib/json',
        Function.prototype.bind ? '' : '../lib/es5-safe'
    ],

    charset : 'utf-8'
});

// load main js
seajs.use('./root');
