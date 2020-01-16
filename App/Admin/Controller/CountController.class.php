<?php
/**
 * CountController.class.php
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
class CountController extends BaseController {
    //
    public function _initialize() {
        $this->unAuth = ['index','xdata','ip','mcount','olocation'];
        parent::_initialize();
        $this->tbname = '';
        $this->config = $this->getInit();
    }
    //
    public function index(){
        $xdate = I('post.xdate', date('Y-m-d', strtotime('-7 days')), 'trim,strip_tags');
        $ydate = I('post.ydate', date('Y-m-d'), 'trim,strip_tags');
        $this->assign('xdate', $xdate);
        $this->assign('ydate', $ydate);
        $this->display();
    }
    //
    public function xdata($xdate, $ydate){
        $xdate = I('get.xdate', date('Y-m-d'), 'trim,strip_tags');
        $ydate = I('get.ydate', date('Y-m-d'), 'trim,strip_tags');
        $data = [];
        $tmp = M('monitor')->where(['xdate'=>[['egt',$xdate],['elt',$ydate]]])->group('xdate')->field(['xdate','Sum(top) tt'])->limit(31)->select();
        if($tmp){
            $Order = M('order');
            $cx = trim($this->config['base']['order_filter']);
            $con = [];
            if($cx != ''){
                $xarr = explode(',', $cx);
                foreach($xarr as $key => $row){
                    if(trim($row) == ''){
                        continue;
                    }
                    $con[] = ['NEQ', $row];
                }
            }
            $dateArr = [];
            foreach($tmp as $row){
                $dateArr[] = $row['xdate'];
            }
            $client = http_client();
            if($client){
                $host = I('server.HTTP_HOST');
                $group = 'x';
                //$list = $client->totalVisit($dateArr, $group);
            }
            foreach($tmp as $key => $row){
                $from = strtotime($row['xdate'].' 00:00:00');
                $to = strtotime($row['xdate'].' 23:59:59');
                $map['cname'] = $con;
                $map['addtime'] = [['egt',$from],['elt',$to]];
                $tmp[$key]['ot'] = $Order->where($map)->count('distinct(telno)');
                $tmp[$key]['tt'] = $list[$row['xdate']] ?? $row['tt'];
            }
            //
            $data['tooltip'] = ['trigger'=>'axis'];
            $data['calculable'] = 1;
            $data['legend'] = ['data'=>['访问总量','订单总量']];
            $data['yAxis'][0] = ['type'=>'value','spliArea'=>['show'=>1]];
            $data['xAxis'][0] = ['type'=>'category','data'=>[]];
            $arr = [];
            foreach($tmp as $key => $row){
                $data['xAxis'][0]['data'][] = $row['xdate'];
                $arr['tt'][] = $row['tt'];
                $arr['ot'][] = $row['ot'];
            }
            $data['series'][] = ['name'=>'访问总量','type'=>'bar','data'=>$arr['tt']];
            $data['series'][] = ['name'=>'订单总量','type'=>'bar','data'=>$arr['ot']];
        }
        $this->ajaxReturn($data);
    }
    //
    public function ip(){
        $date = I('post.date', date('Y-m-d'), 'trim,strip_tags');
        $from = strtotime($date.' 00:00:00');
        $to = strtotime($date.' 23:59:59');
        $cx = trim($this->config['base']['order_filter']);
        $con = [];
        if($cx != ''){
            $xarr = explode(',', $cx);
            foreach($xarr as $key => $row){
                if(trim($row) == ''){
                    continue;
                }
                $con[] = ['NEQ', $row];
            }
        }
        $map = [];
        $map['cname'] = $con;
        $map['addtime'] = [['egt', $from], ['elt', $to]];
        $list = M('order')->where($map)->field(['count(oid)'=>'tt', 'ip', 'oid'])->group('ip')->having('tt>2')->order(['oid'=>'asc'])->select();
        $this->assign('list', $list);
        $thead = ['oid'=>'订单ID', 'ip'=>'IP地址', 'tt'=>'下单次数'];
        $this->assign('thead', $thead);
        $this->assign('date', $date);
        $this->display();
    }
    //
    public function mcount(){
        $key = I('post.key', []);
        if(!$key){
            $key['date'] = I('get.date', date('Y-m-d'));
            $key['time'] = I('get.time', 240);
        }
        $mTmp = M('report_member')->where(['used'=>'1'])->field(['rmid','nickname'])->select();
        $mArr = [];
        if($mTmp){
            foreach($mTmp as $row){
                $mArr[$row['rmid']] = $row['nickname'];
            }
        }
        $planArr = M('report_plan')->where(['used'=>'1','date'=>$key['date']])->field(['rpid','rcid','rmid'])->select();
        $rpidArr = [];
        if($planArr){
            foreach($planArr as $row){
                $rpidArr[] = $row['rpid'];
                $rmidArr[] = $row['rmid'];
            }
            $dTmp = M('report_data')->where(['time'=>$key['time'],'rpid'=>['IN', $rpidArr]])->select();
            $dArr = [];
            if($dTmp){
                foreach($dTmp as $row){
                    $dArr[$row['rpid']] = $row;
                }
            }
            $cArr = [];
            if($key['time'] == '180'){
                $cTmp = M('report_data')->where(['time'=>'18','rpid'=>['IN', $rpidArr]])->select();
                if($cTmp){
                    foreach($cTmp as $row){
                        $cArr[$row['rpid']] = $row;
                    }
                }
            }
            $start = date('Y-m', strtotime($key['date'])).'-1';
            $end = date('Y-m-d', strtotime('-1 day', strtotime($key['date'])));
            $mpTmp = M('report_plan')->where(['rmid'=>['IN',$rmidArr],'date'=>['between',[$start, $end]]])->field(['rpid','rmid'])->select();
            $xrpidArr = $xrmArr = $xttArr = [];
            if($mpTmp){
                foreach($mpTmp as $row){
                    $xrpidArr[] = $row['rpid'];
                    $xrmArr[$row['rpid']] = $row['rmid'];
                }
                $xdTmp = M('report_data')->where(['time'=>'240','rpid'=>['IN',$xrpidArr]])->select();
                if($xdTmp){
                    $txdTmp = [];
                    foreach($xdTmp as $row){
                        if(isset($txdTmp[$row['rpid']])){
                            $txdTmp[$row['rpid']]['time'] == '18' ? $txdTmp[$row['rpid']] = $row : null;
                        }else{
                            $txdTmp[$row['rpid']] = $row;
                        }
                    }
                    $xdTmp = $txdTmp;
                    unset($txdTmp);
                    foreach($xdTmp as $row){
                        isset($xttArr[$xrmArr[$row['rpid']]]) ? $xttArr[$xrmArr[$row['rpid']]] += $row['spend'] : $xttArr[$xrmArr[$row['rpid']]] = $row['spend'];
                    }
                }
            }
        }
        $list = $xlist = [];
        if($planArr)foreach($planArr as $row){
            !isset($dArr[$row['rpid']]) ? $dArr[$row['rpid']] = ['spend'=>0,'order'=>0,'cost'=>0] : null;
            !isset($list[$row['rmid']]['name']) ? $list[$row['rmid']]['name'] = $mArr[$row['rmid']] : null;
            !isset($list[$row['rmid']]['stt']) ? $list[$row['rmid']]['stt'] = $dArr[$row['rpid']]['spend'] : $list[$row['rmid']]['stt'] += $dArr[$row['rpid']]['spend'];
            !isset($list[$row['rmid']]['ott']) ? $list[$row['rmid']]['ott'] = $dArr[$row['rpid']]['order'] : $list[$row['rmid']]['ott'] += $dArr[$row['rpid']]['order'];
            if($key['time'] == '180'){
                !isset($xlist[$row['rmid']]['stt']) ? $xlist[$row['rmid']]['stt'] = $cArr[$row['rpid']]['spend'] : $xlist[$row['rmid']]['stt'] += $cArr[$row['rpid']]['spend'];
            }
        }
        foreach($list as $k => $row){
            $list[$k]['ctt'] = (int)($row['stt']/$row['ott']);
            $list[$k]['mtt'] = $key['time'] == '180' ? $xttArr[$k] + $xlist[$k]['stt'] + $row['stt'] : $xttArr[$k] + $row['stt'];
        }
        ksort($list);
        $this->assign('list', $list);
        $this->assign('key', $key);
        $this->display();
    }
}
