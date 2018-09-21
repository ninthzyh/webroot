<?php
namespace app\app\controller;

use think\Db;


class Course
{
    // 课程列表
    public function getCourseList()
    {
        $start = (intval(input('post.page')) - 1) * COURSE_NUMBER;
        $res = Db::table('course')
                ->where('status',1)
                ->field('course_id,title,description,head_img,push_time')
                ->limit($start,COURSE_NUMBER)
                ->order('push_time desc')
                ->select();
        if(empty($res))
            // 课程已加载完毕
            echo json_encode(['status' => 'fail']);
        else
            // 下一页的课程列表数据
            echo json_encode(['status' => 'success','data' => $res]);
    }
    
    // 课程详细信息
    public function getCourseById()
    {
        $res = Db::table('course')
                   ->where('course_id',input('post.course_id'))
                   ->field('title,video_url,content')
                   ->find();
        echo json_encode(['status' => 'success','data' => $res]);
    }
}

