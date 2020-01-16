<?php
/**
 * OrderController.class.php
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
class OrderController extends BaseController{
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'order';
        $this->config = $this->getInit();
        $this->user = session('_shop_');
    }
    //
    protected function _before_index(){
        $thead = [
            'oid' => 'OID',
            'aid' => '推广名称',
            'pinfo' => '商品名称',
            'cname' => '姓名',
            'telno' => '手机',
            'address' => '收货地址',
            'qq' => 'QQ/微信',
            'desc' => '留言',
            'pay_method' => '支付方式',
            'addtime' => '下单时间',
            'mid' => '员工',
            'link' => '来路',
            'ip' =>'客户IP'
        ];
        $this->assign('thead', $thead);
        $width = [
            'oid' => '50',
            'aid' => '60',
            'pinfo' => '130',
            'cname' => '50',
            'telno' => '80',
            'address' => '130',
            'qq' => '60',
            'desc' => '130',
            'pay_method' => '55',
            'addtime' => '90',
            'mid' => '50',
            'link' => '150',
            'ip' =>'80'
        ];
        $this->assign('width', $width);
        if($this->user['x'] && count($this->user['slave']) == 1 && $this->user['slave'][0] == $this->user['mid']){
            $this->assign('ms', false);
        }else{
            $this->assign('ms', true);
            $condition = [];
            if($this->user['x']){
                $condition['mid'] = ['in', $this->user['slave']];
            }
            $condition['used'] = '1';
            $mArr = M('member')->where($condition)->field(['mid','nickname'])->order(['mid'=>'asc'])->select();
            $this->assign('mArr', $mArr);
        }
        if($this->user['x']){
            $this->assign('mid', $this->user['mid']);
        }
        if(I('get.tt/d', 0) == 1){
            usleep(300000);
        }
    }
    //
    protected function _after_list($volist){
        if($volist){
            $aidArr = $midArr = [];
            foreach($volist as $row){
                $aidArr[] = $row['aid'];
                $midArr[] = $row['mid'];
            }
            $Arr = [
                'advert' => ['map'=>['aid'=>['IN',$aidArr]],'field'=>['aid','xname'],'var'=>'xnArr'],
                'member' => ['map'=>['mid'=>['IN',$midArr]],'field'=>['mid','nickname'],'var'=>'nnArr'],
            ];
            $info = [];
            foreach($Arr as $key => $row){
                $tmp = M($key)->where($row['map'])->field($row['field'])->select();
                if($tmp)foreach($tmp as $xrow){
                    $info[$row['var']][$xrow[$row['field'][0]]] = $xrow[$row['field'][1]];
                }
            }
            foreach($volist as $key => $row){
                $volist[$key]['aid'] = $info['xnArr'][$row['aid']] ?? '';
                $volist[$key]['mid'] = $info['nnArr'][$row['mid']] ?? '';
                $volist[$key]['pay_method'] = $row['pay_method'] == '1' ? '货到付款' : ($row['pay_method'] == '4' ? '微信支付' : '支付宝');
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
                if($this->config['plan']['order_md5'] == '1' && !authcheck('Order_md5')){
                    $volist[$key]['telno'] = mb_substr($row['telno'], 0, 3).'****'.mb_substr($row['telno'], 7, 4);
                    !authcheck('Order_qq') ? $volist[$key]['qq'] = $row['qq'] ? mb_substr($row['qq'], 0, 3).'***' : '' : null;
                }
                $volist[$key]['link'] = strlen($row['link']) > 50 ? mb_substr($row['link'], 0, 50).'...[<a href="'.$row['link'].'" target="_blank" title="'.$row['link'].'">more</a>]' : $row['link'];
            }
        }
        return $volist;
    }
    //
    protected function _filter($map){
        if(isset($map['_complex']['aid']) && $map['_complex']['aid']){
            $aid = str_replace('%', '', $map['_complex']['aid'][1]);
            $tmp = M('advert')->where(['xname'=>['like', '%'.$aid.'%']])->field(['aid'])->select();
            if($tmp){
                $aids = [];
                foreach($tmp as $row){
                    $aids[] = $row['aid'];
                }
                $map['_complex']['aid'] = ['in',$aids];
            }else{
                $map['_complex']['aid'] = '';
            }
        }
        if(isset($map['_complex']['addtime']) && $map['_complex']['addtime']){
            $date = str_replace('%', '', $map['_complex']['addtime'][1]);
            $start = strtotime($date.' 00:00:00');
            $end = strtotime($date.' 23:59:59');
            $xend = strtotime('2017-03-01');
            if($start < $xend){
                $start = $xend;
            }
            $map['_complex']['addtime'] = [['egt',$start],['elt',$end]];
        }else{
            $map['_complex']['addtime'] = ['gt', strtotime('2017-03-01')];
        }
        $keys = I('post.keys', '');
        if(isset($keys['filter']) && $keys['filter'] && !$map['_complex']['cname']){
            $cx = trim($this->config['base']['order_filter']);
            if($cx != ''){
                $xarr = explode(',', $cx);
                $con = [];
                foreach($xarr as $key => $row){
                    if(trim($row) == ''){
                        continue;
                    }
                    $con[] = ['NEQ', $row];
                }
                if($con){
                    $map['_complex']['cname'] = $con;
                }
            }
        }
        if(!isset($map['_complex']['mid']) && $this->user['x']){
            $map['_complex']['mid'] = $this->user['mid'];
        }
        return $map;
    }
    //
    protected function _before_del(){
        $map = [];
        if(I('get.ids') != ''){
            $map['oid'] = ['in', I('get.ids', [0])];
        }
        return $map;
    }
}
