<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Library\Driver;

/**
 * Description of VaAction
 *
 * @author Administrator
 */
class ValidateAction {
    //put your code here
    
    
    //字段为空时验证失败
    public function isnull($field, $value, $params, $data)
    {
        if ( ! isset($data[$field]) || empty($data[$field])) {
            return false;
        }

        return true;
    }

    //验证字段是否存在
    public function required($field, $value, $params, $data)
    {
        if ( ! isset($data[$field])) {
            return false;
        }
        return true;
    }

    

    //存在字段时验证失败
    public function exists($field, $value, $params, $data)
    {
        return isset($data[$field]) ? false : true;
    }

 

    //邮箱验证
    public function email($name, $value, $params)
    {
        //$preg = "/^([a-zA-Z0-9_\-\.])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{1,3}){1,2})$/i";
        $preg = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //验证用户名长度
    public function user($field, $value, $params, $data)
    {
        $params = explode(',', $params);

        return preg_match(
            '/^[\x{4e00}-\x{9fa5}a-z0-9]{'.($params[0] - 1).','.($params[1] - 1)
            .'}$/ui',
            $value
        ) ? true : false;
    }

    //邮编验证
    public function zipCode($name, $value, $params)
    {
        $preg = "/^\d{6}$/i";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //最大长度验证
    public function maxlen($name, $value, $params)
    {
        $len = mb_strlen(trim($value), 'utf-8');
        if ($len <= $params) {
            return true;
        }
    }

    //最小长度验证
    public function minlen($name, $value, $params)
    {
        $len = mb_strlen(trim($value), 'utf-8');
        if ($len >= $params) {
            return true;
        }
    }

    //网址验证
    public function http($name, $value, $params)
    {
        $preg
            = "/^(http[s]?:)?(\/{2})?([a-z0-9]+\.)?[a-z0-9]+(\.(com|cn|cc|build|net|com.cn))$/i";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //固定电话
    public function tel($name, $value, $params)
    {
        $preg = "/(?:\(\d{3,4}\)|\d{3,4}-?)\d{8}/";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //手机号验证
    public function phone($name, $value, $params)
    {
        $preg = "/^\d{11}$/";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //身份证验证
    public function identity($name, $value, $params)
    {
        $preg = "/^(\d{15}|\d{18})$/";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //用户名验证
    public function range($name, $value, $params)
    {
        //用户名长度
        $len    = mb_strlen($value, 'utf-8');
        $params = explode(',', $params);
        if ($len >= $params[0] && $len <= $params[1]) {
            return true;
        }
    }

    //数字范围
    public function num($name, $value, $params)
    {
        $params = explode(',', $params);
        if (intval($value) >= $params[0] && intval($value) <= $params[1]) {
            return true;
        }
    }

    //正则验证
    public function regexp($name, $value, $preg)
    {
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //两个表单比对
    public function confirm($name, $value, $params, $data)
    {
        if ($value == $data[$params]) {
            return true;
        }
    }

    //中文验证
    public function china($name, $value, $params)
    {
        if (preg_match('/^[\x{4e00}-\x{9fa5}a-z0-9]+$/ui', $value)) {
            return true;
        }
    }
    
    
}
