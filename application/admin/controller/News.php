<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

class News extends Base
{
     // 文章管理
     public function manage()
     {
         $res = Db::name('news')
                ->alias('a')
                ->join('column b','a.col_id = b.col_id','LEFT')
                ->field('article_id,title,head_img,col_name,author,status,a.create_time,push_time,is_push,push_time')
                ->order('status,push_time desc')
                ->paginate(10);
         return view('manage',['data' => $res]);
     }
     
     // 文章修改
     public function modify()
     {
         if(empty($_POST))
         {
             // 显示需要修改的文章
             // 先获取文章详情数据
             $article = Db::table('news')
                    ->where('article_id',input('get.article_id'))
                    ->field('article_id,status,col_id,title,description,head_img,author,content,is_push,push_time,push_range')
                    ->find();
             
             // 分割日期和时间
             $datetime = explode(" ",$article['push_time']);  
             $date = $datetime[0];
             $time = $datetime[1];
             // 获取栏目信息
             $column = Db::table('column')->field('col_id,col_name')->select();
             return view('modify',['date'=>$date,'time'=>$time ,'data' => $article,'column' => $column]);    
             
         }else 
         {
             // 拼装数据
             $data['title'] = input('post.title');
             $data['description'] = input('post.description');
             $data['head_img'] = input('post.head_img');
             $data['modify_time'] = date("Y-m-d H:i:s");
			 
			 $data['content'] = str_replace("line-height","/* line-height",input('post.content'));
			 $data['content'] = str_replace("color","*/ color",$data['content']);
			 
             $data['col_id'] = input('post.col_id');
             $data['is_push'] = input('post.is_push');
             if(input('post.is_push') == '1'){
                 $data['push_time'] = input('post.push_time');
                 $data['push_range'] = input('post.range');
             }            
             // 更新数据
             $res = Db::table('news')->where('article_id',input('post.article_id'))->update($data);
             if($res==1){
                 $this->success('修改成功！','News/manage');
                 if(input('get.is_push')=='0')
                     $this->newsPush(input('get.article_id'));
             }
             else
                 $this->error('修改失败，请重新修改！','News/manage');
         }
     }
     
     // 文章审核
     public function check()
     {
         if(empty($_GET))
             $this->redirect('News/manage');
         else
         {
             // 同意改篇文章发布
             
             // 设置文章的发布时间，若文章为推送文章，则发布时间就是推送时间,不用单独设置
             // 若文章为非推送文章，则发布时间为当前审核通过时间
             if(input('get.is_push')=='0')
                    $data['push_time'] = date("Y-m-d H:i:s");
             $data['status'] = 1;
             $res = Db::table('news')
                    ->where('article_id',input('get.article_id'))
                    ->update($data);
             
             // 如果改篇文章为推送文章，则添加推送任务
             if($res==1){
                 $this->success('文章发布成功！','News/manage');
                 if(input('get.is_push')=='0')
                     $this->newsPush(input('get.article_id'));
             }
             else
                 $this->error('文章发布失败，请重试！','News/manage');
         }
     }
     
     // 文章删除
     public function delete()
     {
         $res = Db::name('news')->where('article_id',input('get.article_id'))->delete();
         if($res ==1 )
             $this->success('文章删除成功','News/manage');
         else 
             $this->error('文章删除失败，请重试！','News/manage');
     }
      
     // 文章发布
     public function publish()
     {
         if(empty($_POST))
         {
             // 发表新文章
             // 获取栏目名
             $column = Db::table('column')->field('col_id,col_name')->select();
             return view('publish',['column' => $column]);
             
         }else
         {
             // 拼装数据
             $data['title'] = input('post.title');
             $data['description'] = input('post.description');
             $data['head_img'] = input('post.head_img');
             $data['create_time'] = date("Y-m-d H:i:s");
             $data['modify_time'] = $data['create_time'];
			 
             $data['content'] = str_replace("line-height","/* line-height",input('post.content'));
			 $data['content'] = str_replace("color","*/ color",$data['content']);
			 
             $data['col_id'] = input('post.col_id');
             $data['is_push'] = input('post.is_push');
             $data['push_range'] = input('post.push_range');
             
             // 编辑作者
             $info = Db::table('edu_manager')->where('mg_id',session('mg_id'))->field('nickname')->find();
             $data['author'] = $info['nickname'];
             
             // 推送时间设置，若为推送文章，推送时间为设置时间，否则为当前时间
             if(input('post.is_push') == '1')
                 $data['push_time'] = input('post.push_time');
             else 
                 $data['push_time'] = $data['create_time'];
             
             // 存入数据库
             $res = Db::table('news')->insert($data);
             if($res==1)
                 $this->success('添加文章成功！','News/publish');
             else 
                 $this->error('添加文章失败，请重试！','News/publish');
         }
     }
     
     // 如果该篇文章为推送文章，则添加推送任务
     /**
      * @param unknown $article_id
      */
     private function newsPush($article_id)
     {
          $res = Db::table('news')->
                  where('article_id',$article_id)->
                  field('push_time,push_range,article_id,title')->
                  find();
          
          $obj = new \app\common\Common();
          $param['title'] = '教育考试APP';
          $param['content'] = $res['title'].'，点击查看详情》》》';
          $param['extras'] =  array('article_id'=>$article_id);
          $param['time'] = $res['push_time'];
          // 全部都推送
          if($res['push_range'] == 1)
               $param['receiver'] = 'all';
          // 仅限登录用户接受
          else 
               $param['receiver'] =  array('alias'=>array('login'));
          $obj ->Jpush($param);  
     }
}
