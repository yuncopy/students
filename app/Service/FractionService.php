<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\FractionsModel;
use App\Service\CategoryService;
use App\Service\StudentService;
use App\Service\SemesterService;
use App\Service\SubjectsService;
use App\Library\Session;


class FractionService extends CommonService{
    
    static $fractionsExportKey = 'fractionsExportKey';




    /**
     * 获取数据
     */
    public static function getList($searchdata){
        
        $Fractions = new FractionsModel();
        $where = $output = [];
        if($searchdata){
            parse_str ( $searchdata ,  $output );
            foreach ($output as $key => $value){
                if($value){  
                    if($key == 'score'){  // 分数处理
                        $score = explode('-', $value);
                        $where[] = [$key,'>=',$score[0]];
                        $where[] = [$key,'<=',$score[1]];
                    }else{
                        $where[] = [$key,'=',$value];
                    }
                }
            }
            // 使用SESSION 缓存搜索条件，方便使用再导出数据查询
           Session::set(self::$fractionsExportKey , $where);
        }
      
        $dataTable = $Fractions->getList($where);

        
        $dataTable->setFormatRowFunction(function ($row){

            // 需要优化
            $student = StudentService::getOneData($row['student_id']);
            $semester = SemesterService::getOneData($row['semester_id']);
            $subjects = SubjectsService::getOneData($row['subject_id']);
            $score_grade = CategoryService::getScoreGrade($row['score']);
            $elective_id = $row['elective_id']; // 选课课程ID

            $elective =  ElectiveService::getOneElectives($elective_id);

            $row_data = json_encode($elective);  // 查看更多操作

            return [
                'id'            =>  $row['id'],
                'semester_id'   =>  $semester['name'],
                'student_id'    =>  '['.$student['number'].'] '.$student['username'],
                'subject_id'    =>  $subjects['name'],
                'create_time'   =>  $row['create_time'],
                'elective'      =>  $row_data,
                'score'         =>  $row['score'],
                'elective_id'   =>  $row['elective_id'],
                'score_grade'   =>  $score_grade,
                'comment'       =>  $row['comment'],
                'action'        =>  $row['id']
            ];

        });
       return $dataTable->make();
    }
    
  

    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new FractionsModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new FractionsModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function DelOne($id){
        if($id){
            return (new FractionsModel())->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new FractionsModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new FractionsModel)->getUniqueData($id);
    }
    
    
   
    
    
}