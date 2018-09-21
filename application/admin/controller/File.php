<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;


class File extends Base
{
    // 文件列表
    public function showlist()
    {
        $res = Db::table('file')
                ->join('course','id = file_id','LEFT')
                ->field('id,file_purpose,file_url,file_size,upload_time,course_id')
                ->order('upload_time desc')
                ->paginate(10);
        return view('showlist',['data' => $res]);
    }
    
    // 文件删除
    public function delete()
    {
        $filename = Db::table('file')->where('id',input('get.file_id'))->field('file_url')->find(); 
        Db::table('file')->where('id',input('get.file_id'))->delete();
        $res = unlink(ROOT_PATH . "public". DS . $filename['file_url']);
        if($res)
            $this->success('删除文件成功！','File/showlist');
        else 
            $this->error('删除文件失败，请重试！','File/showlist');
    }
    
    // 文件下载
    public function download()
    {
        $fileinfo = Db::table('file')->where('id',input('get.file_id'))->field('file_url')->find();
        $filename = ROOT_PATH . "public". DS . $fileinfo['file_url'];
       
        if(!isset($filename)||trim($filename)==''){
            echo '500';
        }
        if(!file_exists($filename)){ //检查文件是否存在
            echo '404';
        }
        $file_name=basename($filename);
        $file_type=explode('.',$filename);
        $file_type=$file_type[count($file_type)-1];
        $file_name=date("Y-m-d H:i:s").'.'.$file_type;
        
        $file_type=fopen($filename,'r'); //打开文件
        //输入文件标签
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".filesize($filename));
        header("Content-Disposition: attachment; filename=".$file_name);
        //输出文件内容
        echo fread($file_type,filesize($filename));
        fclose($file_type);
    }
}
