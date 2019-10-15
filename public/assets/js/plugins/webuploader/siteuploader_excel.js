;var UploadExcel = {
    initExcel : function(){
         var $ = jQuery,
            $list = $('#thelist'),
            $btn = $('#ctlBtn'),
            state = 'pending',
            uploader;
        //console.log(uploader);
        uploader = WebUploader.create({
            auto:false,  // 不自动上传
            resize: false,// 不压缩image
            swf: BASE_URL + '/js/Uploader.swf', // swf文件路径
            threads:1, //上传并发数。允许同时最大上传进程数。
            server: UPLOAD_URL,  // 文件接收服务端。

            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#picker',

            fileNumLimit: 300,    // 只允许选择文件，可选。
            fileSingleSizeLimit: 50 * 1024 * 1024,// 限制在50M  
            accept: {
                title: 'Excel',
                extensions: 'xls,xlsx',
                mimeTypes: 'application/*'
            }
        });

        // 当有文件添加进来的时候
        uploader.on( 'fileQueued', function( file ) {
            $list.append( '<div id="' + file.id + '" class="item">' +
                '<h4 class="info">' + file.name + '</h4>' +
                '<p class="state">等待上传...</p>' +
            '</div>' );
        });

        // 文件上传过程中创建进度条实时显示。
        uploader.on( 'uploadProgress', function( file, percentage ) {
            var $li = $( '#'+file.id ),
                $percent = $li.find('.progress .progress-bar');

            // 避免重复创建
            if ( !$percent.length ) {
                $percent = $('<div class="progress progress-striped active">' +
                  '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                  '</div>' +
                '</div>').appendTo( $li ).find('.progress-bar');
            }

            $li.find('p.state').text('上传中');

            $percent.css( 'width', percentage * 100 + '%' );
        });

        uploader.on( 'uploadSuccess', function( file ) {
            $( '#'+file.id ).find('p.state').text('已上传');
            //解除确定上传按钮
            $('#BtnstudentImport').removeAttr('disabled');
        });

        uploader.on( 'uploadError', function( file ) {
            $( '#'+file.id ).find('p.state').text('上传出错');
        });

        uploader.on( 'uploadComplete', function( file ) {
            $( '#'+file.id ).find('.progress').fadeOut();
        });

        uploader.on( 'all', function( type ) {
            if ( type === 'startUpload' ) {
                state = 'uploading';
            } else if ( type === 'stopUpload' ) {
                state = 'paused';
            } else if ( type === 'uploadFinished' ) {
                state = 'done';
            }

            if ( state === 'uploading' ) {
                $btn.text('暂停上传');
            } else {
                $btn.text('开始上传');
            }
        });

        $btn.on( 'click', function() {
            if ( state === 'uploading' ) {
                uploader.stop();
            } else {
                 //点击提交按钮,我们这里要上传图片,以及两个text输入框的值到服务器
                uploader.option('formData',{

                });

                uploader.upload();
            }
        });
        return uploader;
    }
};

