<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:AOC界面处理
 * 
 */
namespace App\Service;
use App\Model\ContentsModel;
class ContentbService extends CommonService{
    
    
    
    /**
     * 2017年12月8日14:40:15
     * Angela
     * 添加,编辑数据
     */
    public static function insertData($data,$where=[]){
        return (new ContentsModel)->insertOrUpdate($data,$where);
    }
    
    /**
     * 2017年12月9日11:06:33
     * Angela
     * 获取单条内容
     */
    public static function getDetail($id){
        return (new ContentsModel)->getOne($id);
    }
    
    /**
     * 2017年12月9日12:13:10
     * Angela
     * 删除单条数
     */
    public static function delOneContent($id){
        return (new ContentsModel)->delOne($id);
    }

    /**
     * 2017年12月8日12:07:00
     * Angela
     * 验证表单
     */
    public static function filter_match($pattern,$subject){
       $matches = null;
       preg_match ( $pattern ,  $subject ,$matches  );
       if($matches){
           return $matches;
       }else{
           return false;
       }
    }
    
    
    /**
     * 2017年12月8日14:36:43
     * Angela
     * 验证
     */
    public static function filter_input_value($post){
        
        // 表单验证 
        $name = null;
        try {
            $title = filter_input (INPUT_POST, 'title');
            if (!$title || mb_strlen($title) < 2) {
                $name = 'title';
                throw new \Exception('标题名称必须大于 2 字符');
            }
            $version = $post['version'];
            if(!self::filter_match("/^v[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}$/",$version)){
                $name = 'version';
                throw new \Exception('版本号必须严格按照格式要求');
            }
            
            $iconurl = filter_input (INPUT_POST, 'iconurl',FILTER_VALIDATE_URL);
            if(!$iconurl){
                $name = 'iconurl';
                throw new \Exception('必须选择图标');
            }
            $scenes =  filter_input (INPUT_POST, 'scenes',FILTER_VALIDATE_URL);
            if(!$scenes){
                $name = 'scenes';
                throw new \Exception('必须选择游戏场景图');
            }
            
            $seeds = filter_input (INPUT_POST, 'seeds',FILTER_VALIDATE_URL);
            if(!$seeds){
                $name = 'seeds';
                throw new \Exception('必须选择游戏安装包');
            }
            
            $texts = filter_input (INPUT_POST, 'texts');
            if (!$texts || mb_strlen($texts) < 10) {
                $name = 'texts';
                throw new \Exception('游戏介绍必须大于 10 字符');
            }
            
        }catch (\Exception $e) {
            header('HTTP1.1 400 Bad Request');
            die(json_encode(['status'=>400,'name'=>$name,'message'=>$e->getMessage()]));
        }
    }
    
    
}