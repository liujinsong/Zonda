/**
 * contribute/index 控制器 editor.js
 * author : Degas
 *
 * CKEDITOR Controller
 * CKEDITOR.instances 对象维护着页面上已经被实例化了的CKEditor编辑器
 * 访问它可获得相应的编辑器的引用，API如下
 * CKEDITOR.instances['editor_id'].setData() : 指定编辑器内的文本/HTML
 * CKEDITOR.instances['editor_id'].getData() : 获得编辑器内的文本/HTML
 * CKEDITOR.instances['editor_id'].setReadOnly( true ) : 使编辑器内的文本/HTML不可编辑
 */
    define( function ( require, exports, module ) {
        /**
         * 上传附加消息处理器
         */
        // 定时器
        var timer_upload;
        var msg_upload = function ( info, type ) {

            clearTimeout( timer_upload );

            if ( type === 'error' ) {
                $("#contribute-upload-block .tip").html( info ).addClass('error');
            } else {
                $("#contribute-upload-block .tip").html( info ).removeClass('error');
            }

            timer = setTimeout(function() {
                // 置空消息DOM
                $("#contribute-upload-block .tip").html('').removeClass('error');
            }, 3000);
        }; // END msg_upload

        /**
         * 提交稿件消息处理器
         */
        var timer;

        var MSG = function ( info, type ) {
            // 清除之前的定时
            clearTimeout( timer );

            if ( type === 'submit' ) {
                $("#contribute-editor-bottom-bar .msg")
                    .addClass('loading')
                    .fadeIn('fast')
                    .text('提交中...');
            } else {
                $("#contribute-editor-bottom-bar .msg")
                    .removeClass('loading')
                    .fadeIn('fast')
                    .text( info );

                // 重新计时
                timer = setTimeout( function () {
                    $("#contribute-editor-bottom-bar .msg")
                        .removeClass('loading')
                        .fadeOut('fast')
                        .text('');
                }, 3000);
            }
        };

        // 加载CKEditor核心文件
        // 这里没有将CKEditor组件化，主要考虑到CKEditor自己要加载很多东西，后期考虑组件化
        require('./CKEditor/ckeditor');

        // 替换并配置编辑器
        CKEDITOR.replace( 'contribute-editor-textarea', {
            toolbar : 'contribute',
            width   : 680,
            height  : 300,
            skin    : 'kama',
            extraPlugins : 'autogrow', // 随内容高度变化
            removePlugins : 'resize', // 关闭可变高宽
            autoGrow_minHeight : 300,
            filebrowserImageUploadUrl : '/attach/addCKEditorFile?upload_type=1' // 上传图片
        });

        // 加载jQuery 模块
        var $ = require('jquery');

        // 加载 Underscore 模块
        var _ = require('underscore');

        // 加载 upload 模块
        var FILE = require('./upload');

        // 监听 upload 模块loading事件
        FILE.loading( function ( msg ) {
            $("#load-gif").fadeIn('fast');
        });

        // 监听 upload 模块ready事件
        FILE.ready( function ( msg ) {
            $("#load-gif").stop().fadeOut('fast');
        });

        window.FILE = FILE;

        // 配置上传文件模块
        FILE.config = {
            url      : '/attach/addfile',
            fileType : 'rar, zip, doc, docx, pdf, xls, xlsx',
            msg      : msg_upload,
            tpl      : require('/Public/Js/tpl/file_cell.tpl'),
            DOMWrap  : document.getElementById('contribute-file-list'),
            ieUploadForm : document.getElementById('contribute-file-upload-form')
        };

        /**
         * 文件控制器
         * 调用Upload模块,完成上传等工作
         */
        function fileHandler ( files ) {
            FILE.upload( files );
        }

        /**
         * 拖拽文件上传
         */
        document.getElementById('contribute-upload-block').ondragenter = function ( event ) {
            // 必须阻止事件冒泡
            event.stopPropagation();

            // 阻止默认动作
            event.preventDefault();

            $("#contribute-upload-block").addClass('contribute-upload-block-dragenter');

            $("#contribute-file-list").show();

            msg_upload('将文件拖拽到这里上传!');
        };

        document.getElementById('contribute-upload-block').ondragover = function ( event ) {
            // 必须阻止事件冒泡
            event.stopPropagation();

            // 阻止默认动作
            event.preventDefault();
        };

        // 松开鼠标，停止拖拽时
        document.getElementById('contribute-upload-block').ondrop = function ( event ) {
            // 必须阻止事件冒泡
            event.stopPropagation();

            // 阻止默认动作
            event.preventDefault();

            msg_upload('');

            $("#contribute-upload-block").removeClass('contribute-upload-block-dragenter');

            // 执行上传动作
            fileHandler( event.dataTransfer.files );
        };

        /**
         * 通过 input file 表单选择多文件上传
         */
        $("#contribute-upload-input-file").change( function () {
            $("#contribute-file-list").show();

            fileHandler( this.files );
        });

        /**
         * 关键字自动提取
         */
        var getKeyWords = function ( data, callback ) {
            if ( /^\s*$/.test( data ) ) {
                return '';
            } else {
                $.ajax({
                    url : '/contribute/getkeyword/',
                    type : 'POST',
                    dataType : 'json',
                    data : { 'content' : data },
                    success : function ( DATA ) {
                        if ( DATA.status === 0 ) {
                            MSG( DATA.info );
                        } else {
                            // 执行回调
                            callback( DATA.data );
                        }
                    },
                    error : function ( data, text ) {
                        if ( text === 'error' ) {
                            MSG( '出现错误请检查网络连接！' );
                        } else {
                            MSG( '关键词提取返回的数据不为JSON！' );
                        }
                    }
                }); // END ajax
            }
        }; // END getKeyWords

        // ckeditor iframe
        var ckeditorFrame = null;

        // 当CKEditor的内容改变时，自动提取关键词
        var autoGetKeyWords = function ( status ) {
            if ( status === 'stop' ) {
                // 停止自动获取
                if ( ckeditorFrame !== null ) {
                    ckeditorFrame.onkeyup = '';
                    return 1;
                }
            } else {
                // 检查CKEditor是否已经加载完毕
                for ( var i = 0; i < frames.length; i++ ) {

                    if ( frames[i].document.title === "CKEDITOR" ) {
                        ckeditorFrame = frames[i];
                        break;
                    }
                }

                if ( ckeditorFrame !== null ) {
                    var data = CKEDITOR.instances['contribute-editor-textarea'].getData();
                    getKeyWords( data , function ( keywords ) {
                        $("#contribute-textarea-keywords").val( keywords );
                    });

                    ckeditorFrame.onkeyup = function () {
                        var data = CKEDITOR.instances['contribute-editor-textarea'].getData();
                        getKeyWords( data , function ( keywords ) {
                            $("#contribute-textarea-keywords").val( keywords );
                        });
                    };

                    return 1;
                }
            }

            window.setTimeout( autoGetKeyWords, 300 );
        }; // END autoGetKeyWords

        // 提取关键词，checkbox 事件绑定
        $("#keyword_auto_get").click( function () {
            if ( $(this).attr('checked') ) {
                autoGetKeyWords();
            } else {
                autoGetKeyWords( 'stop' );
            }
        });

        /**
         * 发布/重写/放弃文章
         */
        // 缓存提交数据，以免发生重复提交
        var cache_data = {};

        // 发布文章
        $("#contribute-editor-bottom-bar .submit").click( function(){
            /**
             * 以下参数，加*项为必填项目
             * 标题     title
             * 作者     author
             * 联系电话 phone
             * 邮箱     mail
             * 部门
             * 栏目     cat_id
             * 内容     content
             * 附件
             * 关键字   keywords
             * 发布状态 status
             * 新闻类型     type
             * 作者类型 admin_id
             */
            // 显示正在提交
            MSG('', 'submit');

            // 加载数据验证模块
            var verify = require('./verification');
            
            // 数据包
            var data = {};

            // 取得CKEditor正文信息
            var editor_content = CKEDITOR.instances['contribute-editor-textarea'].getData();

            var _DATA = verify.getInputData();

            var result = verify.check( _DATA );

            // 验证失败，显示错误信息
            if ( result.status === 0 ) {
                MSG( result.info );
                return false;
            // 验证正文是否已经填写，将数据提交
            } else if ( /^\s*$/.test( editor_content ) ) {
                MSG( '正文为空' );
                return false;
            // 验证成功，组织数据
            } else {
                // 获得返回的处理好的数据
                data = result;

                // TODO 以下三项为预设值，需要Debug
                data.type = 1;
                data.status = 1;
                data.admin_id = 4;
                data.cat_id = 25;

                // 将附件链接插入正文信息尾部
                $("#contribute-file-list a").each( function (i) {
                    editor_content = editor_content + '<br />' + '<a target="_blank" href="' + $(this).attr('href') + '">' + $(this).text() + '</a>';
                });

                // 将正文信息插入data
                data.content = editor_content;

                // 检测是否为刚才已经提交过的数据
                if ( _.isEqual( cache_data, data ) ) {
                    MSG('请不要重复提交相同的内容。');
                    return false;
                }
            }

            // 提交数据
            $.ajax({
                url : '/contribute/saveAdd',
                type : 'POST',
                dataType : 'json',
                data : data,
                success : function ( DATA ) {
                    if ( DATA.status === 0 ) {
                        MSG( DATA.info );
                    } else {
                        // 缓存已经成功提交的数据
                        cache_data = data;

                        MSG( '提交成功，请耐心等待审核。' );

                        window.onbeforeunload = '';

                        window.setTimeout( function () {
                            window.location.reload();
                        }, 1200);
                    }
                },
                error : function ( data, text ) {
                    if ( text === 'error' ) {
                        MSG( '出现错误请检查网络连接！' );
                    } else {
                        MSG( '返回的数据不为JSON！' );
                    }
                }
            });// END ajax
        });// END 发布

        // 重写
        $("#contribute-editor-bottom-bar .reload").click( function(){
            if ( confirm('确定要放弃重写？') ) {
                CKEDITOR.instances['contribute-editor-textarea'].setData('');
            }
        });

        // 放弃
        $("#contribute-editor-bottom-bar .cancel").click( function(){
            if ( confirm('确定要放弃修改离开本页吗？') ) {
                window.history.back();
            }
        });

        // 防手贱关闭窗口时提示
        window.onbeforeunload = function ( event ) {
            return '关闭此页面或者刷新会导致未发布的数据丢失，请先提交';
        };

        // 来源部门select联动
        var origin_data = $("#contribute-origin-category").attr('data');

        try {
            origin_data = eval('(' + origin_data + ')');

            $("#contribute-origin-category").change( function () {
                var _fid = Math.abs( $(this).val() );

                $("#contribute-origin-detail").empty();

                for ( var i in origin_data ) {
                    var name = origin_data[i].name;
                    var full_name = origin_data[i].full_name;
                    var depart_id = origin_data[i].depart_id;
                    var fid = Math.abs( origin_data[i].fid );
                    var option_DOM;

                    if ( fid === _fid ) {
                        option_DOM = '<option value="' + depart_id + '" title="' + full_name + '">' + name + '</option>';
                        $("#contribute-origin-detail").append( option_DOM );
                    }

                }// END for
            });
        } catch (e) {
        }// END try catch

        // 将上传按钮明显标识
        $("#contribute-image-upload-block button").click( function () {
            getUploadButton();
        });

        $("#contribute-image-upload-block .red").click( function () {
            getUploadButton();
        });

        // 获取title为上传的按钮
        var getUploadButton = function () {
            if ( $('a[id^="cke_Upload"]')[0] ) {
                $('a[id^="cke_Upload"]').addClass('Degas-red-tab');
                return false;
            }

            setTimeout( getUploadButton, 300);
        };

    }); // END define
