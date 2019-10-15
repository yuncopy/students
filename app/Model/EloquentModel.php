<?php

/**
 * 2017年11月21日14:58:47
 * Angela 
 * 功能： 数据基类
 */
// https://laravel.com/api/5.1/Illuminate/Database.html
namespace App\Model;
use \Illuminate\Database\Eloquent\Builder;  //  第二种权限过滤方法
use \Illuminate\Database\Eloquent\Model as Model; //基础Model  手册
use App\Service\LoginService;

class EloquentModel extends Model{
    
    
    protected $builder;  // 第二种存放实例化对象 , 第三种方法可是返回对象全局使用，不过这样比较麻烦
    
    
   /**
     * 权限过滤模式是否开启
     */
    protected $switchAuth = false;
    

    
    
    
    
    // 第一方法   以下三种过滤数据时尽量要保证数据字段的统一性,方便操作
    /**Angela 2018年1月19日13:54:15
     * 增加调用前置操作,重写了底层方法
     * @return type
     
    public function newQuery() {
        $builder = $this->newQueryWithoutScopes();
        foreach ($this->getGlobalScopes() as $identifier => $scope) {
            $builder->withGlobalScope($identifier, $scope);
        }
        $this->authAnalysis($builder);  // 设置权限数据
        return $builder;
    }
    */
 
    
    /**
     * 分析权限数据
     
    private function authAnalysis($builder){
        if($this->switchAuth){
            //获取权限数据
            $jsonData = Session::get(__AUTH__);  // 获取权限数据  
            $authData = json_decode($jsonData, true);  // ['student_id'=>[1,2,3,3]]
            if($authData){
                $builder->where(function($query) use ($authData){
                    foreach ($authData as $field => $value){
                        $query->whereIn($field, $value);
                    }
                });
            }
        }else{
            return $builder;
        }
    }
    */


    /**
     * 第二种权限过滤方法
     * 过滤条件
     * @param Builder|Model $builder
     * @return $this
     */
    private function setWhereFilter($builder)
    {
        if ( ! ($builder instanceof Builder || $builder instanceof Model)) {
            throw new Exception('$builder variable is not an instance of Builder or Model.');
        }
        
        $key_auth = __AUTH__;
        $authData = LoginService::setGet($key_auth);  // 获取权限数据 
       //p($authData); 
        //p($authData,false);
        //$authData = ['student_id'=>[1,2,3,3],'teacher_id'=>[1,3]];
        //p($authData);
        
        if($this->switchAuth && $authData){
            
            $tableName = $this->getTable(); // 优化查询次数
            $keyColumns = $key_auth.'_columns_'.$tableName;  //表字段
            $fieldData = LoginService::setGet($keyColumns,function() use($tableName,$keyColumns){
                $fieldData = getTableColumns($tableName);
                return LoginService::setGet($keyColumns,$fieldData);
            });
            
            foreach ($authData as $field => $value) {
                if (in_array($field, $fieldData)) {
                    $this->builder = $builder->where(
                        function ($query) use ($field,$value) {
                           $query->whereIn($field, $value);
                        }
                    );
                }
            }
        }
        return !empty($this->builder) ? $this->builder : $builder;
    }
     
    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters){
        
        $builder = $this->newQueryWithoutScopes();  //获取一个没有全局范围的新查询生成器。
        $query = $this->setWhereFilter($builder);
        return call_user_func_array([$query, $method], $parameters);
        
    }

































    /*
     * 构建查询条件
     * 
     * 执行思路：
     * laravel框架model类查询实现
     * User::where(['id'=8])->get();
     * User类继承自Model类：Illuminate\Database\Eloquent\Model
     * 
     * 执行思路：
     * 
     * 1、当User类静态调用where方法时，自动调用了Model里的魔术方法 __callStatic
     * 
     * 知识点：
     * 在对象中调用一个不可访问方法时，__call() 会被调用。 
     * 用静态方式中调用一个不可访问方法时，__callStatic() 会被调用。 
     * public mixed  __call  ( string $name  , array $arguments  )
     * public static mixed  __callStatic  ( string $name  , array $arguments  )
     * $name 参数是要调用的方法名称。 $arguments  参数是一个枚举数组，包含着要传递给方法 $name  的参数。 
     * 
     * 2、接着会调用
     *    public static function __callStatic($method, $parameters)
     *   {
     *      return (new static)->$method(...$parameters);  //new static 就是User类的实例对象
     *      
     *      //相当于：return call_user_func_array([$instance, $method], $parameters);
     *   }
     * 
     * 知识点：
     * 
     * 1）PHP中new self()和new static()的区别探究 容易理解的文章 ：https://www.cnblogs.com/shizqiang/p/6277091.html
     * 首先，他们的区别只有在继承中才能体现出来，如果没有任何继承，那么这两者是没有区别的。
     * 然后，new self()返回的实例是万年不变的，无论谁去调用，都返回同一个类的实例，而new static()则是由继承调用者决定的。
     * 
     * 官方解释 ： http://php.net/manual/zh/language.oop5.late-static-bindings.php
     * 
     * 2）使用 ... 运算符进行参数展开 
     * ...$parameters
     * 官方解释 ：http://php.net/manual/zh/migration56.new-features.php
     * 
     * 
     * 2、接着发现自身类（new User 类）中没有发现 where 方法，会调用
     *   public function __call($method, $parameters)
     *  {
     *      if (in_array($method, ['increment', 'decrement'])) {
     *          return $this->$method(...$parameters);
     *      }
     *      return $this->newQuery()->$method(...$parameters);   //返回Illuminate\Database\Eloquent\Builder对象
     *      
     *      //相当于 以下两句
     *      //$query = $this->newQuery(); //返回Illuminate\Database\Eloquent\Builder对象
     *      //return call_user_func_array([$query, $method], $parameters);
     *  }
     * 
     * 3、 会调用 $this->newQuery()方法  返回 Illuminate\Database\Eloquent\Builder 对象
     *   public function newQuery()
        {
            $builder = $this->newQueryWithoutScopes();

            foreach ($this->getGlobalScopes() as $identifier => $scope) {
                $builder->withGlobalScope($identifier, $scope);
            }

            return $builder;
        }
     * 
     * 执行 $builder = $this->newQueryWithoutScopes();
     * 构建基础查询
     * $builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());
     *  
     *  protected function newBaseQueryBuilder()
       {
            $conn = $this->getConnection(); \\连接数据库并返回connection对象

            $grammar = $conn->getQueryGrammar();

            return new QueryBuilder($conn, $grammar, $conn->getPostProcessor()); //Illuminate\Database\Query\Builder
        }
     * 
     * 4、选择连接池
     * Model类的$resolver属性(连接解析器)的设定是通过
       Illuminate\Database\DatabaseServiceProvider 里的boot方法设置的
        Model类的getConnection方法实际调用的DatabaseManager类的connection方法，返回connection类实例
     * 
     * 5、执行查询操作
     *  相当于调用Illuminate\Database\Eloquent\Builder对象里的where方法和get方法，这两个方法里其实
        其实是封装调用了Illuminate\Database\Query\Builder对象里的where方法和get方法->get方法里调用了runselect方法
     */
  
}

