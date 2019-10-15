<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;
use LiveControl\EloquentDataTable\DataTable;

class SemesterModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'semesters'; // 学期表


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
            ['id', 'name','status','sort', 'create_time']
        );
        return $dataTable;
        
    }
    
    /**
     *获取全部信息 (排序列表)
     */
    public function getAllsData(){
        return $this->select('id','name')->orderBy('sort', 'DESC')->get()->toArray();
    }

     
    /**
     * 自动填充使用ID
     */
    public function autoNumber($name='id'){
        return $this->max($name)+1;
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
     * 查询导师信息
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
    
    // 删除导师信息
    public function delOne($id){
        $del = $this->getConnection()->transaction(function () use($id){
            return $this->where('id',$id)->delete();
        });
        return $del;
    }
    
    
    //降序
    /**
     * @param int $id
     * @param string $name 自减字段
     * @param int $loop 自减字段
     */
    public function descData(int $id ,string $name,int $loop=1){
        return $this->where('id',$id)->decrement($name,$loop);
    }
    
    
    //升序
    public function ascData(int $id ,string $name,int $loop=1){
        return $this->where('id',$id)->increment($name,$loop);
    }
}
