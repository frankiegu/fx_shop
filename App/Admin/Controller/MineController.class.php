<?php
/**
 * MineController.class.php
 * 风行者广告推广系统
 * Copy right 2020-2030 风行者 保留所有权利。
 * 官方网址: https://fxz.nixi.win/
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * @author John Doe <john.doe@example.com>
 * @date 2020-01-20
 * @version v2.0.22
 */
namespace Admin\Controller;
use Think\Controller;
/**
 * 我的主页
 * @author Administrator
 */
class MineController extends Controller{
    //put your code here
    public function _initialize(){
        $this->user = session('_shop_');
    }
    //
    public function index(){
        $this->display();
    }
    //
    public function moni(){
        $condition = [];
        $condition['tb1.used'] = '1';
        $condition['tb2.xdate'] = date('Y-m-d');
        if($this->user['x']){
            $condition['tb1.mid'] = ['in', $this->user['slave']];
        }
        $tmp = M()->table('__ADVERT__ tb1')->join('__MONITOR__ tb2 on tb2.aid=tb1.aid')->where($condition)
                ->field(['tb1.aid','tb1.xname','tb2.moid','tb2.top','tb2.middle','tb2.bottom'])
                ->order('tb2.top desc')->limit(20)->select();
        $data = [];
        $data['tooltip'] = ['trigger'=>'axis'];
        $data['calculable'] = 1;
        $data['legend'] = ['data'=>['总','中','底']];
        $data['yAxis'][0] = ['type'=>'value','spliArea'=>['show'=>1]];
        $data['xAxis'][0] = ['type'=>'category','data'=>[]];
        if($tmp){
            $arr = [];
            foreach($tmp as $key => $row){
                $data['xAxis'][0]['data'][] = $row['xname'];
                $arr['top'][] = $row['top'];
                $arr['middle'][] = $row['middle'];
                $arr['bottom'][] = $row['bottom'];
            }
            $data['series'][] = ['name'=>'总','type'=>'bar','data'=>$arr['top']];
            $data['series'][] = ['name'=>'中','type'=>'bar','data'=>$arr['middle']];
            $data['series'][] = ['name'=>'底','type'=>'bar','data'=>$arr['bottom']];
        }
        $this->ajaxReturn($data);
    }
    //
    public function order(){
        $condition = [];
        $condition['o.addtime'] = ['EGT', strtotime(date('Y-m-d'))];
        $tmp = M()->table('__ORDER__ o')->join('__ADVERT__ a on a.aid=o.aid')
                    ->where($condition)->field(['a.xname','count(o.oid)'=>'tt','count(distinct(o.telno))'=>'dt'])
                    ->group('o.aid')->order('tt desc')->limit(20)->select();
        $data = [];
        $data['tooltip'] = ['trigger'=>'axis'];
        $data['calculable'] = 1;
        $data['legend'] = ['data'=>['全部订单','有效订单']];
        $data['yAxis'][0] = ['type'=>'value','spliArea'=>['show'=>1]];
        $data['xAxis'][0] = ['type'=>'category','data'=>[]];
        if($tmp){
            $arr = [];
            foreach($tmp as $key => $row){
                $data['xAxis'][0]['data'][] = $row['xname'];
                $arr['tt'][] = $row['tt'];
                $arr['dt'][] = $row['dt'];
            }
            $data['series'][] = ['name'=>'全部订单','type'=>'bar','data'=>$arr['tt']];
            $data['series'][] = ['name'=>'有效订单','type'=>'bar','data'=>$arr['dt']];
        }
        $this->ajaxReturn($data);
    }
}
