<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;
use LiveControl\EloquentDataTable\DataTable;
class ContentsModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'contents';
    protected  $page_size = 16;


    /**
     * 2017年11月25日15:51:20
     * Angela
     * 获取分页数据
     */
    public function index($page,$sort = 'create_time'){
       $page_size = $this->page_size;
       $results = $this->orderBy($sort, 'desc')->simplePaginate($page_size,['*'],'page',$page);
       return $results->toArray();
    }
    
    /**
     * 2017年11月25日17:05:25
     * Angela
     * 热门 TOP视频
     */
    public function topVideo($cate_id=null){
        if($cate_id){
            return $this->where("cate_id",$cate_id)->orderBy('click', 'DESC')->take(10)->get()->toArray();
        }
        return $this->orderBy('click', 'DESC')->take(10)->get()->toArray();
    }
    
    /**
     * 2017年11月25日17:05:35
     * Angela
     * 获取单条记录
     */
    
    public function getOne($id){
        return $this->where('id', $id)->first()->toArray();
    }
    
    /**
     * 2017年12月9日12:11:51
     * Angela
     * 删除单条数据
     */
    public function delOne($id){
        return $this->where('id', $id)->delete();
    }


    /**
     * 获取分类信息
     * Angela
     * 2017年11月27日10:11:39
     */
    public function getCate($cate_id,$page=1) {
        $page_size = $this->page_size;
        $results = $this->where('cate_id',$cate_id)->orderBy('create_time', 'desc')->simplePaginate($page_size,['*'],'page',$page);
        return $results->toArray();
    }
    
    /**
     * 排序处理
     * Angela
     * 2017年11月27日16:19:28
     */
    
    public function orderContent($order = 'create_time',$page=1){
        $page_size = $this->page_size;
        $results = $this->orderBy( $order, 'desc' )->simplePaginate($page_size,['*'],'page',$page);
        return $results->toArray();
    }
    
    
    /**
     * 递增单击数据
     * Angela
     * 2017年11月28日17:34:16
     */
    
    public function incrClick($id){
        return $this->where('id',$id)->increment('click');
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
     * 2017年12月8日17:38:25
     * Angela
     * 获取内容列表
     */
    public function getList(){
        // 手册 https://packagist.org/packages/livecontrol/eloquent-datatable
        $dataTable = new DataTable(
            $this,
            ['id', 'title', 'version','click','create_time','id']
        );
        return $dataTable;
    }
}
