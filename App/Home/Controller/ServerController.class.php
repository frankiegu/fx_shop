<?php
/**
 * ServerController.class.php
 * 风行者广告推广系统
 * Copy right 2020-2030 风行者 保留所有权利。
 * 官方网址: https://fxz.nixi.win/
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * @author John Doe <john.doe@example.com>
 * @date 2020-01-20
 * @version v2.0.22
 */
namespace Home\Controller;
use Think\Controller\HproseController;

class ServerController extends HproseController{
    //获取当天访问总数
    public function totalVisit($date){
        $date = trim($date) ? trim($date) : date('Y-m-d');
        $start = strtotime($date.' 00:00:00');
        $end = strtotime($date.' 23:59:59');
        $total = M('agent_log')->where(['addtime'=>[['egt',$start],['elt',$end]]])->count();
        return $total;
    }
    //定时清除缓存信息
    public function removeCache(){
        $condition = [];
        $condition['expire'] = ['lt', time()];
        M('think_cache', null)->where($condition)->delete();
    }
    //定时清除客户访问日志
    public function removeAgentlog(){
        $condition = [];
        $condition['addtime'] = ['lt', (time()-(86400*3))];
        M('agent_log')->where($condition)->delete();
    }
    //定时删除操作日志
    public function removeActionlog(){
        $condition = [];
        $condition['addtime'] = ['lt', (time()-86400*30)];
        M('action_log')->where($condition)->delete();
    }
    //定时删除统计日志
    public function removeTongjilog(){
        $xdate = date('Y-m-d',strtotime('-30 day'));
        $row = M('monitor')->where(['xdate'=>['ELT',$xdate]])->limit(1)->select();
        if($row){//定时删除统计记录
            M('monitor')->where(['xdate'=>['ELT',$xdate]])->delete();
        }
    }
    //定时移除过期的黑名单IP
    public function removeBlockip(){
        $condition = [];
        $condition['addtime'] = ['lt', (time()-86400*5)];
        M('block_ip')->where($condition)->delete();
    }
}
