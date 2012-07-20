#!/usr/local/bin/node
/**
 * less.js
 * node 程序，监听css文件夹，less文件改变时，编译
 */

var fs = require('fs');
var exec = require('child_process').exec;
var path = require('path');

// 设置lessc路径
var lessc = path.resolve('/home/shiyang/node_modules/less/bin/lessc');

// 设置css路径
var cssPath = path.resolve( './', '../');

cssPath = path.dirname( module.filename );

cssPath = path.join( cssPath, '../css' );

// 调用shell运行lessc编译.less文件
var lessToCss = function ( fileName ) {

    if ( !/\.less$/.test( fileName ) ) {
        return false;
    }

    var baseName = path.basename( fileName, '.less' );
    
    exec( '' + lessc + ' ' + cssPath + '/' + fileName + ' > ' + cssPath + '/build/' + baseName + '.css', { encoding: ''},
        function ( err, stdout, stderr ) {
            if ( err !== null ) {
                console.log( err );
                fs.writeFile( cssPath + '/log/' +  baseName + '.log', err, '', function ( error ) {
                    if ( error ) {
                        console.log( 'Write file error: ' + error );
                    }
                }); // END writeFile
            } else {
                console.log( baseName + '.css has render.');
            }
        } // END callBack
    ); // END exec
}; // toCss

// 监听目录
fs.watch( '/home/shiyang/www/Alumni2012/Foundation/statics/foundation/css', function ( event, name ) {
    if ( event === 'change' ) {
        console.log(event);
        lessToCss( name );
    }
});
