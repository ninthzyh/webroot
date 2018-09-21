<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

class Column extends Base
{
    
    // 新闻栏目显示主页
    public function index()
    {
        // 显示所有新闻栏目
        $res = Db::table('column')->select();
        return view('index',['data' => $res]);

    }
    
    // 新增新闻栏目
    public function  add()
    {
        // 新增栏目
        // 先检查新增栏目是否已经存在
        $res = Db::table('column')->where('col_name',input('post.col_name'))->find();
        if($res == null)
        {
            $create_time = date("Y-m-d H:i:s");
            $data = ['col_name' => input('post.col_name'),'create_time' => $create_time];
            $res = Db::table('column')->insert($data);
            if($res == 1)
                $this->success('新增栏目成功！','Column/index');
            else
                $this->error('新增栏目失败，请重试！','Column/index');
        }else
            // 该栏目已存在
            $this->error('新增失败，该栏目名已存在！','Column/index');
    }
    
    // 新闻栏目名称删除
    public function  delete()
    {
        // 删除该栏目
        $res = Db::table('column')->where('col_id',input('get.col_id'))->delete();
        if($res === 1)
        {
            // 继续删除该栏目下的新闻
            $res = Db::table('news')->where('col_id',input('get.col_id'))->delete();
            $this->success('删除该栏目成功！','Column/index');
        }else
            $this->error('删除该栏目失败，请重试！','Column/index');
    }
    
    // 新闻栏目名称修改
    public function  modify()
    {
        if(empty($_POST))
            // 进入修改名称页面，并传入栏目ID参数
            return view('modify',['col_id' => input('get.col_id'),'col_name' => input('get.col_name')]);
        else 
        {
            // 修改程序
            if(input('post.new_col_name') == input('post.old_col_name'))
                // 栏目名没有修改
                $this->error('修改失败，新旧栏目名重复！','Column/index');
            else
            {
                // 先检查修改栏目名是否已经存在
                $res = Db::table('column')->where('col_name',input('post.new_col_name'))->find();
                if($res == null)
                {
                    $res = Db::table('column')->where('col_id',input('post.col_id'))->update(['col_name' => input('post.new_col_name')]);
                    if($res == 1)
                        $this->success('栏目修改成功！','Column/index');
                    else
                        // 栏目名修改失败
                        $this->error('栏目名修改失败，请重试！','Column/index');
                }else 
                    // 该栏目已存在
                    $this->error('修改失败，该栏目名已存在！','Column/index');
            }
        }
    }
}
