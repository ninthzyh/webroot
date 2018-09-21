<?php
namespace app\admin\controller;

use think\Db;

class User extends Base
{
    
    // 后台管理员列表
    public function showlist()
    {
        // 加载全部后台用户
        $res = Db::table('edu_manager')->
                alias('a')->
                join('edu_role','mg_role_id = role_id','LEFT')->
                field('mg_id,username,nickname,role_name,role_id')->
                select();
        
        $role = Db::table('edu_role')->where('role_id','neq',1)->field('role_id,role_name')->select();
        
        return view('showlist',['data' => $res,'role'=> $role]);
    }
    
    // 后台管理员增加
    public function add()
    {
        if(empty($_POST))
        {
            $role = Db::table('edu_role')->where('role_id','neq',1)->field('role_id,role_name')->select();
            return view('add',['role'=> $role]);
        }else{
            
            if($this->checkRepeat(input('post.username'),input('post.nickname'))){
                // 组装数据
                $data['username'] = input('post.username');
                $data['password'] = md5(input('post.password'));
                $data['nickname'] = input('post.nickname');
                $data['original_pwd'] = input('post.password');
                $data['mg_role_id'] = input('post.mg_role_id');
                // 存入数据库中
                $res = Db::table('edu_manager')->insert($data);
                if($res==1)
                    $this->success('添加管理员成功！','User/showlist');
                else
                    $this->error('添加管理员失败，请重试！','User/showlist');
            }else{
                $this->error('管理员登录名或者昵称存在重复，请重新添加！','User/showlist');
            }
            
            
        }
    }
    
    // 后台管理员删除
    public function delete()
    {
        $res = Db::table('edu_manager')->where('mg_id',input('get.mg_id'))->delete();
        if($res==1)
            $this->success('管理员删除成功！','User/showlist');
        else
            $this->error('管理员删除失败，请重试！','User/showlist');
    }
    
    // 管理员修改
    public function modify()
    {
        if(empty($_POST))
        {
            // 获取该管理员信息
            $res = Db::table('edu_manager')->
                        where('mg_id',input('get.mg_id'))->
                        field('mg_id,username,original_pwd,nickname,mg_role_id')->
                        find();
            // 获取角色信息
            $role = Db::table('edu_role')->where('role_id','neq',1)->field('role_id,role_name')->select();
            
            return view('modify',['data' => $res,'role'=> $role]);
            
        }else
        {
            if($this->checkRepeat(input('post.username'),input('post.nickname'))){
                // 组装数据
                $data['username'] = input('post.username');
                $data['password'] = md5(input('post.password'));
                $data['nickname'] = input('post.nickname');
                $data['original_pwd'] = input('post.password');
                $data['mg_role_id'] = input('post.mg_role_id');
                
                // 数据更新
                $res = Db::table('edu_manager')->where('mg_id',input('post.mg_id'))->update($data);
                if($res==1||$res==0)
                    $this->success('管理员信息修改成功！','User/showlist');
                else
                    $this->error('管理员信息修改失败，请重试！','User/showlist');
            }else{
                $this->error('管理员登录名或者昵称存在重复，请重新添加！','User/showlist');
            }
        }
    }
    
    // 检查管理员登录名以及昵称是否重复
    private function checkRepeat($username,$nickname)
    {
        $res = Db::table('edu_manager')->
                where('username = '.$username.' or nickname = '.$nickname)->
                find();
        
        if($res == null)
            return true;
        else
            return false;
    }
    
}
