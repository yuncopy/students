<?php

/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:AOC界面处理
 * 
 */

namespace App\Service;
use App\Library\Upload\Upload;
use App\Library\Redis;

class UploadService extends CommonService {
    
    static $uploadImport = 'studentImportFile';


    /**
     * 批量导入学生信息
     */
    public static function uploadStudent() {

        $upload = new Upload(); // 实例化上传类
        $upload->maxSize = 3145728000; // 设置附件上传大小
        $upload->rootPath = __UPLOAD__; // 设置附件上传根目录，临时设置上传目录  
        $info = $upload->upload(); // 上传文件 
        $update_data = null;
        if (!$info) {// 上传错误提示错误信息
            
            return die(json_encode( 400, ['error' => $upload->getError()])); // 上传文件说明
            
        } else {// 上传成功
            $file = $upload->rootPath . $info['file']['savepath'] . $info['file']['savename']; // 全部路径文件名
            $savepath = $upload->rootPath . $info['file']['savepath'];  // 保存文件路径
            $savename = $info['file']['savename'];
            $update_data = [
                'file'=>$file,
                'savepath' =>$savepath,
                'filename' => $savename
            ];
            self::uploadImportFile($update_data);  // 设置缓存
        }
        return $update_data;
    }
    
     // 导入数据缓存文件名路径
    public static function uploadImportFile($data=null){
        $studentIpportKey = self::$uploadImport;
        $Redis = new Redis;
        if(is_null($data)){
            return json_decode($Redis->get($studentIpportKey),true);  //获取数据
        }else{
            $Redis->delete($studentIpportKey);
            return $Redis->setex($studentIpportKey, 3600,json_encode($data));  // 缓存数据
        }
    }

}
