/**
 * SeaJS 入口文件
 * author shiyang@epptime.com
 * copyright epp.studio
 */
// 配置loader
seajs.config({
    //顶级标识
    //base : '最好不配置此项',

    // 配置别称
    alias : {
        'underscore' : 'path/lib/underscore-min.js',
        'jquery' : 'path/lib/jquery-1.7.2.min.js',
        'backbone' : 'path/lib/backbone-min.js',
        'pngFix' : 'path/lib/DD_belatedPNG_0.0.8a-min.js'
    },

    // 预加载
    preload : [
        'plugin-text',
        this.JSON ? '' : 'path/lib/json',
        Function.prototype.bind ? '' : 'path/lib/es5-safe'
    ],

    charset : 'utf-8'
});

// 加载主文件
seajs.use('path/root');
