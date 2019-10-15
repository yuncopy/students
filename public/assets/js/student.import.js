;
$(document).ready(function () {
     // BUG 解决方案  https://github.com/fex-team/webuploader/issues/1492
    // 只需要加载一次
    var webuploaderBug;
    $('#studentImport').on('shown.bs.modal', function (e) {
        if(!webuploaderBug){
            UploadExcel.initExcel(); // 初始化上传
            $('#BtnstudentImport').attr('disabled','disabled');  // 不显示上传按钮
        }
    });
    
   
    // 获取地址动态导入数据
    var process = function(file_name,ob){
        console.log(ob);
        var func = setInterval(function(){
            $.post( STUDENT_IMPORT_PROCESS ,{file_name:file_name}, function(msg){
                console.log(msg);
                var idx = msg.status;
                var cont = msg.message;
                var show ='<p class="text-info"><span class="badge badge-danger">'+idx+'</span>'+cont+'</p>';
                ob.prepend(show);
            },'json');
       },1000); 
       return func;
    };
      
    //执行导出数据操作
    $('#BtnstudentImport').click(function(){
        /*
        layer.open({
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['420px', '420px'], //宽高
            content: '<div class="ibox-content" id="process-content"></div>'
        }); */
        var importWave = $('#studentImportwave');
        var bStudentImport = $(this);
        bStudentImport.attr('disabled','disabled');   // 静止再次单击确定按钮
        importWave.show();   // 显示加载框
        $.post( STUDENT_IMPORT_ACTION , function(msg){
            /* 效果不太好，不使用了
            var cont = msg.data;
            if(cont.all){
                var ob = $('#process-content');
                //var tt = process(cont.all,ob);
            }*/
            //console.log(msg);
            if(msg.data){
                layer.alert(msg.message+':'+msg.data, {
                    icon: 0,
                    closeBtn: 0  // 不开启关闭按钮
                }, function(index){
                    layer.close(index);
                    importWave.hide();
                    bStudentImport.removeAttr('disabled');
                });
            }else{
                importWave.hide();
                bStudentImport.removeAttr('disabled');
                layer.alert(msg.message, {icon: 1});
            }
        },'json');
    });
});

