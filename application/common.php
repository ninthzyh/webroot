<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
namespace app\common;

class Common
{
    /**
     * 极光推送函数
     * 
     * $param['receiver']  array    目标人群：tag 标签；tag_and 标签AND；alias 别名；registration_id 注册ID
     * $param['title']     string   通知栏展示的App名称
     * $param['content']   string   通知栏展示的内容
     * $param['extras']    array    附加字段
     * $param['time']      string   推送时间
     */
    public function Jpush($param=array())
    {
        //获取极光推送相关配置信息
        $config  = config('jpush');
        $Jpush = new \api\ScheduleJpush($config);
        $res = $Jpush ->push($param);
        if($res)
            return true;
        else 
            return false;
    }
}