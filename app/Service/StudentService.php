<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:AOC界面处理
 * 
 */
namespace App\Service;
use App\Model\StudentsModel;
use App\Service\SemesterService;
use App\Service\TutorService;
use App\Service\UploadService;
use App\Library\PExcel;
use App\Library\Session;

class StudentService extends CommonService{
    
    public static $studentExportKey = 'studentExport';
    public static $studentIpportKey = 'studentImport';
    public static $studentRepeatKey = 'studentRepeat';
    

    /**
     * 获取数据
     */
    public static function getList($searchdata){
        $where = $output = [];
        if($searchdata){
            parse_str ( $searchdata ,  $output );
            foreach ($output as $key => $value){
                if($value){  // enrolment
                    if(is_array($value)){
                        $start = $value[0];
                        $end = $value[1];
                        if(strlen($start) > 1) $where[] = [$key,'>=',$start];
                        if( strlen($end) > 1 ) $where[] = [$key,'<=',$end];
                    }else{
                        $where[] = [$key,'=',$value];
                    }
                }
            }
            // 使用SESSION 缓存搜索条件，方便使用再导出数据查询
            Session::set(self::$studentExportKey , $where);
        }
        

        $dataTable = (new StudentsModel)->getList($where);
        //$sql = (new StudentsModel)->toSql();
        $dataTable->setFormatRowFunction(function ($row) {
            return [
                'id'        =>  $row['id'],
                'number'    =>  $row['number'],
                'username'  =>  $row['username'],
                'my_mobile' =>  $row['my_mobile'],
                'enrolment' =>  $row['enrolment'],
                'status'    =>  $row['status'],
                'action'    =>  $row['id']
            ];
        });
       return $dataTable->make();
    }
    
    
    
    //获取某些条件学生
    public function getWhereData($postdata){
        $where = $output = [];
        $searchdata = $postdata['data']['wheredata'];
        parse_str ( $searchdata ,  $output );
        foreach ($output as $key => $value){
            $where[] = [$key,'=',$value];
        }
        $column =[
            'username'
        ];
        $search = !empty($postdata['query']['search']) ? $postdata['query']['search'] : [];
        $take = $postdata['query']['page'];
        $data =(new StudentsModel)->getWhereData($where,$column,$search,$take);
        return $data;
    }
    
    
    // 简单条件查询
    public function getWhereStudentsData($wheredata){
        return (new StudentsModel)->getStudents($wheredata);
    }
    

    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new StudentsModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new StudentsModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    // 处理导出数据
    public static function makeDataExport($data){
        if($data){
            $outData = [];
            $gender = [
                1=>'女',
                2=>'男',
                3=>'保密'
            ];
            $semesters = SemesterService::getAllsData();
            $tutors = TutorService::getTutors();
            $TData = null;
            array_map(function($var)use(&$TData){
                $var['number_name'] = "[{$var['number']}] {$var['name']}";
                $TData[]= $var;
            }, $tutors);
            $semester_id = array_column($semesters, 'name','id');  //学期
            $tutor_id = array_column($TData, 'number_name','id');  // 导师
            foreach ($data as $key => $value){
                $value['gender'] =  $gender[$value['gender']];
                $value['semester_id'] =  isset($semester_id[$value['semester_id']]) ? $semester_id[$value['semester_id']] : 'N/A';
                $value['tutor_id'] =  isset($tutor_id[$value['tutor_id']]) ? $tutor_id[$value['tutor_id']] : 'N/A';
                $outData[] = $value;
            }
            return $outData;
        }
        return false;
    }

    

    /**
     * 导出学生数据
     */
    public static function exportData($inputData=null,$makedir = null){
     
        $students = new StudentsModel();
        if(is_null($inputData)){  // 导出数据
            $whereData = Session::get(self::$studentExportKey);
            $data = $students->getStudents($whereData);
            $data = self::makeDataExport($data);
            $columnsAlias = self::columnsAlias();
            $Columns = array_column ( $columnsAlias ,  'type' ,  'alias' );
            $alias = array_column ( $columnsAlias ,  'name' ,  'alias' );
            $title = self::makeStudentTitle($Columns);  // $students->getTableColumns()
            $dataExport =[
                'title'=>$title,
                'alias'=>$alias,
                'data'=>$data,
                'filename'=>'student'.date('YmdHis')
            ];
        }else{
            //导出有重复的数据需要生成表格提供下载
            $data = $inputData;
            $title = self::makeStudentTitle($students->getTableColumns());
            unset($title['id']);
            unset($title['create_time']);
            unset($title['update_time']);
            $dataExport =[
                'title'=>$title,
                'data'=>$data,
                'filename'=>'repeat_student'.date('YmdHis')
            ];
        }
      
        if($data){
            return (new PExcel)->Export($dataExport,$makedir);
        } 
    }
    
