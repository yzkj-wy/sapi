<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------

namespace Common\Tool\Extend;


use Common\Tool\Tool;

/**
 * curl 操作
 * @author Administrator
 * @version 1.0.1
 */
class CURL
{
    /**
     * 文件数组
     * @var array
     */
    private $file = [];
    
    /**
     * url
     * @var string
     */
    private $url = '';
    
    /**
     * @param array $file
     * @param string $url
     */
    public function __construct(array $file, $url)
    {
        $this->file = $file;
        
        $this->url  = $url;
        
    }
    
    /**
     * @param array  $file 文件信息
     * @param string $url  上传的URL
     */
    public function uploadFile()
    {
        $file = $this->file;
        
        $url = $this->url;
        
        if (empty($file) || empty($url))
        {
            throw new \Exception('文件错误');
        }
        //php 5.5以上的用法
        if (class_exists('\CURLFile')) {
            $data = [
                'file' => new \CURLFile(realpath($file['tmp_name']),$file['type'],$file['name'])
            ];
        } else {
            $data =[ 
                'file'  =>'@'.realpath($file['tmp_name']).";type=".$file['type'].";filename=".$file['name']
            ];
        }
        
        $this->file = $data;
        
        $returnData = $this->curlConfig();
        return $returnData;
    }
    
    /**
     * 生成缩略图
     */
    public function sendImageToCreateThumb()
    {
        return $this->curlConfig();
    }
    
    
    private function curlConfig()
    {
        $url = $this->url;
        
        $data = $this->file;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $returnData = curl_exec($ch);
        curl_close($ch);
        return $returnData;
    }
    
    /**
     * 删除文件 
     */
    public function deleteFile()
    {
        return $this->curlConfig();
    }
    
    /**
     * 异步执行
     */
    public function asynchronousExecution()
    {
        $urlinfo = parse_url($this->url);
        
        $host = $urlinfo['host'];
        $path = $urlinfo['path'];
        $query = http_build_query($this->file);
        
        $port = 80;
        $errno = 0;
        $errstr = '';
        $timeout = 10;
        
        $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        
        $out = "POST ".$path." HTTP/1.1\r\n";
        $out .= "host:".$host."\r\n";
        $out .= "content-length:".strlen($query)."\r\n";
        $out .= "content-type:application/x-www-form-urlencoded\r\n";
        $out .= "connection:close\r\n\r\n";
        $out .= $query;
        
        fputs($fp, $out);
        fclose($fp); 
    }
}