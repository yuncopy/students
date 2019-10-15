<?php

/**
 * 首页控制
 * 2017年11月21日17:54:32
 */
namespace App\Controller;
use App\Model\ContentsModel;
use App\Model\UsersModel;
use App\Model\AuthorityModel;
use App\Service\ContentbService;
use App\Service\LoginService;

class FileController extends BaseController{
    
    /**
     * 2017年12月4日11:17:57
     * Angela
     * 后台管理列表
     */
    public function index()
    {
        $this->display('index.html'); 
    }
    
    
    /**
     * 2017年12月4日12:10:50
     * Angela
     * 分类管理
     */
    public function cate(){
        $cate = Config('cate');
        $this->display('cate.html',['cate'=>$cate]); 
    }
     /**2017年12月4日16:00:37
     * Angela
     * 添加分类/编辑界面
     */
    public function addcate($id){
        $need = null;
        if($id){
            $cate = Config('cate');
            $needs = array_filter($cate,function($q) use($id){
                if( $q['id'] == $id ){ return $q;}
            });
            foreach ($needs as $val){$need =  $val;}
        }
        $this->display('addcate.html',['need'=>$need]);
    }
    /**
     * 2017年12月4日17:32:47
     * Angela
     * 执行分类编辑和添加和删除
     */
    public function docate($cate_data = null){
        $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
        $name = $this->getPostDatas('POST','name',false,'htmlspecialchars');
        $cate = $cate_data == null ? Config('cate') : $cate_data;
        $cate_str = "<?php \n return [".PHP_EOL;
        $addstr = '';
        if(!$id && !$cate_data){ // 添加操作
            $keys = array_keys($cate);  
            $addId = max($keys)+2; //先获取所有key，再获取最大的key
            $addstr = "\t['id' =>'{$addId}','name'  =>'{$name}']".PHP_EOL;
        }
        // 处理数据
        foreach ($cate as $value){
            if($value['id'] == $id){  // 编辑操作
                $cate_str .= "\t['id' =>'{$id}','name'  =>'{$name}'],".PHP_EOL;
            }else{
                $cate_str .= "\t['id' =>'{$value['id']}','name'  =>'{$value['name']}'],".PHP_EOL;
            }
        }
        $write_str = trim( $cate_str.$addstr ,',')."]; \n ?>".PHP_EOL;
        $file_name = __CONFIG__.'cate.php';
        //print_r($write_str);exit;
        $res = file_put_contents($file_name, $write_str);
        if($res){
           return  redirect('/file/cate.html'); //更新成功
        }
    }

    /**
     * 2017年12月6日15:48:13
     * Angela
     * 删除分类
     */
    public function delcate($id){
        $cate = Config('cate');
        if($cate){
            $callback = function($value) use($id){
                if($value['id'] == $id){
                    unset($value);
                }else{
                    return $value;
                }
            };
           $delArray = array_filter($cate, $callback);
           return $this->docate($delArray);
        }
    }

    /**
     * 2017年12月7日11:34:00
     * Angela
     * 图片管理
     * 
     */
    public function image(){
        $this->display('image.html');
    }
    
    
    /**
     * 2017年12月9日18:41:18
     * Angela
     * 
     */
    public function subdata(){
        dd('121');
    }

        /**
     * 2017年12月9日12:07:21
     * Angela 
     * 删除操作
     */
    public function delgame($id){
        if($id){
           $delRes = ContentbService::delOneContent($id); 
        }
        return  redirect('/file/list.html'); //删除成功
    }

      /**
     * 2017年12月8日19:33:25
     * Angela
     * 编辑游戏
     */
    public function editgame($id){
        if($id){   // 显示编辑页面
           $detail =  ContentbService::getDetail($id);
           $detail['texts'] = htmlspecialchars_decode($detail['texts']);
           $cate = Config('cate');
           $url = self::getBackendsUrl();
        }
        $this->display('add.html', compact('detail','cate','url'));
    }
    
    /**
     * 2017年12月9日11:36:49
     * Angela
     * 获取后台图片配置地址
     */
    public static  function  getBackendsUrl(){
        $config = include (__CKFINDER__.'config.php');
        $backends = $config['backends'][0]['baseUrl'];
        return trim($backends);
    }

