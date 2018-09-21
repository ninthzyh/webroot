<?php
namespace app\admin\controller;


use think\Controller;
use think\Request;
use think\Db;

/**
 * 后台基类控制器
 * @author Sperk
 *
 */
Class Base extends Controller
{
    //用户所拥有的的权限  
    protected $auth  = '';
    
    
    
    /**
     * 自动初始化函数
     */
    public function _initialize()
    {
        //先判断是否登录
        if(!$this->checkLoginStatus())
            $this->redirect('Manager/login'); 
        
        //检测用户权限是否符合
        if(!$this->checkManagerAuth())
             $this->error('对不起，您没有该权限');
           
        
    }
    
    /**
     * 404页面
     * @return string
     */
    public function _empty()
    {
        echo '<html><head><title>404 Not Found</title></head><body bgcolor="white"><center><h1>404 Not Found</h1></center><hr><center>nginx</center></body></html>';
    }
    
    /**
     * 检测是否登录
     * @return bool
     */
    
    protected function checkLoginStatus()
    {
        if(session('mg_id')==null)
            return false;
        else 
            return true;
        
    }
    
    
    /**
     * 检测用户权限
     * @return bool
     */
    protected function checkManagerAuth()
    {
        if(session('role_id')== 1)
            return true;
        
        //当前控制器名
        $controller = Request::instance()->controller();
        if($controller=='Index'){
            return true;
        }
        //当前方法名
        $action = Request::instance()->action();
        
        
        //先判断权限基基准表中是否有数据
        $this->auth = Db::table('edu_role')->
                        where('role_id',session('role_id'))->
                        field('role_auth_ac')->
                        find();
        //检查当前控制与方法操作是否合法
        $pos = strpos($this->auth['role_auth_ac'],strtolower($controller).'-'.$action);
        if($pos===false)
            return false;
        else{
            session('last_url',strtolower($controller).'/'.$action);
            return true;
        }
    }
}