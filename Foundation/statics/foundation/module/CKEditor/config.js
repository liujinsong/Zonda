/**
 * 配置编辑器
*/
    CKEDITOR.editorConfig = function ( config ) {
        config.toolbar = 'Full';

        // 为在线投稿自定义编辑器的工具条
        config.toolbar_contribute = [
            { name : 'basicstyles', items : [ 'Bold', '-', 'Italic', '-', 'Underline'] },
            { name : 'colors', items : [ 'TextColor', '-', 'BGColor' ] },
            { name : 'styles', items : [ 'Font', 'FontSize' ] },
            { name : 'paragraph', items : [ 'NumberedList', '-', 'BulletedList' ] },
            { name : 'paragraph', items : [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
            { name: 'basicstyles', items : [ 'RemoveFormat' ] },
            { name: 'links', items : [ 'Link', '-', 'Unlink' ] },
            { name: 'insert', items : [ 'Image', 'Flash', '-', 'Table', 'HorizontalRule', '-', 'SpecialChar' ] }
        ];

        // 工具条全部按钮
        config.toolbar_Full = [
            { name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
            { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
            { name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton',
                'HiddenField' ] },
                '/',
            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
            { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
                '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
            { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
            { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
                '/',
            { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
            { name: 'colors', items : [ 'TextColor','BGColor' ] },
            { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
    ];
    };
