<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 通用化API接口数据输出
 * @param int $status 业务状态码
 * @param string $message 信息提示
 * @param [] $data  数据
 * @param int $httpCode http状态码
 * @return array
 */
function show($status, $message, $data=[], $httpCode=200) {

    $data = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ];

    return json($data, $httpCode);
}

//数组递归
function arrayDiGui($arr, $pKey, $key, $pid=0){
    $tmp = [];
    foreach($arr as $v){
        if($v[$key] == $pid){
            $v['child'] = arrayDiGui($arr, $pKey, $key, $v[$pKey]);
            $tmp[] = $v;
        }
    }
    return $tmp;
}

/**
* 发起请求
* @param  strin $url  url地址
* @param  string|array $ret  请求体
* @param  string $file 上传的文件绝对地址
* @return [type]       [description]
*/
function http_request($url,$ret='',$file=''){
    if (!empty($file)) {  // 有文件上传
        # php5.5之前 '@'.$file;就可以进地文件上传
        # $ret['pic'] = '@'.$file;
        # php5.6之后用此方法
        $ret['media'] = new CURLFile($file);
    }
    // 初始化
    $ch = curl_init();
    // 相关设置
    # 设置请求的URL地址
    curl_setopt($ch,CURLOPT_URL,$url);
    # 请求头关闭
    curl_setopt($ch,CURLOPT_HEADER,0);
    # 请求的得到的结果不直接输出，而是以字符串结果返回  必写
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    # 设置请求的超时时间 单位秒
    curl_setopt($ch,CURLOPT_TIMEOUT,30);
    # 设置浏览器型号
    // curl_setopt($ch,CURLOPT_USERAGENT,'MSIE001');

    # 证书不检查
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

    # 设置为post请求
    if($ret){ # 如果 $ret不为假则是post提交
        # 开启post请求
        curl_setopt($ch,CURLOPT_POST,1);
        # post请求的数据 
        curl_setopt($ch,CURLOPT_POSTFIELDS,$ret);
    }
    // 发起请求
    $data = curl_exec($ch);
    // 有没有发生异常
    if(curl_errno($ch) > 0){
        // 把错误发送给客户端
        echo curl_error($ch);
        $data = '';
    }
    // 关闭请求
    curl_close($ch);
    return $data;
}

/**
 * excel表格导出
 * @param string $fileName 文件名称
 * @param array $headArr 表头名称
 * @param array $data 要导出的数据
 * @author static7  */
 
function excelExport($fileName = '', $headArr = [], $data = []) {
    $fileName .= "_" . date("Y_m_d", Request::instance()->time()) . ".xls";
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->getProperties();
    $key = ord("A"); // 设置表头
    foreach ($headArr as $v) {
        $colum = chr($key);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
        //下面注释的这行代码是让表头拥有筛选功能，根据需要取消注释即可
        //$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());
        $key += 1;
 
    }
    $column = 2;
    $objActSheet = $objPHPExcel->getActiveSheet();
    foreach ($data as $key => $rows) { // 行写入
        $span = ord("A");
        foreach ($rows as $keyName => $value) { // 列写入
            $objActSheet->setCellValue(chr($span) . $column, $value);
            $span++;
        }
        $column++;
    }
    $fileName = iconv("utf-8", "gb2312", $fileName); // 重命名表
    $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename='$fileName'");
    header('Cache-Control: max-age=0');
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output'); // 文件通过浏览器下载
    exit();
}