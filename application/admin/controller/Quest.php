<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

class Quest extends Base
{
    
    // 试题主页
    public function showlist()
    {
         // 显示所有试题
         // 加载试题科目
         $cat = Db::table('edu_test_cat')->field('cid,cat_name')->select();
         // 获取试题数据
         $quest = Db::name('edu_quest')
                    ->alias('a')
                    ->join('edu_test_cat b','a.cid = b.cid','LEFT')
                    ->field('qid,a.cid,content,cat_name,type')
                    ->order('qid desc')
                    ->paginate(10);
         return view('showlist',['cat' => $cat,'data' => $quest]);
    }
    
    // 新增试题
    public function  add()
    {
         if(empty($_POST))
         {
             // 获取科目名
             $cat = Db::table('edu_test_cat')->field('cid,cat_name')->select();
             return view('add',['cat' => $cat]);
         }else
         {
              // 拼装试题数据
              $data['type'] = input('post.type');
              $data['cid'] = input('post.cid');
              $data['content'] = input('post.content');
              $data['addtime'] = $_SERVER['REQUEST_TIME'];
              $data['modify_time'] = $_SERVER['REQUEST_TIME'];
              if(input('post.up_img')=="1")
                  $data['img_url'] = input('post.img_url');
              if(input('post.type')=="0")
              {
                  $data['aA'] = input('post.a_answer');
                  $data['aB'] = input('post.b_answer');
                  $data['aC'] = input('post.c_answer');
                  $data['aD'] = input('post.d_answer');
                  $data['answer'] = input('post.sel_answer');
              }else if(input('post.type')=="3")
                  $data['answer'] = input('post.saq_answer');
              else if(input('post.type')=="1")
                  $data['answer'] = input('post.judge_answer');
              else if(input('post.type')=="2")
              {
                  $data['aA'] = input('post.a_answer');
                  $data['aB'] = input('post.b_answer');
                  $data['aC'] = input('post.c_answer');
                  $data['aD'] = input('post.d_answer');
                  
                  $answer = "";
                  $arr = $_POST['multi_choice_answer'];
                  for($i = 0; $i < count($arr) - 1; $i++)
                  {
                      $answer.= $arr[$i].",";
                  }
                  $answer.= $arr[count($arr)-1];
                  $data['answer'] = $answer;
              }
              // 存入数据库
              $res = Db::table('edu_quest')->insertGetId($data);
              if($res){
                  if(input('post.type')=="0"||input('post.type')=="1"||input('post.type')=="2")
                      Db::table('edu_error')->insert(['qid'=>$res,'count'=>0]);
                  $this->success('试题添加成功！','Quest/showlist');
              }else 
                  $this->error('试题添加失败，请重试！','Quest/showlist');
         }
    }
    
    // 试题删除
    public function  delete()
    {
          Db::table('edu_quest')->where('qid',input('get.qid'))->delete();
          Db::table('edu_error')->where('qid',input('get.qid'))->delete();
          Db::table('edu_test_quest')->where('qid',input('get.qid'))->delete();
          $this->success('试题删除成功！','Quest/showlist');
    }
    
    // 试题信息修改
    public function  modify()
    {
        if(empty($_POST))
        {
            $cat = Db::table('edu_test_cat')->field('cid,cat_name')->select();
            $quest = Db::table('edu_quest')->where('qid',input('get.qid'))->find();
            if($quest['img_url']!=NULL)
                $up_img = "1";
            else
                $up_img = "0";
            return view('modify',['data' => $quest,'cat'=>$cat,"up_img"=> $up_img]);
        }else
        {
            // 拼装试题数据
              $data['type'] = input('post.type');
              $data['cid'] = input('post.cid');
              $data['content'] = input('post.content');
              $data['modify_time'] = $_SERVER['REQUEST_TIME'];
              if(input('post.up_img')=="1")
                  $data['img_url'] = input('post.img_url');
              if(input('post.type')=="0")
              {
                  $data['aA'] = input('post.a_answer');
                  $data['aB'] = input('post.b_answer');
                  $data['aC'] = input('post.c_answer');
                  $data['aD'] = input('post.d_answer');
                  $data['answer'] = input('post.sel_answer');
              }else if(input('post.type')=="3")
                  $data['answer'] = input('post.saq_answer');
              else if(input('post.type')=="1")
                  $data['answer'] = input('post.judge_answer');
              else if(input('post.type')=="2")
              {
                  $data['aA'] = input('post.a_answer');
                  $data['aB'] = input('post.b_answer');
                  $data['aC'] = input('post.c_answer');
                  $data['aD'] = input('post.d_answer');
                  $answer = "";
                  $arr = $_POST['multi_choice_answer'];
                  for($i = 0; $i < count($arr) - 1; $i++)
                  {
                      $answer.= $arr[$i].",";
                  }
                  $answer.= $arr[count($arr)-1];
                  $data['answer'] = $answer;
              }
              
              // 存入数据库
              $res = Db::table('edu_quest')->where("qid",input('post.qid'))->update($data);
              if($res){
                  $this->success('试题修改成功！','Quest/showlist');
              }else 
                  $this->error('试题修改失败，请重试');
        }
    }
    

}
