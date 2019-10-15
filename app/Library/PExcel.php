<?php

/**
 * 2018年1月5日11:33:28
 * Angela
 * 表格处理
 */

namespace App\Library;

use PHPExcel;
use App\Service\TutorService;

class PExcel extends PHPExcel {

    private $filename;      //[文件名称（追加当前时间）]
    private $mimetype;     // [文件后类型]
    private $suffix;        //[文件后缀名（默认xls）]
    private $data;          //[数据（二维数组）]
    private $title;         //[标题（二维数组）]
    private $jump_url;      //[出错跳转url地址（默认上一个页面）]
    private $msg;           //[错误提示信息]

    /*     * *
     * 导出数据
     */

    public function Export($param = [],$makedir= null) {

        $this->mimetype = 'vnd.ms-excel';
        $this->title = isset($param['title']) ? $param['title'] : [];
        $this->alias = isset($param['alias']) ? $param['alias'] : [];
        $this->data = isset($param['data']) ? $param['data'] : [];
        $this->filename = isset($param['filename']) ? $param['filename'] : date('YmdH');
        $this->suffix = isset($param['suffix']) ? trim($param['suffix']) : 'xls';

        if (is_array($param) && empty($this->title) && empty($this->data)) {
            // 错误提示页面
        }
        // 操作第一个工作表
        $this->setActiveSheetIndex(0);
        // 文件输出类型
        switch ($this->suffix) {

            case 'pdf':
                $writer = \PHPExcel_IOFactory::createWriter($this, 'PDF');
                $this->mimetype = 'pdf';  // $this->suffix
                $writer->setSheetIndex(0);
                break;
            case 'xlsx':
                $writer = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
                break;
            // 默认EXCEL
            default:
                $writer = \PHPExcel_IOFactory::createWriter($this, 'Excel5');
        }

        // 获取配置编码类型

        $charset = 'gb2312';
        $excel_charset = 0;
        // 标题
        foreach ($this->title as $v) {
            if($this->alias){
                $header = $this->alias[$v['name']];
            }else{
                $header = $v['name'];
            }
            $this->getActiveSheet()->setCellValue($v['col'] . '1', ($excel_charset == 0) ? $header : iconv('utf-8', $charset, $header));
            
        }
       //p($this->title);
        // 内容
        foreach ($this->data as $k => $v) {
            $i = $k + 2;
            if (is_array($v) && !empty($v)) {
                foreach ($this->title as $tk => $tv) {
                    //$this->getActiveSheet()->setCellValue($tv['col'] . $i, $v[$tk]);
                    $this->getActiveSheet()->setCellValueExplicit($tv['col'] . $i, ($excel_charset == 0) ? $v[$tk] : iconv('utf-8', $charset, $v[$tk]), \PHPExcel_Cell_DataType::TYPE_STRING);
                }
            }
        }
       
      
        // 生成文件保存服务器
        if(!is_null($makedir)){
            $makeFile = $makedir.$this->filename . '.' . $this->suffix;
            
            $writer->save( $makeFile );
            
            return $makeFile;  // 生成文件路径
        }
        
        // 头部
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control:must-revalidate, post-check=0, pre-check=0');
        header('Content-Type:application/force-download');
        header('Content-Type: application/' . $this->mimetype . ';;charset=' . $charset);
        header('Content-Type:application/octet-stream');
        header('Content-Type:application/download');
        header('Content-Disposition:attachment;filename=' . $this->filename . '.' . $this->suffix);
        header('Content-Transfer-Encoding:binary');
        $writer->save('php://output');
    }

    /**
     * [Import excel文件导入]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-04-06T18:18:55+0800
     * @param    [string]    $file [文件位置,空则读取全局excel的临时文件]
     * @return   [array]           [数据列表]
     */
    public function Import($param = []) {
        
        if(is_null($param)){
            throw new \Exception('Not found param');
        }
        $file = isset($param['file']) ? $param['file'] : NULL;
        if(!file_exists($file)){
            throw new \Exception('Not found File');
        }
        
        $this->title = isset($param['title']) ? $param['title'] : [];
        $this->alias = isset($param['alias']) ? $param['alias'] : [];
        // 获取文件后缀名
        $info = pathinfo($file);
        
        $excelExt =  $info['extension'] == 'xlsx' ? 'Excel2007' : 'Excel5'; 
        
        // 取得文件基础数据
        $reader = \PHPExcel_IOFactory::createReader( $excelExt );
        $excel = $reader->load($file);

        
        $worksheet = $excel->getActiveSheet();

        // 取得总行数
        $highest_row = $worksheet->getHighestRow();

        // 取得最高的列 取得总列数 
        $highest_column = $worksheet->getHighestColumn();

        // 总列数
        $highest_column_index = \PHPExcel_Cell::columnIndexFromString($highest_column);

        // 定义变量
        $result = array();
        $field = array();
        $tutors = TutorService::cacheTutors();
        $tutors_reverse = array_flip($tutors); // 导师
        $genders = array_column(__STUDENT_GENDER__, 'id','name'); // 性别
        $status = array_column(__STUDENT_STATUS__, 'id','name'); // 状态
        // 读取数据 
        for ($row = 1; $row <= $highest_row; $row++) {
            // 临时数据
            $info = array();

            // 注意 highest_column_index 的列数索引从0开始
            for ($col = 0; $col < $highest_column_index; $col++) {
                $value = trim($worksheet->getCellByColumnAndRow($col, $row)->getFormattedValue());
                //if(strlen($value) > 0){  // 值
                    if ($row == 1){
                        foreach ($this->title as $tk => $tv) {
                            if ($value == $tv['name']) {
                                $tk = $this->alias[$value];  // 替换
                                $tv['field'] = $tk;
                                $field[$col] = $tv;
                            }
                        }
                    } else {
                        if (!empty($field)) {
                            // 处理列表数组
                            if($col == 2){
                                $value = isset($genders[$value]) ? $genders[$value] : 3;// 性别
                            }else if($col == 4){
                                $value = isset($tutors_reverse[$value]) ? $tutors_reverse[$value] : '';// 导师
                            }else if($col == 8){
                                $value = isset($status[$value]) ? $status[$value] : '';// 学生状态
                            }
                            $info[$field[$col]['field']] = ($field[$col]['type'] == 'int') ? trim(ScienceNumToString($value)) : trim($value);
                        }
                    }
                //}
            }
            if ($row > 1 && !empty($info)) {
                $result[] = $info;
            }
        }
        return $result;
    }

}
