<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

class Test extends Base
{
    
    // 试卷列表主页
    public function showlist()
    {
        // 加载试题科目
        $cat = Db::table('edu_test_cat')->field('cid,cat_name')->select();
        $test = Db::name('edu_test')
                ->alias('a')
                ->join('edu_test_cat b','a.cid = b.cid','LEFT')
                ->field('tid,level,title,addtime,test_time,cat_name,status,score')
                ->order('tid desc')
                ->paginate(10);
        return view('showlist',['cat' => $cat,'data' => $test]);
    }
    
    // 创建试卷
    public function  create()
    {
        if(empty($_POST))
        {
            // 加载试题科目
            $cat = Db::table('edu_test_cat')->field('cid,cat_name')->select();
            return view('create',['cat' => $cat]);    
            
        }else
        {
            $data['title'] = input('post.title');
            $data['level'] = input('post.level');
            $data['cid'] = input('post.cid');
            $data['test_time'] = input('post.test_time');
            $data['addtime'] = date("Y-m-d H:i:s");
            
            // 存入数据库
            $res = Db::table('edu_test')->insert($data);
            if($res==1)
                $this->success('添加试卷成功！','Test/showlist');
            else
                $this->error('添加试卷失败，请重试！','Test/showlist');
        }
    }
    
    // 试卷删除
    public function  delete()
    {
        // 删除该套试卷
        Db::table('edu_test')->where('tid',input('get.tid'))->delete();
        // 删除该套试卷所有试题
        Db::table('edu_test_quest')->where('tid',input('get.tid'))->delete();
        // 删除用户的分数表
        Db::table('edu_score')->where('tid',input('get.tid'))->delete();
       
        $this->success('试题删除成功！','Test/showlist');

    }
    
    // 试卷分值设置
    public function setQuestScore()
    {
        if(empty($_POST))
        {
            $res = Db::table("quest_score")->select();
            return view('score',['data' => $res]);
        }else
        {
            foreach ($_POST as $k => $v)
            {
                $arr = explode("_",$k);
                $res = Db::table('quest_score')->where('type_id', $arr[1])->update(['score' => $v]);
            }
            $this->success('试题分值设置成功！','Test/setQuestScore');   
        }
    }
    
    // 发布试卷
    public function publish()
    {
        $res = Db::table('edu_test')->where('tid',input('get.tid'))->update(['status' => 1]);
        if($res == 1)
            $this->success('试卷发布成功！','Test/showlist');
        else 
            $this->error('试卷发布失败，请重试！','Test/showlist');
    }
    
    // 试题添加列表 
    public function addQuest()
    {
           if(input('get.tid') == NULL)
               $tid = cookie('tid');
           else{
               $tid = input('get.tid');
               cookie('tid', $tid, 3600*48);
           }   
        // 获取试卷名
        $test_name  = Db::table('edu_test')->where('tid',$tid)->field('title')->find();
        // 获取全部试题
        $quest = Db::name('edu_quest')
                ->alias('a')
                ->join('edu_test_cat b','a.cid = b.cid','LEFT')
                ->field('qid,content,cat_name,type')
                ->order('type desc')
                ->paginate(10);
        // 获取指定试卷的全部试题
        $test_quest = Db::table('edu_test_quest')
                        ->alias('a')
                        ->join('edu_quest b','a.qid = b.qid')
                        ->join('edu_test_cat c','b.cid = c.cid')
                        ->where('tid',$tid)
                        ->field('a.qid,content,type,cat_name')
                        ->select();
        return view('addquest',[
            'tid' => $tid,
            'data' => $quest,
            'test_quest'=> $test_quest,
            'test_name' => $test_name['title']
        ]);
    }
    
    // 删除试题
    public function deleteQuest()
    {
        $res = Db::table('edu_test_quest')
                ->where('tid',input('get.tid'))
                ->where('qid',input('get.qid'))
                ->delete();
        if($res==1)
            echo json_encode(['status' => 'success']);
        else
            echo json_encode(['status' => 'fail']);
    }

    // 更新试题
    public function updateQuest()
    {
        $data = array();
        $qids = json_decode($_POST['qids']);
        
        foreach($qids as $k => $v)
            array_push($data,['tid' => $_POST['tid'],'qid' => $v]);
        
        $res = Db::table('edu_test_quest')->insertAll($data);//添加试题
        
        if($res >= 1)
            echo json_encode(['status' => 'success']);
        else
            echo json_encode(['status' => 'fail']);
    }
   
    // 保存试题
    public function saveQuest()
    {
         $res = Db::table('edu_test_quest')
                ->alias('a')
                ->join('edu_quest b','a.qid = b.qid')
                ->join('quest_score c','b.type = c.type_id')
                ->where('tid',input('post.tid'))
                ->field('score')
                ->select();
         $score = 0;
         foreach($res as $k => $v)
         {
             $score += $v['score'];
         }
         Db::table('edu_test')->where('tid',input('post.tid'))->update(['score' => $score]);
         $this->success('试卷保存成功！','Test/showlist');
    }
}
