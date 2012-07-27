define( function ( require, exports, module ) {
    document.getElementById('file-input').onchange = function () {
        exports.reader = new FileReader();
        exports.files = this.files;

        window.reader = exports.reader;
        window.files = exports.files;

        exports.reader.addEventListener( 'load', function ( event ) {

            alert('data ready!');

            var data = event.target.result;

            console.log( data );
        });

        exports.reader.onerror = function ( event ) {
            console.log('Error!!');
            console.log( event );
        };

        for ( var i = 0; i < exports.files.length; i++ ) {
            exports.reader.readAsText( this.files[i] );
        }
    };
});
