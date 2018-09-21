<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

class Cat extends Base
{
    
    // 考试科目主页
    public function showlist()
    {
        // 显示所有科目
        $res = Db::table('edu_test_cat')->select();
        return view('showlist',['data' => $res]);

    }
    
    // 新增新闻栏目
    public function  add()
    {
        // 新增栏目
        // 先检查新增栏目是否已经存在
        $res = Db::table('edu_test_cat')->where('cat_name',input('post.cat_name'))->find();
        if($res == null)
        {
            $create_time = date("Y-m-d H:i:s");
            $data = ['cat_name' => input('post.cat_name')];
            $res = Db::table('edu_test_cat')->insert($data);
            if($res == 1)
                $this->success('新增考试科目成功！','Cat/showlist');
            else
                $this->error('新增考试科目失败，请重试！','Cat/showlist');
        }else
            // 该栏目已存在
            $this->error('新增失败，该科目名已存在！','Cat/showlist');
    }
    
    // 新闻栏目名称删除
    public function  delete()
    {
        // 删除该栏目
        $res = Db::table('edu_test_cat')->where('cid',input('get.cid'))->delete();
        if($res === 1)
        {
            // 继续删除该栏目下的新闻
            $res = Db::table('news')->where('col_id',input('get.col_id'))->delete();
            $this->success('删除该考试科目成功！','Cat/showlist');
        }else
            $this->error('删除该考试科目失败，请重试！','Cat/showlist');
    }
    
    // 新闻栏目名称修改
    public function  modify()
    {
        if(empty($_POST))
            // 进入修改名称页面，并传入栏目ID参数
            return view('modify',['cid' => input('get.cid'),'cat_name' => input('get.cat_name')]);
        else 
        {
            // 修改程序
            if(input('post.new_cat_name') == input('post.old_cat_name'))
                // 栏目名没有修改
                $this->error('修改失败，新旧考试科目名重复！','Cat/showlist');
            else
            {
                // 先检查修改栏目名是否已经存在
                $res = Db::table('edu_test_cat')->where('cat_name',input('post.new_cat_name'))->find();
                if($res == null)
                {
                    $res = Db::table('edu_test_cat')->where('cid',input('post.cid'))->update(['cat_name' => input('post.new_cat_name')]);
                    if($res == 1)
                        $this->success('考试科目修改成功！','Cat/showlist');
                    else
                        // 栏目名修改失败
                        $this->error('考试科目名修改失败，请重试！','Cat/showlist');
                }else 
                    // 该栏目已存在
                    $this->error('修改失败，该考试科目名已存在！','Cat/showlist');
            }
        }
    }
}
