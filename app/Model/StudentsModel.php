<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;
use LiveControl\EloquentDataTable\DataTable;
use App\Service\RoleService;
use App\Service\UserService;
use App\Service\UserRelationService;


class StudentsModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'students';
    
    protected $switchAuth = true;  // 数据模型开启
    /**
     * 获取数据
     */
    public function getList($where){
        $dataTable = new DataTable(
            $this->where(function($query) use ($where){
                if($where){
                    foreach ( $where as  $value){
                        $query->where($value[0],$value[1] ,$value[2]);
                    }
                }
            }),
            ['id', 'number', 'username', 'gender', 'my_mobile', 'enrolment','status']
        );
        return $dataTable;
    }
    
    /**
     * 获取过滤条件的写生
     */
    public function getWhereData($where,$columns,$search,$take){
 
        if(is_array($where) && !empty($where)){
           return $this->where(function($query) use ($where,$columns,$search){
                if($where){
                    foreach ( $where as  $value){
                       $query->where($value[0],$value[1] ,$value[2]);  //等值查询
                    }
                    if($search){
                        foreach ( $columns as  $column){
                           $query->where($column,'like', '%' . $search . '%');  //等值查询
                        }
                    }
                }
            })->offset($take)->limit(10)->get();
            
        }
    }
    
    
    /**
     * 2017年12月8日14:29:32
     * Angela
     * 修改内容和添加
     */
    public function insertOrUpdate($data,$where=[]){
        if(!empty($data) && !empty($where)){
            return $this->where(function($query) use ($where){
                foreach ($where as $key => $value){
                    $query->where($key, $value);
                }
            })->update($data);
        }else if(!$where){
            return $this->insertGetId($data);
        }
        return false;
    }
    
    /**
     * 查询用户信息
     * @param string $name Description
     */
    public function getUniqueData($name,$value=null){
        
        if(is_null($value)){
            $value = $name;
            $name = 'id';
        }
        $res = $this->where($name, $value)->first();
        if($res){
            return  $res->toArray();
        }
        return false;       
    }
    
    
    /**
     * 
     * 获取全部学生
     */
    public function getStudents($where=[]){
        return $this->where(function($query) use ($where){
            if($where){
                foreach ($where as $key => $value){
                    $query->where($value[0],$value[1] ,$value[2]);
                }
            }
        })->get()->toArray();
    }
    
    
    /**
     * 获取 in 学生信息
     */
    public function getInStudents($data=[]){
        if($data && is_array($data)){
            return  $this->whereIn('number',$data)->get()->toArray();
        }
        return false;
        
    }


    // 获取表字段信息
    public function getTableColumns(){
        $capsule = $GLOBALS['capsule'];
        $schema = $capsule::schema();
        $tableName = $this->getTable();
        $columns = $schema->getColumnListing($tableName); 
        if($columns){
            $tableColumn = [];
            foreach ($columns as $key => $val){
                $columnType = $schema->getColumnType($tableName,$val);
                $tableColumn[$val] = $columnType == 'string' ? 'string' : 'int';
            }
        }
        return $tableColumn;
    }
    
   

      /**
     * 批量导入数据
     * @param array $data 数据库处理的二维数组
     */
    public function setBatchStudents($data){
       
        if($data){
            // 使用事务处
            ini_set('max_execution_time', 600); //300 seconds = 5 minutes
            $capsule = $GLOBALS['capsule'];  //$this->getConnection()->
            $repeatData = $this->getConnection()->transaction(function () use($data,$capsule){
                
                $role_id = RoleService::customRole('student') ; // 当前学生角色
                $table = $this->getTable();
                $student_data = [];
                $numbers_data = [];
                $user_data = [];
                foreach ($data as $key => $value){
                    $student_data[$key]['number'] = $value['number'];
                    $student_data[$key]['username'] = $value['username'];
                    $student_data[$key]['gender'] = isset($value['gender']) ? $value['gender'] : 3;
                    $student_data[$key]['semester_id'] = isset($value['semester_id']) ? $value['semester_id'] : 0;
                    $student_data[$key]['tutor_id'] = isset($value['tutor_id']) ? intval($value['tutor_id']) : 0;
                    $student_data[$key]['enrolment'] = isset($value['enrolment']) ? $value['enrolment'] : '';
                    $student_data[$key]['my_mobile'] = isset($value['my_mobile']) ? trim($value['my_mobile']) : '';
                    $student_data[$key]['email'] =  isset($value['email']) ? trim($value['email']) : '';
                    $numbers_data[] = $value['number'];
                }
                try{
                    // 批量添加学生数据
                    $stmt = $capsule::table($table)->insert($student_data);  // 添加学生
                    if($stmt){
                                          
                        // 获取学生ID
                        $students = $this->getInStudents($numbers_data);
                        $student_ids = array_column($students, 'number', 'id');
                        //批量添加用户
                        foreach ($students as $k => $val){
                            $pass = __PASS__ == false ? trim($val['number']) : __PASS__; // 默认密码不设置，则使用学号
                            $pass_hash = pass_hash($val['number'].$pass); 
                            $user_data[$k]['username'] = $val['number'];
                            $user_data[$k]['passwd'] = $pass_hash;
                            $user_data[$k]['role_id'] = $role_id;
                            $user_data[$k]['email'] = isset($val['email']) && !empty($val['email']) ? $val['email'] : '';
                        }
                        
                        // 执行批量添加
                        $table_user= UserService::getTableName();
                        $stmt_user = $capsule::table($table_user)->insert($user_data);  // 添加用户
                        if($stmt_user){
                           
                            // 获取插入用户ID
                            $users_info = UserService::getInUsers($numbers_data);
                            $user_ids = array_column($users_info, 'id', 'username');
                            $userRelation = [];
                            foreach ($student_ids as $ky => $vl){
                                $userRelation[$ky]['role_id']  = $role_id; 
                                $userRelation[$ky]['user_id']  =$user_ids[$vl]; // 用户ID
                                $userRelation[$ky]['relation_id']  = $ky;// 学生数据表ID   
                            }
                            $table_relation = UserRelationService::getTaleName();
                            $stmt_relation = $capsule::table($table_relation)->insert($userRelation);  // 添加用户
                            if($stmt_relation){
                               return true;  // 成功
                            }
                        }
                    }
                    return false; // 失败
                } catch (\Illuminate\Database\QueryException $e){  // 抛出异常
                    $report = $e->getMessage();
                }
                return $report;
                
                /*  拆分代码,减少数据库I/O操作
                $file_success = __UPLOAD__.date('YmdHis').'_success.text';
                $file_failure = __UPLOAD__.date('YmdHis').'_failure.text';
                foreach($data as $val){
                    try{
                        $insertResId = $this->insertGetId($val);//先导入学生表数据库
                        $pass = __PASS__ == false ? trim($val['number']) : __PASS__;
                        $userData = [
                            'username'  => $val['number'],
                            'passwd'    => password_hash( $pass ,PASSWORD_DEFAULT), // 学号为默认密码
                            'role_id'   => $role_id,
                            'email'     => isset($val['email']) && !empty($val['email']) ? $val['email'] : ''
                        ];
                        $insertUserId = UserService::insertData($userData);  //添加入用户数据库
                         
                        // 关系表
                        $relation = UserRelationService::insertData([
                            'role_id'  => $role_id,  
                            'user_id'  => $insertUserId,  // 用户ID
                            'relation_id'=>$insertResId  // 学生数据表ID
                        ]);  // 入关系表
                        yield $insertUserId => $val;
                        // 导入成功报告
                        $number = $val['number'];
                        $username = $val['username'];
                        $content ="成功导入ID:{$insertUserId} || 学号:{$number} || 姓名:{$username}";
                        file_put_contents($file_success,$content.PHP_EOL, FILE_APPEND );
                    } catch (\Illuminate\Database\QueryException $e){  // 抛出异常
                       // $repeatData[] =  $val;
                        $content = $e->getMessage();
                        $number = $val['number'];
                        $username = $val['username'];
                        $content ="导入失败学号:{$number} || 姓名:{$username}";
                        file_put_contents($file_failure,$content.PHP_EOL, FILE_APPEND );
                    }
                }*/
                //$out =  ['success'=>$file_success,'failure'=>$file_failure,'message'=>$content];
          });
          return $repeatData;
        }
        return null;  //不传参数
    }
    
    
    // 删除学生信息
    public function delStudents($id){
        
        return $this->where('id',$id)->delete();
        
    }
    
    
}
