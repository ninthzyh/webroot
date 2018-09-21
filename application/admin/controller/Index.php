<?php
namespace app\admin\controller;

use think\Db;

class Index extends Base
{
    
    // 管理员主界面
    public function index()
    {
        // 先获取管理员对应角色的ID
        if(session('role_id')==1)
            // 超级管理员获取全部权限
            $res = Db::table('edu_auth')->
                   where('auth_level','neq',2)->
                   field('auth_id,auth_pid,auth_c,auth_a,auth_name,auth_level,icon')->
                   select(); 
        else{    
            $res = Db::table('edu_role')->
                       where('role_id',session('role_id'))->
                       field('role_auth_ids as ids')->
                       find();
            //  获取管理员左侧权限
            $res = Db::table('edu_auth')->
                       where('auth_id IN ('.$res['ids'].')')->
                       where('auth_level','neq',2)->
                       field('auth_id,auth_pid,auth_c,auth_a,auth_name,auth_level,icon')->
                       select(); 
        }
        
        $data = array();
        $icon = array();
        $url = array();
        
        //对权限进行处理
        foreach($res as $k => $v)
        {
            if($v['auth_level']==0)
            {
                $data['index'][$v['auth_id']]=$v['auth_name'];//存储顶级栏目ID
                $icon[$v['auth_id']]= $v['icon'];//存储顶级栏目icon图标
            }
            else
            {
                $data[$v['auth_pid']][$v['auth_id']]= $v['auth_name'];
                $url[$v['auth_id']]= $v['auth_c'].'/'.$v['auth_a'];
            }
        }
        return view('index',['data' => $data,'icon'=>$icon,'url'=>$url,'last_url'=>session('last_url')]); 
    }
}
