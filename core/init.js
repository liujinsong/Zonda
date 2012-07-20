/**
 * SeaJS 入口文件
 * author shiyang@epptime.com
 * copyright epp.studio
 */
// 配置loader
seajs.config({
    //顶级标识
    //base : '最好不要配置此项',

    // 配置别称
    alias : {
        'underscore' : '/path/to/lib/underscore-min.js',
        'jquery' : '/path/to/lib/jquery-1.7.2.min.js',
        'backbone' : '/path/to/lib/backbone-min.js',
        'pngFix' : '/path/to/lib/DD_belatedPNG_0.0.8a-min.js'
    },

    // 预加载
    preload : [
        'plugin-text',
        this.JSON ? '' : '/path/to/lib/json',
        Function.prototype.bind ? '' : '/path/to/lib/es5-safe'
    ],

    charset : 'utf-8'
});

// 加载主文件
seajs.use('/path/to/root');
