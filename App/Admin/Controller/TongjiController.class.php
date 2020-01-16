<?php
/**
 * TongjiController.class.php
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
class TongjiController extends BaseController {
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'monitor';
    }
    //
    protected function _before_index(){
        $thead = [
            'moid' => 'MOID',
            'aid' => '推广名称',
            'link' => '网页地址',
            'top' => '总访问量',
            //'small' => '25%位置',
            'middle' => '中部访问量',
            //'large' => '75%位置',
            'bottom' => '底部访问量',
            'xdate' => '统计时间',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _after_list($volist){
        if($volist){
            $advert = M('advert');
            $domain = M('domain');
            foreach($volist as $key => $row){
                $arr = $advert->where(['aid'=>$row['aid']])->field(['xname','tname','did'])->find();
                $dm = $domain->where(['did'=>$arr['did']])->getField('domain');
                $volist[$key]['aid'] = $arr['xname'];
                $volist[$key]['link'] = $dm.'/'.$arr['tname'];
                //$volist[$key]['small'] = $row['small'].' ('.(round($row['small']/$row['top'], 2) * 100).'%)';
                $volist[$key]['middle'] = $row['middle'].' ('.(round($row['middle']/$row['top'], 2) * 100).'%)';
                //$volist[$key]['large'] = $row['large'].' ('.(round($row['large']/$row['top'], 2) * 100).'%)';
                $volist[$key]['bottom'] = $row['bottom'].' ('.(round($row['bottom']/$row['top'], 2) * 100).'%)';
                if($row['small'] != 0){
                    $volist[$key]['top'] = $row['top'].' / '.$row['small'];
                }
            }
        }
        return $volist;
    }
    //
    protected function _filter($map){//die(print_r($map));
        if((isset($map['_complex']['aid']) && $map['_complex']['aid']) || (isset($map['_complex']['top']) && $map['_complex']['top']) || $map['_complex']['bottom']){
            $map1 = [];
            $aid = str_replace('%', '', $map['_complex']['aid'][1]);
            unset($map['_complex']['aid']);
            $aid ? $map1['xname'] = ['like', '%'.$aid.'%'] : null;
            $top = str_replace('%', '', $map['_complex']['top'][1]);
            unset($map['_complex']['top']);
            $top ? $map1['tname'] = ['like', '%'.$top.'%'] : null;
            $domain = str_replace('%', '', $map['_complex']['bottom'][1]);
            unset($map['_complex']['bottom']);
            $did = M('domain')->where(['domain'=>$domain])->getField('did');
            $did ? $map1['did'] = $did : null;
            $tmp = $map1 ? M('advert')->where($map1)->field(['aid'])->select() : null;
            if($tmp){
                $aids = [];
                foreach($tmp as $row){
                    $aids[] = $row['aid'];
                }
                $map['_complex']['aid'] = ['in',$aids];
            }else{
                $map['_complex']['aid'] = '';
            }
        }//die(print_r($map1));
        return $map;
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['moid'] = array('in', I('get.ids'));
        }
        return $map;
    }
}
