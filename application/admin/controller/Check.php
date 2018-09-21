<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;
use think\console\Input;

class Check extends Base
{
    // 试卷列表
    public function showlist()
    {
        // 加载试题科目
        $cat = Db::table('edu_test_cat')->field('cid,cat_name')->select();
        $test = Db::name('edu_test')
                ->alias('a')
                ->join('edu_test_cat b','a.cid = b.cid','LEFT')
                ->where('status',1)
                ->field('tid,level,title,cat_name')
                ->order('tid desc')
                ->paginate(10);
        return view('showlist',['cat' => $cat,'data' => $test]);
    }
    
    // 加载所有考生答题列表
    public function allUserTest()
    {
        // 先加载该套试卷所有考生整体答题信息
        $res = Db::table('edu_score')->
                 alias('a')->
                 join('user b','uid = user_id','left')->
                 where('tid',input('post.tid'))->
                 field('id,user_name,company_name,mobile,score,addtime')->
                 order('addtime desc')->
                 paginate(10);
        return view('list',['data' => $res]);
    }
    
    // 加载某个考生答题详细情况
    public function userTestDetail()
    {
        // 先加载该套试卷所有试题信息
	    $test =  Db::table('edu_test_quest')->
        	        alias('a')->
        	        join('edu_quest b','a.qid = b.qid')->
        	        where('tid',input('get.tid'))->
        	        field('a.qid,type,img_url,content,aA,aB,aC,aD,answer')->
        	        select();
	    
	    // 再加载考生答题情况
        $result = Db::table('edu_score')->
                    where('id',input('get.id'))->
                    field('answer')->
                    find();
        // 考生答案为1-A,2-1,3-B.C.D
        $key_array = array();//题号数组
        $val_array = array();//答案数组
        if(!empty($result)){
            $res = explode(",",$result);
            foreach($res as $k => $v)
            {
                $arr = explode("-",$v);
                array_push($key_array,$arr[0]);
                array_push($val_array,$arr[1]);
            } 
        }
        return view('detail',['test' => $test,'key_array' => $key_array,'val_array'=>$val_array]);
    }
}
