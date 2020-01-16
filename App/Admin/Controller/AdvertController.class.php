<?php
/**
 * AdvertController.class.php
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
class AdvertController extends BaseController{
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'advert';
        $this->user = session('_shop_');
    }
    //
    protected function _before_index(){
        $thead = [
            'aid' => 'AID',
            'xname' => '推广名称',
            //'did' => '投放域名',
            'tname' => '二级目录',
            'url' => '推广网址',
            'tnid' => '推广模版',
            'tbid' => '审核模版',
            'toid' => '订单模板',
            'mid' => '员工',
            'ismo' => '页面统计',
        ];
        $this->assign('thead', $thead);
        $slave = $this->user['slave'];
        if($this->user['x'] && count($slave) == 1 && $slave[0] == $this->user['mid']){
            $this->assign('ms', false);
        }else{
            $this->assign('ms', true);
            $condition = [];
            if($this->user['x']){
                $condition['mid'] = ['in', $slave];
            }
            $condition['used'] = '1';
            $memArr = M('member')->where($condition)->field(['mid', 'nickname'])->select();
            $this->assign('memArr', $memArr);
        }
        if($this->user['x']){
            $this->assign('mid', $this->user['mid']);
        }
        $dmArr = M('domain')->where(['used'=>'1'])->field(['did','domain','xname'])->select();
        $this->assign('dmArr', $dmArr);
    }
    protected function _filter($map){
        if(!isset($map['_complex']['mid']) && $this->user['x']){
            $map['_complex']['mid'] = $this->user['mid'];
        }
        return $map;
    }
    //
    protected function _after_list($volist){
        if($volist){
            $sip = gethostbyname(I('server.SERVER_NAME', '', 'string'));
            $didArr = $tnidArr = $tbidArr = $midArr = [];
            foreach($volist as $row){
                $didArr[] = $row['did'];
                $tnidArr[] = $row['tnid'];
                $tbidArr[] = $row['tbid'];
                $toidArr[] = $row['toid'];
                $midArr[] = $row['mid'];
            }
            $Arr = [
                'domain' => ['map'=>['did'=>['IN', $didArr]],'field'=>['did','domain'],'var'=>'dmArr'],
                'tpl_normal' => ['map'=>['tnid'=>['IN', $tnidArr]],'field'=>['tnid','tname'],'var'=>'tnArr'],
                'tpl_black' => ['map'=>['tbid'=>['IN',$tbidArr]],'field'=>['tbid','tname'],'var'=>'tbArr'],
                'tpl_order' => ['map'=>['toid'=>['IN',$toidArr]],'field'=>['toid','tname'],'var'=>'toArr'],
                'member' => ['map'=>['mid'=>['IN',$midArr]],'field'=>['mid','nickname'],'var'=>'memArr'],
            ];
            $info = [];
            foreach($Arr as $key => $row){
                $tmp = M($key)->where($row['map'])->field($row['field'])->select();
                if($tmp)foreach($tmp as $xrow){
                    $info[$row['var']][$xrow[$row['field'][0]]] = $xrow[$row['field'][1]];
                }
            }
            foreach($volist as $key => $row){
                $volist[$key]['did'] = $info['dmArr'][$row['did']] ?? '';
                $volist[$key]['url'] = $volist[$key]['did'].'/'.$row['tname'];
                $volist[$key]['tnid'] = $info['tnArr'][$row['tnid']] ?? '';
                $volist[$key]['tbid'] = $info['tbArr'][$row['tbid']] ?? '';
                $volist[$key]['toid'] = $info['toArr'][$row['toid']] ?? '';
                $volist[$key]['mid'] = $info['memArr'][$row['mid']] ?? '';
                $volist[$key]['ismo'] = $row['ismo'] == '1' ? '是' : '否';
            }
        }
        return $volist;
    }
    //
    protected function _before_add(){
        $dArr = M('domain')->where(['used'=>'1'])->field(['did','domain','xname'])->select();
        $this->assign('dArr', $dArr);
        $cArr = M('category')->where(['lock'=>'0'])->field(['cid','cname'])->select();
        $this->assign('cArr', $cArr);
        $cidArr = [];
        if($cArr){
            foreach($cArr as $row){
                $cidArr[] = $row['cid'];
            }
        }
        $pArr = M('product')->where(['sale'=>'1',['cid'=>['in',$cidArr]]])->field(['pid','cid','pname'])->select();
        $this->assign('pArr', $pArr);
        $condition = [];
        if($this->user['x']){
            $condition['mid'] = ['in', $this->user['slave']];
            $this->assign('mid', $this->user['mid']);
        }
        $condition['used'] = '1';
        $mArr = M('member')->where($condition)->field(['mid','nickname'])->select();
        $this->assign('mArr', $mArr);
        $tnArr = M('tpl_normal')->where(['used'=>'1'])->field(['tnid','tname'])->select();
        $this->assign('tnArr', $tnArr);
        $baArr = M('black_area')->where(['used'=>'1'])->field(['baid','area'])->select();
        $this->assign('baArr', $baArr);
        $tbArr = M('tpl_black')->where(['used'=>'1'])->field(['tbid','tname'])->select();
        $this->assign('tbArr', $tbArr);
        $toArr = M('tpl_order')->where(['used'=>'1'])->field(['toid','tname'])->select();
        $this->assign('toArr', $toArr);
    }
    //
    protected function _before_insert($data){
        $data['tname'] = str_replace('/', '-', trim($data['tname']));
        $data['pids'] = serialize($data['pids']);
        $data['baids'] = serialize($data['baids']);
        $data['useragent'] = trim($data['useragent']);
        $data['addtime'] = time();
        return $data;
    }
    //
    protected function _before_edit(){
        $this->_before_add();
    }
    //
    protected function _filter_edit($list){
        $pids = unserialize($list['pids']);
        $this->assign('pids', $pids);
        $cid = M('product')->where(['pid'=>$pids[0]])->getField('cid');
        $this->assign('cid', $cid);
        $baids = unserialize($list['baids']);
        $this->assign('baids', $baids);
        return $list;
    }
    //
    protected function _before_save($data){
        $data['tname'] = str_replace('/', '-', trim($data['tname']));
        $data['pids'] = serialize($data['pids']);
        $data['baids'] = serialize($data['baids']);
        $data['useragent'] = trim($data['useragent']);
        return $data;
    }
    //
    protected function _after_add($id){
        $log = [];
        $row = M($this->tbname)->where(['aid'=>$id])->find();
        $log['happen'] = '新添加了一个二级目录为"'.$row['tname'].'"推广链接';
        $log['desc'] = $this->fmdata($row);
        $this->log($log);
    }
    //
    protected function _after_save($data){
        $tname = $data['tname'];
        S('advert_'.$tname, null);
        S('advert_x_'.$tname, null);
        $log = [];
        $log['happen'] = '修改了二级目录为"'.$tname.'"的推广链接';
        $log['desc'] = $this->fmdata($data);
        $this->log($log);
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['aid'] = array('in', I('get.ids'));
        }
        return $map;
    }
    protected function _filter_del($rows){
        if($rows){
            foreach($rows as $row){
                $log = [];
                $log['happen'] = '删除了二级目录为"'.$row['tname'].'"的推广链接';
                $log['desc'] = '';
                $this->log($log);
            }
        }
        return true;
    }
    //
    private function fmdata($data){
        if(!$data){
            return null;
        }
        $log = [];
        $log[] = '推广名称:'.$data['xname'];
        $log[] = '二级目录:'.$data['tname'];
        $domain = M('domain')->where(['did'=>$data['did']])->getField('domain');
        $log[] = '域名:'.$domain;
        $member = M('member')->where(['mid'=>$data['mid']])->getField('nickname');
        $log[] = '员工:'.$member;
        $pidArr = unserialize($data['pids']);
        if($pidArr){
            $tt = M('product')->where(['pid'=>['in',$pidArr]])->field(['pname','price'])->select();
            $pids = [];
            if($tt){
                foreach($tt as $row){
                    $pids[] = $row['pname'];
                }
            }
            $log[] = '商品:('.implode(',', $pids).')';
        }
        $tnname = M('tpl_normal')->where(['tnid'=>$data['tnid']])->getField('tname');
        $log[] = '推广模板:'.$tnname;
        $tbname = M('tpl_black')->where(['tbid'=>$data['tbid']])->getField('tname');
        $log[] = '审核模板:'.$tbname;
        $log[] = '订单模板:'.$data['toid'];
        $log[] = '公司名称:'.$data['company'];
        $log[] = '备案信息:'.$data['beian'];
        $log[] = '颜色:'.$data['color'];
        $log[] = '固话:'.$data['telno'];
        $log[] = '手机:'.$data['mobile'];
        $log[] = $data['deny'] ? '强制屏蔽' : '非强制屏蔽';
        $log[] = $data['defend'] ? '爬虫屏蔽' : '非爬虫屏蔽';
        $baidArr = unserialize($data['baids']);
        if($baidArr){
            $barea = [];
            $tt = M('black_area')->where(['baid'=>['in',$baidArr]])->field('area')->select();
            if($tt){
                foreach($tt as $row){
                    $barea[] = $row['area'];
                }
            }
            $log[] = '屏蔽地区:('.implode(',', $barea).')';
        }else{
            $log[] = '屏蔽地区:无';
        }
        $log[] = 'UA屏蔽:'.$data['useragent'];
        $log[] = '统计代码:'.$data['jscode'];
        $log[] = '禁止下单:'.($data['isuno'] ? '是' : '否');
        return implode(',', $log);
    }
}
