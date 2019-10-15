<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\MenusModel;
use App\Service\PermissionService;
use App\Service\RoleService;

class MenuService extends CommonService{
    
    
   
    
    /**
     * 获取数据
     */
    public static function getList($where){
        
        $Menus = new MenusModel();
        $dataTable = $Menus->getList($where);
        $dataTable->setFormatRowFunction(function ($row){
            $permission_ids = explode(',', $row['permission_id']);
            $permission = PermissionService::getDatas($permission_ids);
            $permission_name = array_column($permission, 'name' ,'id');
            $permission_name_str = implode(",", $permission_name);
            return [
                'id'            =>  $row['id'],
                'pid'           =>  $row['pid'],
                'name'          =>  $row['name'],
                'icon'          =>  $row['icon'],
                'permission_name'=> $permission_name_str,
                'permission_id' =>  $row['permission_id'],
                'create_time'   =>  $row['create_time'],
                'status'        =>  $row['status'],
                'action'        =>  $row['id']
            ];
        });
       return $dataTable->make();
    }
    
    
    // 获取当前最大ID
    public static function getMaxId(){
        return (new MenusModel)->max('id');
    }
    
    //获取数据
    public static function getDatas(){
        $permission_str = RoleService::getCurrentRolePermission();
        $permission_array  = explode(',', $permission_str);
        $showMenu = (new MenusModel)->getDatas($permission_array);
        $authPermission = PermissionService::getDatas($permission_array);
        $route = array_column($authPermission, 'route' ,'id');
        return [
             'M'=>$showMenu,
             'P'=>$route
        ];
    }
    
    // 左侧菜单
    public static function leftMenu(){
        
        // 不使用递归算法
        $gentree = function($test){  
            $tree = array(); //格式化好的树  
            $tmp = array();  //临时扁平数据  
            foreach ($test as $vo){  
              $tmp[$vo['id']] = $vo;  
            }  
            foreach ($test as $vo){  
              if (isset($tmp[$vo['pid']])){  
                $tmp[$vo['pid']]['children'][] = &$tmp[$vo['id']];  
              }else{  
                $tree[] = &$tmp[$vo['id']];  
              }  
            }  
            unset($tmp);  
            return $tree;  
        }; 
       
        $data = self::getDatas();
        $datas = $gentree($data['M']);
        $routeArray = $data['P'];
        //p($routeArray);
        function make_tree_nav($tree,$route){
            $html = '';
            foreach($tree as $key => $v){
                if( $key%3 == 0 ){
                    $htmlline = '<li class="line dk"></li>';
                    $htmlline .= '<li class="hidden-folded padder m-t m-b-sm text-muted text-xs">';
                        $htmlline .= '<span class="ng-scope">分类</span>';
                    $htmlline .= '</li>';
                    if(!empty($v['children'])){
                        $html .= $htmlline;
                    }
                }
                if(empty($v['children'])){
                    $html .= '<li>';
                        $html .= '<a class="J_menuItem" href="'.siteURL($route[$v['permission_id']]).'">';
                            $html .= '<i class="'.$v['icon'].'"></i>';
                            $html .= '<span class="nav-label">'.$v['name'].'</span>';
                        $html .= '</a>';
                    $html .= '</li>';
                }else{
                    $html .= '<li>';
                        $html .= '<a href="javascript:void(0)">';
                            $html .= '<i class="'.$v['icon'].'"></i>';
                            $html .= '<span class="nav-label">'.$v['name'].'</span>';
                            $html .= '<span class="fa arrow"></span>';
                        $html .= '</a>';
                        $html .= '<ul class="nav nav-second-level">';
                           $html .= make_tree_nav($v['children'],$route);
                        $html .= '</ul>';
                    $html .= '</li>';
                }
            }
            return $html;
        }
        return make_tree_nav($datas,$routeArray);
    }

    



    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new MenusModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new MenusModel())->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function DelOne($id){
        if($id){
            return (new MenusModel())->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new MenusModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new MenusModel())->getUniqueData($id);
    }
    
    
   
    
    
}