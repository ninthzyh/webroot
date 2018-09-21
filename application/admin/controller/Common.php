<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Common extends Controller
{
    
    // 图片上传接口
    public function img_upload()
    {
        // 检测是否有文件上传
        if($this->request->file('file'))
            $file = $this->request->file('file');
        else{
            $res['code']=1;
            $res['msg']='没有上传文件';
            return json($res);
        }
        
        // 验证图片格式是否正确
        $info = $file->validate(['size'=>10*1024*1024,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads' . DS .'image');
        if($info)
        {
            $res['code'] = 2;
            $res['msg'] = '上传成功!';
            $res['src'] =  UPLOADS_IMG_URL.str_replace(DS,'/',$info->getSaveName());
       
        }else
        {
            $res['code'] = 0;
            $res['msg'] = $file->getError();
        }
        return json($res);
    }
    
    // 音频上传接口
    public function video_upload()
    {
        // 检测是否有文件上传
        if($this->request->file('file'))
            $file = $this->request->file('file');
        else{
            $res['code']=1;
            $res['msg']='没有上传文件';
            return json($res);
        }
            
        // 验证是否为音频格式
        if(input('post.type')=='video')
            $info = $file->validate(['size'=>1024*1024*1024,'ext'=>'mp4,rmvb,avi,mkv,flv'])->move(ROOT_PATH . 'public' . DS . 'uploads' . DS .'video');
        else 
            $info = $file->validate(['size'=>1024*1024*1024,'ext'=>'mp3,wav,wma'])->move(ROOT_PATH . 'public' . DS . 'uploads' . DS .'audio');
       
        if($info)
        {   
            // 拼装数据
            $data['file_purpose'] = input('post.type');
            if(input('post.type')=='video')
            {
                $data['file_url'] =  "uploads". DS ."video". DS .$info->getSaveName();
                $res['src'] =  UPLOADS_VIDEO_URL.$info->getSaveName();
            }
            else
            {
                $data['file_url'] =  "uploads". DS ."audio". DS .$info->getSaveName();
                $res['src'] =  UPLOADS_AUDIO_URL.str_replace(DS,'/',$info->getSaveName());
            }
            $data['file_size'] = round(floor($info->getSize()/1024/1024),2);
            $data['file_format'] = input('post.type');
            $data['upload_time'] = date("Y-m-d H:i:s");
            
            // 存入数据库中,并获取文件ID
            Db::table('file')->insert($data);
            $file_id = Db::table('file')->getLastInsID();
            
            // 设置返回参数
            $res['code'] = 2;
            $res['msg'] = '上传成功!';
            $res['file_id'] = $file_id;
        }else
        {
            $res['code'] = 0;
            $res['msg'] = $file->getError();
        }
        return json($res);
    }
    
    // 音频文件下载
    
}