        /**
     * 2017年12月7日20:38:37
     * Angela
     * 添加 ， 编辑
     */
    
    public function addgame(){
        $post = $this->getPostDatas('POST');  // 获取数据
        $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
        ContentbService::filter_input_value($post);  // 验证数据
        if($post){  //添加/编辑操作
            // 处理数据
            $insert_data = [];
            $backends = self::getBackendsUrl();
            foreach ($post as $key => $value){
                $val = substr($value, strlen($backends)); 
                if($val){
                    $insert_data[$key] = htmlspecialchars(str_replace($backends,"",$value));  // 替换
                }else{
                    $insert_data[$key] = htmlspecialchars($value);
                }
            }
            $insert_data['click'] = rand(200, 999);
            $user = LoginService::getUserCookie();
            $insert_data['uid'] = $user['uid'];
            $where = [];
            if($id){
                $where['id'] = $id;  // 编辑操作
            }else{
                unset($insert_data['id']);  // 添加时去掉
            }
            //print_r($insert_data);exit;
            try {
                if(ContentbService::insertData($insert_data,$where)){
                    $message = ['status'=>200,'message'=>'操作成功'];
                }else{
                    throw new \Exception('400');
                }
            } catch (\Exception $exc) {
                //$message = $exc->getMessage();
                $message = ['status'=>200,'message'=>'操作成功'];
            }
            // 添加数据入库
            die(json_encode($message));
        }
    }

    /**
     * 2017年12月4日15:27:54
     * Angela
     * 游戏列表
     */
    public function lists(){

        $this->display('list.html'); 
    }
    // 获取数据
    public function  getajaxlist(){
        $dataTable =  (new ContentsModel)->getList();
        $dataTable->setFormatRowFunction(function ($row) {
            return [
                $row->id,
                $row->title,
                $row->version,
                $row->click,
                $row->create_time,
                '<div class="btn-group">
                    <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a  target="_Blank" href="/file/editgame/'.$row->id.'.html"><i class="icon-pencil"></i> Edit</a></li>
                        <li><a  href="javascript:void(0);" data="/file/delgame/'.$row->id.'.html" class="delcate" data-toggle="modal" data-target="#delModal"  data="/file/delgame/'.$row->id.'.html"><i class="icon-trash"></i> Delete</a></li>
                        <li><a  target="_Blank" href="/detail/'.$row->id.'.html" ><i class="icon-eye-open"></i> Details</a></li>
                    </ul>
                </div>'
            ];
          });
        echo json_encode($dataTable->make());
    }

        /**
     * 2017年12月4日15:38:46
     * Angela
     * 添加数据
     */
    public function add(){
        $cate = Config('cate');
        $this->display('add.html',['cate'=>$cate]); 
    }
    
    
   
   /**
    * 2017年12月9日12:27:10
    * Angela 
    * 系统配置
    */
   public function system(){
       
       
       dd('TODO');
   }
   
   /**
    * 2017年12月9日12:28:30
    * Angela
    * 系统用户
    */
   public function sysuser(){
       $post = $this->getPostDatas('POST');
       if($post){
           echo $this->getsysuser();
       }else{
            $this->display('sysuser.html'); 
       }
      
   }
   // 获取数据
   public function  getsysuser(){
        $dataTable = (new UsersModel())->getUserList();
        $dataTable->setFormatRowFunction(function ($row) {
            return [
                $row->id,
                $row->name,
                array_sum(explode(',', $row->auth_id)),
                $row->create_time,
                '<div class="btn-group">
                    <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a  href="/file/edituser/'.$row->id.'.html"><i class="icon-pencil"></i> Edit</a></li>
                        <li><a  href="javascript:void(0);" data="/file/deluser/'.$row->id.'.html" class="delcate" data-toggle="modal" data-target="#delModal"  data="/file/delgame/'.$row->id.'.html"><i class="icon-trash"></i> Delete</a></li>
                    </ul>
                </div>'
            ];
        });
        return json_encode($dataTable->make());
   }
   
