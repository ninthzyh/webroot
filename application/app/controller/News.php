<?php
namespace app\app\controller;

use think\Db;

Class News
{   
    // 获取新闻所有栏目
    public function allColumn()
    {
        $res = Db::table('column')->field('col_id,col_name')->select();
        echo json_encode(['status' => 'success','data' => $res]);
    }
    
    // 获取最新发表10条的新闻文章列表数据
    public function getNewsList()
    {
        $res = Db::table('news')
        ->alias('a')
        ->join('column b','a.col_id = b.col_id','LEFT')
        ->where('status',1)
        ->field('a.article_id,title,head_img,push_time,col_name,description')
        ->limit(0,NEWS_NUMBER)
        ->order('push_time desc')
        ->select();
        if(empty($res))
            // 无数据
            echo json_encode(['status' => 'fail']);
        else
            echo json_encode(['status' => 'success','data' => $res]);       
    }

    // 根据新闻文章所属栏目ID获取该栏目下的新闻列表
    public function getNewsListByColumId()
    {
        // 新闻列表每页显示
        $start = (intval(input('post.page')) - 1) * NEWS_NUMBER;
        $res = Db::table('news')
                ->where('col_id',input('post.col_id'))
                ->where('status',1)
                ->field('article_id,title,head_img,push_time,description')
                ->limit($start,NEWS_NUMBER)
                ->order('push_time desc')
                ->select();
        if(empty($res))
            // 该栏目的所有新闻已加载完毕
            echo json_encode(['status' => 'fail']);
        else
            // 下一页的新闻列表数据
            echo json_encode(['status' => 'success','data' => $res]);
        
    }
     
    // 根据新闻文章ID获取该文章详细内容
    public function newsDetail()
    {
        $res = Db::table('news')->where('article_id',input('post.article_id'))->field('content,push_time,title,author')->find();
        echo json_encode(['status' => 'success','data' => $res]);
    }
}