    // 处理表头
    public static function  makeStudentTitle($title){
        $t = [];
        if($title){
            $s = 65;  //  A 
            foreach ($title as $key => $val){
               $t[$key]  = [
                   'col' => chr ( $s ),
                   'name'=> $key,
                   'type'=> $val
               ];
                ++$s;
            }
        }
        return $t;
    }
    
    // 导入数据缓存文件名路径
    public static function studentImportFile($data=null){
        $studentIpportKey = self::$studentExportKey;
        if(is_null($data)){
            return Session::get($studentIpportKey);  //获取数据
        }else{
            return Session::set($studentIpportKey , $data);  // 缓存数据
        }
    }
    
    // 导出重复的数据
    public static function studentRepeatFile($data=null){
        $studentRepeatKey = self::$studentRepeatKey;
        if(is_null($data)){
            return Session::get($studentRepeatKey);  //获取数据
        }else{
            return Session::set($studentRepeatKey , $data);  // 缓存数据
        }
    }
    
    // 设置
    public static function columnsAlias(){
        return  [
            ['type'=>'int','name'=>'编号ID','alias'=>'id'],
            ['type'=>'string','name'=>'学生学号','alias'=>'number'],
            ['type'=>'string','name'=>'学生姓名','alias'=>'username'],
            ['type'=>'int','name'=>'性别','alias'=>'gender'],
            ['type'=>'int','name'=>'学期ID','alias'=>'semester_id'],
            ['type'=>'int','name'=>'导师姓名','alias'=>'tutor_id'],
            ['type'=>'string','name'=>'入学日期','alias'=>'enrolment'],
            ['type'=>'string','name'=>'学生状态','alias'=>'status'],
            ['type'=>'string','name'=>'学生手机号','alias'=>'my_mobile'],
            ['type'=>'string','name'=>'学生邮箱','alias'=>'email'],
            ['type'=>'string','name'=>'添加日期','alias'=>'create_time'],
            ['type'=>'string','name'=>'修改日期','alias'=>'update_time']
        ];
    }

    
    // 导入数据操作 Test
    public static function studentImportDataTest(){
       //测试
        $file_success = __UPLOAD__.date('YmdHis').'_success.text';
        $file_failure = __UPLOAD__.date('YmdHis').'_failure.text';
        for($i=0;$i<60;$i++){
            $content = date('Y-m-d H:i:s')."||".$i;
            if($i%2 == 0){
                file_put_contents($file_success,$content.PHP_EOL, FILE_APPEND );
            }else{
                file_put_contents($file_failure,$content.PHP_EOL, FILE_APPEND );
            }
            echo $content;
            sleep(2);
        }
    }

    // 导入数据操作
    public static function studentImportData(){
        $fileData = self::studentImportFile();//上传文件
        if(is_null($fileData)){
            $fileData = UploadService::uploadImportFile();
        }
        $filePathName = $fileData['file'];
        $students = new StudentsModel();
        //p($students->getTableColumns());
        $columnsAlias = self::columnsAlias();
        $Columns = array_column ( $columnsAlias ,  'type' ,  'name' );
        $alias = array_column ( $columnsAlias ,  'alias' ,  'name' );
  
        $title = self::makeStudentTitle($Columns);  // 设置头信息
        $importData = (new PExcel)->Import(['file'=>$filePathName,'title'=>$title,'alias'=>$alias]);
        $data = [];
        if($importData){
            $data = $students->setBatchStudents($importData);
        }
        return $data;
    }
     
    // 删除学生操作
    public static function studentDelOne($id){
        if($id){
            return (new StudentsModel())->delStudents($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new StudentsModel)->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new StudentsModel)->getUniqueData($id);
    }
    
    
    /**
     * 获取某一个学期学生数据
     */
    
    public static function getSemesterData($semester_id){
        return (new StudentsModel)->getSemesterData($semester_id);
    }
    
    
}