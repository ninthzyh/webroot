<?php
namespace app\app\controller;

use think\Db;

Class Test
{
	
    // 试卷列表控制器
    public function testList()
	{
		if(empty($_POST))
		    // 非法访问本接口
		    echo "fail";
		else
		{
		    $start = (intval(input('post.page')) - 1) * TEST_NUMBER;
		    $res = Db::table('edu_test')->
		              alias('a')->
		              join('edu_test_cat b','a.cid = b.cid','LEFT')->
		              where('status',1)->
		              field('tid,title,test_time,cat_name,score')->
		              limit($start,TEST_NUMBER)->
		              order('tid desc')->
		              select();
		    if(empty($res))
                // 暂无试卷
                echo json_encode(['status' => 'fail']);
            else
                // 加载试卷
                echo json_encode(['status' => 'success','data' => $res]);
		} 
	}
	
	// 根据试卷ID获取试卷全部信息控制器
	public function testDetail()
	{
	    if(empty($_POST))
	        // 非法访问本接口
	        echo "fail";
	    else
	    {
	        // 加载试卷全部试题
	        $res =  Db::table('edu_test_quest')->
        	        alias('a')->
        	        join('edu_quest b','a.qid = b.qid')->
        	        where('tid',input('post.tid'))->
        	        field('a.qid,type,img_url,content,aA,aB,aC,aD,answer')->
        	        select();
	        if(empty($res))
	            // 高改试卷暂无试题
	            echo json_encode(['status' => 'fail']);
	        else
	            // 试题获取成功
	            echo json_encode(['status' => 'success','data' => $res]);
	    }
	}
	
	// 用户提交答题结果
	public function testResult()
	{
	    if(empty($_POST))
	        // 非法访问本接口
	        echo "fail";
	    else
	    {
	        // 组装数据
	        $data['uid'] =  input('post.uid');
	        $data['tid'] =  input('post.tid');
	        $data['score'] = input('post.score');
	        $data['addtime'] = $_SERVER['REQUEST_TIME'];
	        $data['answer'] = input('post.answer');
	        
	        $res = Db::table('edu_score')->insert($data);
	        
	        if($res == 1)
	            // 入库成功
	            echo json_encode(['status' => 'success']);
	        else 
	            echo json_encode(['status' => 'fail']);
	    }
	}
	
	// 用户获取以已经参加考试的结果
	public function testHistory()
	{
	    if(empty($_POST))
	        // 非法访问本接口
	        echo "fail";
	    else
	    {
	        $res = Db::table('edu_score')->
	                 alias('a')->
	                 join('edu_test b','a.tid = b.tid','LEFT')->
	                 where('uid',input('post.uid'))->
	                 field('score,addtime,title')->
	                 order('addtime desc')->
	                 select();
	        if(empty($res))
	            echo json_encode(['status' => 'fail']);
	        else 
	            echo json_encode(['status' => 'success','data' => $res]);
	    }
	}
	
	
	
}
