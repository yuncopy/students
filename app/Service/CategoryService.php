<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\CategoryModel;

class CategoryService extends CommonService{
    
    /**
     * 获取分类信息
     * getWeeks
     */
    public static function getCates($data=null){
        $object = (new CategoryModel())->getCates($data);
        $object2array = json_decode( json_encode( $object ),true);
        return $object2array;
    }
    
    // 教室
    public static function roomData($data){
        $rooms = [];
        if($data && !$rooms){
            //$object2array =  json_decode( json_encode( $data ),true);
            $treeRoom = make_tree_with_namepre($data);
            $rooms = make_tree2list($treeRoom);
        }
        return $rooms;
    }

    /**
     * 检测唯一性
     * $data =[$key,$val,$cate];
     */
    public static function uniqueData($data=[]){
        if(is_array($data) && count($data) == 3){
            $resObject = (new CategoryModel)->getUniqueData($data);
            $object2array =  json_decode( json_encode( $resObject ),true);
            return $object2array;
        }
        return false;
    }
    
     /**
     * 编辑数据
     */
    public static function updateData($cate,$data,$id){
        if(is_array($data) && $data && $cate && is_array($id)){
            return (new CategoryModel())->insertOrUpdate($cate,$data,$id);
        }
        return false;
    }
    

    /**
     * 删除数据
     */
    public static function delData($cate,$id){
        return (new CategoryModel)->delData($cate,$id);
    }
    

    /**
     * 插入数据
     */
    public static function insertData($cate,$data){
        if(is_array($data) && $data && $cate){
            return (new CategoryModel())->insertOrUpdate($cate,$data);
        }
        return false;
    }
    
    
    //计算分数等级
    public static function getScoreGrade($score){
        $grades = self::getCates('grade');
        if($grades){
            $grade = [];
            foreach ($grades as $value){
                $grade [$value['name']] = $value['value'];
            }
            $gradeName = 'N/A';
            foreach ($grade as $key => $v){
                $pos = strpos($v, '-');
                $start = substr($v, 0,$pos);
                $end = substr($v,$pos+1);
                if(($start <= $score ) && ( $score <= $end)){
                    $gradeName = $key;                   
                    break;
                }
            }
            return $gradeName;
        }
    }

    
}