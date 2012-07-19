/**
 * 上传文件模块 upload.js
 * author : Degas
 *
 * 文件对象构造函数为 File
 * API分为
 * 1,上传接口: 以XHR方式上传，可显示进度条;
 *             以同步表单提交的方式，需要新建form和iframe，并在上传成功后删除form和iframe
 * 2,DOM接口: add方法新建File对象的DOM形态
 *            delete方法删除File对象的DOM形态
 *            uploading方法控制DOM形态的“上传中”和“上传成功/失败”等状态
 *
 * 配置参数:
 *          url      : {string} 服务端脚本
 *          fileType : {string} 文件类型,用英文逗号","隔开
 *          DOMWrap  : {DOM} 原生DOM对象，文件列表包裹器
 *          msg      : {function} 消息回调函数
 *          tpl      : {string} 字符串或文本对象，文件DOM形态模板
 *
 * 模块事件:
 *          loading : 事件，upload方法正在向服务器推送数据
 *          ready   : 事件，服务器响应准备就绪
 */
define( function( require, exports, module ) {
    // 加载 Underscore 模块
    var _ = require('underscore');

    // 加载 jQuery 模块
    var $ = require('jquery');

    // 加载 Backbone 模块
    var Backbone = require('backbone');

    // 用 Backbone 为 exports 做扩展
    _.extend( exports, Backbone.Events );

    // 自定义 loading 模块事件
    exports.loading = function ( callBack ) {
        // 监听 upload 模块 loading 事件
        exports.on('loading', function ( msg ) {
            callBack( msg );
        });
    };

    // 自定义 ready 模块事件
    exports.ready = function ( callBack ) {
        // 监听 upload 模块 ready 事件
        exports.on('ready', function ( msg ) {
            callBack( msg );
        });
    };

    // 配置,初始置为空
    exports.config = {};

    // 必须的参数
    var need = {
        url      : '',
        fileType : '',
        DOMWrap  : '',
        msg      : '',
        tpl      : ''
    };

    // 如果为IE浏览器，则需要配置文件上传表单
    if ( $.browser.msie ) {
        need.ieUploadForm = '';
    }

    // 模块内部缓存配置
    var _config = {};

    /**
     * 检查配置，并重写配置
     */
    var checkConfig = function ( ) {
        // 当配置改变时，重新检查配置
        if ( _.isEqual( _config, exports.config ) ) {
            return false;
        } else {
            _config = exports.config;
        }

        // 检查是否包含必须的配置项
        try {
            _.each( need, function ( em, key ) {
                if ( !_.has( exports.config, key ) ) {
                    throw new Error('上传文件模块未配置' + key );
                }
            });

        } catch (e) {
            if ( window.console !== undefined ) {
                console.error( e.message );
            } else {
                alert( e.message );
            }
        }

        // 解析文件类型为数组
        if ( _.isString( exports.config.fileType ) ) {
            // 去掉空格
            exports.config.fileType = exports.config.fileType.replace(/\s{1,}/g, '');
            // 分割成数组
            exports.config.fileType = exports.config.fileType.split(',');
        }

    }; // END checkConfig

    /**
     * checkFileType 内部方法检查文件类型
     * 接受参数: fileList 文件对象
     * 返回符合要求的文件数组
     */
    var checkFileType = function ( fileList ) {
        // 错误消息队列
        var error_msg = [];

        // 正确文件队列
        var correctFileList = [];
        
        // 检查是否为文件夹
        if ( fileList.length !== undefined && fileList.length === 0 ) {
            error_msg.push( '暂不支持直接上传文件夹，请压缩后上传<br />' );
        } else if ( $.browser.msie ) {
            //兼容IE，检测文件名
            var name = $( exports.config.ieUploadForm ).find('input:file').val().split('.');
            // 后缀，转换为小写
            var suffix = _.last( name ).toLowerCase();

            if ( !_.include( exports.config.fileType, suffix ) ) {
                error_msg.push( '<span class="black">[ ' + name + ' ]</span> 为不允许上传的文件类型<br />' );

                exports.config.msg( error_msg.toString(), 'error' );

                return 0;
            } else {
                // 正确格式的文件压入新的文件队列
                return 1;
            }
        }// END 兼容性 IE

        _.each( fileList, function ( file, key ) {
            var nameArr = file.name.split('.');
            // 后缀，转换为小写
            var suffix = _.last( nameArr ).toLowerCase();

            // 无后缀名
            if ( nameArr.length === 1 ) {
                error_msg.push( '<span class="black">[ ' + file.name + ' ]</span> 无后缀名，请重新选择<br />' );
            } else {
                if ( !_.include( exports.config.fileType, suffix ) ) {
                    error_msg.push( '<span class="black">[ ' + file.name + ' ]</span> 为不允许上传的文件类型<br />' );
                } else {
                    // 正确格式的文件压入新的文件队列
                    correctFileList.push( file );
                }
            } // END if
        }); // END each

        // 输出错误消息
        exports.config.msg( error_msg.toString(), 'error' );

        //返回正确的文件队列
        return correctFileList;

    };// END checkFileType

    /**
     * 获得后端需要的文件类型
     */
    var conv_fileType = function ( fileName ) {
        // 分割文件名
        var nameArr = fileName.split('.');
        // 后缀，转换为小写
        var suffix = _.last( nameArr ).toLowerCase();

        var image = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
        var doc   = ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'];

        if ( _.include( image, suffix ) ) {
            return '1';
        } else if ( _.include( doc, suffix ) ) {
            return '2';
        } else {
            return '3';
        }

    }; // END conv_fileType

    /**
     * 文件DOM形态构造器
     * 属性:
     *     fileType : 文件类型
     *     fileName : 文件名
     *     filePath : 在服务器上的位置
     * 方法：
     *     delete   : 删除DOM形态
     *     loading  : 上传状态
     */
    var DOM = function ( fileName, fileType, fileSize ) {
        this.fileName = fileName;
        this.fileType = fileType;
        this.fileSize = fileSize;

        // 加载模板文件并编译
        var file_DOM = _.template( exports.config.tpl, this);

        // 插入文件DOM列表中
        $( exports.config.DOMWrap ).find('ul').prepend( file_DOM );

        // 获得该DOM节点
        this.dom = $( exports.config.DOMWrap ).find('li').eq(0);

        var _this = this;

        // 绑定事件删除该节点
        $( this.dom ).find('.delete').click( function() {
            _this.dom.remove();
        });

        return this;
    };

    /**
     * 兼容IE的上传方法适配
     */
    // 目标iframe缓存
    var uploadIframe = null;

    // 当前正在上传的文件(文件名)
    var uploadCurrentFileName;

    // ieUpload 内置方法，使用同步表单+iframe形式模拟AJAX提交
    var ieUpload = function () {
        // 获取input:file中要上传的文件名，赋给uploadCurrentFileName
        uploadCurrentFileName = $( exports.config.ieUploadForm ).find('input:file').val();

        // 检查要上传的文件类型是否配置要求
        if ( !checkFileType( uploadCurrentFileName ) ) {
            return false;
        }

        exports.trigger('loading', 'Sending...');

        // 根据配置（同步表单的target）取得返回结果用的iframe，并缓存（性能）
        if ( uploadIframe === null ) {
            uploadIframe = $( 'iframe[name="' + exports.config.ieUploadForm.target + '"]' );
        }

        // 为该iframe绑定事件，根据配置提供的同步文件上传form的target确定目标iframe
        if ( uploadIframe.attr( 'isBind' ) !== 'yes' ) {
            // 绑定成功后为iframe加上属性 isBind=true，防止再次绑定
            uploadIframe.attr( 'isBind', 'yes' );

            uploadIframe.on( 'load', function () {
                // 初始化文件DOM形态
                var File = new DOM( uploadCurrentFileName );

                try {
                    var data = window.frames[ exports.config.ieUploadForm.target ].data;

                    if ( data.status === 1 ) {
                        File.dom.find('a').attr('href', data.data.url );

                        exports.trigger('ready', 'Ready');
                    } else {
                        exports.config.msg('发生错误: ' + File.fileName + data.info, 'error' );

                        File.dom.remove();

                        exports.trigger('ready', 'Error');
                    }

                } catch (e) {
                    exports.config.msg('发生错误: ' + File.fileName + e, 'error' );

                    File.dom.remove();

                    exports.trigger('ready', 'Error');
                }
            });
        } // END bind event

        // JS提交表单
        $( exports.config.ieUploadForm ).submit();

    }; // END ieUpload

    /**
     * upload 对外方法，接受文件对象，并发送文件到服务器
     */
    exports.upload = function ( fileList ) {
        // 检查配置是否满足要求
        checkConfig();

        // 如果为IE浏览器，则使用ieUpload方法上传
        if ( $.browser.msie ) {
            ieUpload();
            return false;
        }

        // 检查要上传的文件类型是否配置要求
        var correctFileList = checkFileType( fileList );

        // 初始化文件的DOM形态,并上传到服务器
        _.each( correctFileList, function ( file, key) {
            // 转化为DOM形态
            var File = new DOM( file.name, file.type, file.size );

            // 对每个文件建立XHR,以及FormData
            var XHR  = new XMLHttpRequest();
            var FileData = new FormData();

            // 将数据加入表单中
            FileData.append( 'file', file );
            FileData.append( 'upload_type', conv_fileType( file.name ) );

            // 向服务器声明以AJAX的方式发送，期望获得AJAX返回
            FileData.append( 'ajax', 1 );

            XHR.open( 'POST', exports.config.url, true );

            exports.trigger('loading', 'Sending');

            // 进度条
            if ( $( File.dom ).find('progress')[0] ) {
                var progress   = $( File.dom ).find('progress')[0];
                progress.min   = 0;
                progress.max   = 100;
                progress.value = 0;

                XHR.onload = function() {
                    $( progress ).hide();
                };

                XHR.upload.onprogress = function ( event ) {
                    if ( event.lengthComputable ) {
                        progress.value =
                        progress.innerHTML = ( event.loaded / event.total * 100 || 0 );
                    }
                };
            } // END progress

            // 获得文件在服务器上的位置
            XHR.onreadystatechange = function () {
                if ( XHR.readyState === 4 ) {
                    try {
                        // 尝试解析JSON返回结果
                        var data = $.parseJSON( XHR.response );

                        if ( data.status === 1 ) {
                            // 以属性的形式将该文件在服务器上的url添加到文件的DOM形态中
                            File.dom.find('a').attr('href', data.data.url);

                            exports.trigger('ready', '完成');
                        } else {
                            exports.config.msg('发生错误: ' + File.fileName + data.info, 'error' );

                            File.dom.remove();

                            exports.trigger('ready', 'Error');
                        }
                    } catch (e) {
                        exports.config.msg('发生错误: ' + e, 'error' );

                        exports.trigger('ready', 'Error');
                    }// END try catch
                }
            };

            // 上传
            XHR.send( FileData );

        });

    }; // END upload

    /**
     * needConfig 对外方法，读取必须配置项
     */
    exports.needConfig = function () {
        return need;
    };

});
