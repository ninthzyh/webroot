<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

class Manager extends Controller
{
    
	// 后台登录页面
    /**
     * @return \think\response\View
     */
    public function login()
	{
		if(empty($_POST))
		    // 管理员登录
	        return view('login',['error' => '']);
		else{
		    // 登录验证
		    //if(!captcha_check(input('post.code')))
		      //  return view('login',['error' => '验证码错误，请重新输入！']);
		  //  else{
		        // 获取用户信息
		        $res = Db::table('edu_manager')->
		                  where('username',input('post.username'))->
		                  field('mg_id,mg_role_id,password')->
		                  find();
		        if($res)
		        {
		            if($res['password']===md5(input('post.password')))
		            {
		                // 存储管理员相关信息
		                session('mg_id',$res['mg_id']);
		                session('role_id',$res['mg_role_id']);
		                session('last_url','news/manage');
		                $this->redirect('Index/index');
		            }
		            else
		                return view('login',['error' => '用户名或者密码错误！']);
		        }else 
		            return view('login',['error' => '用户名或者密码错误！']);
		  //  }
		}
	}
	
    // 管理员注销
	public function logout()
	{
	    session(null);
	    $this->success('退出登录成功！','Manager/login');
	}
}
