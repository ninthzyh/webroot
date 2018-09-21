<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

class Course extends Base
{
    // 课程列表
    public function showlist()
    {
        $res = Db::table('course')
                ->field('course_id,title,head_img,create_time,status,file_id')
                ->order('status,create_time desc')
                ->paginate(10);
        return view('showlist',['data' => $res]);
    }
    
    // 课程添加
    public function publish()
    {
        if(empty($_POST))
            return view();
        else
        {
            
            $data['title'] = input('post.title');
            $data['description'] = input('post.description');
            $data['content'] = input('post.content');
            $data['head_img'] = input('post.head_img');
            $data['video_url'] = input('post.video_url');
            $data['create_time'] = date("Y-m-d H:i:s");
            $data['push_time'] = $data['create_time'];
            $data['modify_time'] = $data['create_time'];
            $data['file_id'] = input('post.file_id');
               
            // 存入数据库
            $res = Db::table('course')->insert($data);
            if($res==1)
                $this->success('添加课程成功！','Course/showlist');
            else
                $this->error('添加课程失败，请重试！','Course/publish');
        }
    }
    
    // 课程删除
    public function delete()
    {
        // 删除课程
        Db::table('course')->where('course_id',input('get.course_id'))->delete();
        
        
        $res = Db::table('file')->where('id',input('get.file_id'))->field('file_url')->find();
        // 删除课程音频文件记录
        Db::table('file')->where('id',input('get.file_id'))->delete();
        
        if($res != null)
        // 删除课程音频文件
            unlink(ROOT_PATH  . "public". DS . $res['file_url']);
        
        $this->success('课程删除成功！','Course/showlist');
    }
    
    // 课程确认
    public function check()
    {
        $push_time = date("Y-m-d H:i:s");
        $res = Db::table('course')
                ->where('course_id',input('get.course_id'))
                ->update(['status' => 1,'push_time' => $push_time]);
        if($res == 1)
            $this->success('课程发布成功！','Course/showlist');
        else 
            $this->error('课程发布失败，请重试！','Course/showlist');
    }

    // 课程修改
    public  function modify()
    {
        if(empty($_POST))
        {
            //  获取课程信息
            $res = Db::table('course')
                    ->where('course_id',input('get.course_id'))
                    ->field('course_id,title,description,content,head_img,video_url,file_id')
                    ->find();
            return view('modify',['data' => $res]);         
            
        }else 
        {
            // 组装数据
            $data['title'] = input('post.title');
            $data['description'] = input('post.description');
            $data['content'] = input('post.content');
            $data['head_img'] = input('post.head_img');
            $data['video_url'] = input('post.video_url');
            $data['file_id'] = input('post.file_id');
            $data['modify_time'] = date("Y-m-d H:i:s");
            
            // 更新课程信息
            $res = Db::table('Course')->where('course_id',input('post.course_id'))->update($data);
            if($res == 1)
            {
                // 更新课程成功，判断课程所用到的视频文件有没有改变,变了的话就需要删除
                if(input('post.file_id')!=input('post.old_file_id'))
                {
                    $res = Db::table('file')->where('id',input('post.old_file_id'))->field('file_url')->find();
                    // 删除课程音频文件记录
                    Db::table('file')->where('id',input('post.old_file_id'))->delete();
                    if($res != null)
                        // 删除课程音频文件
                        unlink(ROOT_PATH . "public". DS . $res['file_url']);
                }
                $this->success('修改课程成功！','Course/showlist');
            }else 
                $this->error('修改课程失败，请重试！','Course/showlist');
        }
    }
}

