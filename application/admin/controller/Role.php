<?php
namespace app\admin\controller;

use think\Db;
use think\console\Input;

class Role extends Base
{
    
    // 所有角色列表
    public function showlist()
    {
        // 加载全部管理员角色  
        $role = Db::table('edu_role')->
                    field('role_id,role_name')->
                    select();
        
        return view('showlist',['data'=> $role]);
    }
    
    // 管理员角色增加
    public function add()
    { 
        // 先检查新增管理员角色是否已经存在
        $res = Db::table('edu_role')->where('role_name',input('post.role_name'))->find();
        if($res == null)
        {
            $data = ['role_name' => input('post.role_name')];
            $res = Db::table('edu_role')->insert($data);
            if($res == 1)
                $this->success('新增管理员角色成功！','Role/showlist');
            else
                $this->error('新增管理员角色失败，请重试！','Role/showlist');
        }else
            // 该管理员角色已存在
            $this->error('新增失败，该管理员角色已存在！','Role/showlist');
    }
    
    // 后台管理员删除
    public function delete()
    {  
        $res = Db::table('edu_role')->where('role_id',input('get.role_id'))->delete();
        if($res==1)
            $this->success('角色删除成功！','Role/showlist');
        else
            $this->error('角色删除失败，请重试！','Role/showlist');
    }
    
    // 角色修改
    public function modify()
    {
       if(empty($_POST))
            // 进入修改名称页面
            return view('modify',['role_id' => input('get.role_id'),'role_name' => input('get.role_name')]);
        else 
        {
            // 修改程序
            if(input('post.new_role_name') == input('post.old_role_name'))
                // 管理员角色名没有修改
                $this->error('修改失败，新旧管理员角色名重复！','Role/showlist');
            else
            {
                // 先检查管理员角色名是否已经存在
                $res = Db::table('edu_role')->where('role_name',input('post.new_role_name'))->find();
                if($res == null)
                {
                    $res = Db::table('edu_role')->where('role_id',input('post.role_id'))->update(['role_name' => input('post.new_role_name')]);
                    if($res == 1)
                        $this->success('管理员角色名修改成功！','Role/showlist');
                    else
                        // 管理员角色名修改失败
                        $this->error('管理员角色名修改失败，请重试！','Role/showlist');
                }else 
                    // 该管理员角色名已存在
                    $this->error('修改失败，该管理员角色名已存在！','Role/showlist');
            }
        }
    }

    // 角色权限修改
    public function auth()
    {
        // 查看角色拥有的权限
        if(empty($_POST))
        {
            // 获取全部权限
            $pauth = Db::table('edu_auth')->where('auth_level',0)->field('auth_id,auth_name,auth_pid')->select();
            $sauth = Db::table('edu_auth')->where('auth_level',1)->field('auth_id,auth_name,auth_pid')->select();
            $tauth = Db::table('edu_auth')->where('auth_level',2)->field('auth_id,auth_name,auth_pid')->select();

            // 获取该角色的拥有的权限
            $role = Db::table('edu_role')->where('role_id',input('get.role_id'))->field('role_id,role_name,role_auth_ids')->find();
            $auth_ids_arr = explode(',',$role['role_auth_ids']); //数组auth_id 信息
            
            return view('auth',[
                'pauth' => $pauth,
                'sauth' => $sauth,
                'tauth' => $tauth, 
                'role_id' => $role['role_id'],
                'role_name' => $role['role_name'],
                'auth_ids_arr'=>$auth_ids_arr
            ]);
        // 更新角色权限
        }else 
        {
            //把权限id信息有数组变为中间用逗号的分隔的字符串信息
            $auth_ids = implode(',',$_POST['authname']);

            //根据ids权限id信息查询具体操作方法信息
            $info = Db::table('edu_auth')->
                        where('auth_id IN ('.$auth_ids.')')->
                        field('auth_c,auth_a')->
                        select();
            //拼装控制器和操作方法
            $auth_ac = '';
            foreach($info as $k => $v){
                if(!empty($v['auth_c']) && !empty($v['auth_a'])){
                    $auth_ac .= $v['auth_c']."-".$v['auth_a'].",";
                }
            }
            $auth_ac = rtrim($auth_ac,',');//删除最右边的逗号
            
            // 更新权限
            Db::table('edu_role')->
                where('role_id',$_POST['role_id'])->
                update(['role_auth_ids'=>$auth_ids,'role_auth_ac'=>$auth_ac]);
            
            $this->success('修改角色权限成功！','Role/showlist'); 
        }
                    
    }
}
