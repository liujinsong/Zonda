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
        'underscore' : '/statics/foundation/lib/underscore-min.js',
        'jquery' : '/statics/foundation/lib/jquery-1.7.2.min.js',
        'backbone' : '/statics/foundation/lib/backbone-min.js',
        'pngFix' : '/statics/foundation/lib/DD_belatedPNG_0.0.8a-min.js'
    },

    // 预加载
    preload : [
        'plugin-text',
        this.JSON ? '' : '/statics/foundation/lib/json',
        Function.prototype.bind ? '' : '/statics/foundation/lib/es5-safe'
    ],

    charset : 'utf-8'
});

// 加载主文件
seajs.use('/statics/foundation/root');
