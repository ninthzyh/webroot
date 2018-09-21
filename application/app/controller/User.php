<?php
namespace app\app\controller;

use think\Db;

Class User
{
	
    // 用户注册控制器 
    public function register()
	{
		if(empty($_POST))
		    // 非法访问本接口
		    echo "fail";
		else
		{
		    // 先检查该用户是否已注册
		    $res = Db::table('user')->where('identity_id',input('post.identity_id'))->field('user_id')->find();
		    if($res == null)
		    {
		        // 新用户，将用户信息存入表中
		        $res = Db::table('user')->insert(input('post.'));
		        if($res == 1)
		            // 注册成功
		            echo json_encode(['status'=>'success']);
		        else
		            // 注册失败
		            echo json_encode(['status'=>'fail','code'=>'-1']);
		    }
		    else
		        // 该身份证号已经注册
		        echo json_encode(['status'=>'fail','code'=>'-2']);
		} 
	}
	
	// 用户登录控制器
	public function login()
	{
	    if(empty($_POST))
	        // 非法访问本接口
	        echo "fail";
	    else
	    {
	        // 根据手机号获取登录密码
	        $res = Db::table('user')->where('mobile',input('post.mobile'))->field('user_pwd,user_id')->find();
	        if($res == null)
	            // 该手机号不存在
	            echo json_encode(['status'=>'fail','code'=>'-1']);
	        else
	        {
	            if($res['user_pwd'] === input('post.user_pwd'))
	                // 登录成功
	                echo json_encode(['status'=>'success','data'=>$res['user_id']]);
	            else
	                // 密码错误
	                echo json_encode(['status'=>'fail','code'=>'-2']);
	        }
	    }
	}
	
	// 获取用户信息
	public function getUserInfo()
	{
	    if(empty($_POST))
	        // 非法访问本接口
	        echo "fail";
	    else
	    {
	        $res = Db::table('user')->where('user_id',input('post.user_id'))->field('user_name,mobile,identity_id,company_name')->find();
	        echo json_encode(['status'=>'success','data'=> $res]);
	    }
	    
	}
	
    // 忘记密码
    public function forgetPwd()
    {
        if(empty($_POST))
            // 非法访问本接口
            echo "fail";
        else
        {
            // 根据提交的身份证和手机号查找
            $res = Db::table('user')->
                    where('mobile',input('post.mobile'))->
                    where('identity_id',input('post.identity_id'))->
                    field('user_id')->
                    find();
            if($res == null)
                echo  json_encode(['status'=>'fail']);
            else 
                echo  json_encode(['status'=>'success','data'=>$res['user_id']]);
        }  
    }
    
    // 重置密码
    public function resetPwd()
    {
        if(empty($_POST))
            // 非法访问本接口
            echo "fail";
        else
        {
            // 根据提交的身份证和手机号查找
            $res = Db::table('user')->
                    where('user_id',input('post.user_id'))->
                    update(['user_pwd' => input('post.user_pwd')]);
            if($res==1||$res==0)
                echo  json_encode(['status'=>'success']);
            else 
                echo  json_encode(['status'=>'fail']);
                    
        }           
    }
	
	
	public function test()
	{
	    $obj = new \app\common\Common();
	    $param['title'] = '美食家';
	    $param['content'] = '号桌顾客申请退款，点击查看详情》》》';
	    //设置通知类型，0代表为简单呼叫服务，1代表有新的订单,2代表订单已支付
	    $param['extras'] =  array('type' => '1');
	    $param['time'] = "2018-03-16 15:31:00";
	    $param['receiver'] = array('alias' => array('alias22'));
	    $res = $obj ->Jpush($param);
	    if($res)
	        echo "success";
	    else
	        echo "fail";

	}
	public function password($password, $password_code='lshi4AsSUrUOwWV')
	{
	    return md5(md5($password) . md5($password_code));
	}
}