   /**
    * 2017年12月9日19:23:41
    * Angela
    * 添加用户
    */
   public function addsysuser($id=null){
       $post = $this->getPostDatas("POST");
       $post_id = $this->getPostDatas('POST','id',false,'trim');
       $name = $this->getPostDatas('POST','name',false,'trim');
       $pass = $this->getPostDatas('POST','pass',false,'trim');
       if($post && $name){
           $data=[
                'name'      =>  $name,
                'auth_id'   =>  !empty($post['auth_id']) ? implode(',',$post['auth_id']) : null
            ];
            if($pass){ $data['pass'] = password_hash($pass,PASSWORD_DEFAULT);}  // 设置密码
            $where = [];
            if($post_id) $where = ['id'=>$post_id];
            $rs = (new UsersModel)->insertOrUpdate($data,$where);
            return  redirect('/file/sysuser.html'); //添加成功
       }else{
            //获取用户
            $user = $auth = [];
            if($id){ $user = (new UsersModel)->getOne($id);}  
            $this->display('addsysuser.html', compact('user')); 
       }
    }

      /**
    *  2017年12月11日10:54:51
    * Angela
    * 删除用户
    */
   
   public function deluser($id){
        if($id){
           $delRes = (new UsersModel)->delOneUser($id); 
        }
       if($delRes)  return  redirect('/file/sysuser.html'); //删除成功
   }



   /**
    * 2017年12月9日16:43:26
    * Angela
    * 权限管理
    */
   public function authority(){
    
       $this->display('authority.html'); 
   }
   
   /**
    * 2017年12月9日16:48:35
    * Angela
    * 获取数据
    */
   public function  getauthority(){
        $dataTable =  (new AuthorityModel)->getList();
        $dataTable->setFormatRowFunction(function ($row) {
            return [
                'id'            =>$row->id,
                'name'          =>$row->name,
                'abits'         =>$row->abits,
                'create_time'   =>$row->create_time,
                'action'        =>'<div class="btn-group">
                    <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a  href="/file/editauthority/'.$row->id.'.html"><i class="icon-pencil"></i> Edit</a></li>
                        <li><a  href="javascript:void(0);" data="/file/delgame/'.$row->id.'.html" class="delcate" data-toggle="modal" data-target="#delModal"  data="/file/delgame/'.$row->id.'.html"><i class="icon-trash"></i> Delete</a></li>
                    </ul>
                </div>',
                'pid'           => $row->pid,
                'route'         => !empty($row->route) ? "[{$row->method}]&nbsp;&nbsp;{$row->route}" : ''
            ];
          });
         // 处理输出界面处理
        $out_data = $dataTable->make();
        $data = $out_data['data'];
        $data_tree = make_tree_with_namepre($data);
        $data_tree_to_list = make_tree_to_list($data_tree);
        $out_data['data'] = $data_tree_to_list;
        echo json_encode($out_data);
   }
   
   /**
    * 2017年12月9日17:01:15
    * Angela
    * 添加权限
    */
   public function  addauthority($id=null){
       $post  = $this->getPostDatas('POST');
       $post_id = $this->getPostDatas('POST','id',false,'trim'); // 编辑
       $method = $this->getPostDatas('POST','method',null,'trim');
       $route = $this->getPostDatas('POST','route',null,'trim');
       if($post && $method && $route){
           $bits = !empty($post['abits']) ? (int)$post['abits'] : 0;
           $inster_data=[
               'name'   => $post['name'],
               'pid'    => $post['pid'],
               'active' => $post['active'],
               'abits'  => (string)pow ( 2 ,$bits ),  // 二进制处理
               'method'    =>  $method,
               'route'     =>  $route
           ];
           //dd($inster_data);
            $where = [];
            if($post_id){  // 编辑操作
                $where = ['id'=>$post_id];
                unset($inster_data['abits']);  // 去掉二进制值    
            }
            $idRes = (new AuthorityModel)->insertOrUpdate($inster_data,$where);
            if($idRes) return  redirect('/file/authority.html'); //删除成功
       }else{
            $Auth = new AuthorityModel;
            $btis = $Auth->counts();
            $auths = $Auth->getAuthoritys();
            $auths_option = make_option_tree_for_select($auths);
            if($id){ $authone = (new AuthorityModel)->getOne($id);}  
            $this->display('addauthority.html', compact('btis','auths_option','authone')); 
       }
   }

}
