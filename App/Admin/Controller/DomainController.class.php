<?php
/**
 * DomainController.class.php
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
class DomainController extends BaseController{
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'domain';
    }
    //
    protected function _before_index(){
        $thead = [
            'did' => 'DID',
            'xname' => '推广编号',
            'domain' => '域名',
            'tdid' => '域名模版',
            'beian' => '备案信息',
            'telno' => '公司电话',
            'company' => '公司名称',
            'used' => '是否在用',
            'addtime' => '创建时间',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _after_list($volist){
        if($volist){
            $dtpl = M('tpl_domain');
            foreach($volist as $key => $row){
                $volist[$key]['tdid'] = $dtpl->where(['tdid'=>$row['tdid']])->getField('tname');
                $volist[$key]['used'] = $row['used'] == 0 ? '否' : '是';
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
            }
        }
        return $volist;
    }
    //
    protected function _before_add(){
        $tplArr = M('tpl_domain')->field(['tdid','tname'])->select();
        $this->assign('tplArr', $tplArr);
    }
    //
    protected function _before_insert($data){
        $data['domain'] = trim($data['domain']);
        if(strpos($data['domain'], 'wap.') !== false){
            $data['used'] = '0';
        }
        $data['addtime'] = time();
        return $data;
    }
    //
    protected function _before_save($data){
        $data['domain'] = trim($data['domain']);
        if(strpos($data['domain'], 'wap.') !== false){
            $data['used'] = '0';
        }
        return $data;
    }
    //
    protected function _before_edit(){
        $tplArr = M('tpl_domain')->field(['tdid','tname'])->select();
        $this->assign('tplArr', $tplArr);
    }
    //
    protected function _filter_edit($list){
        return $list;
    }
    //
    protected function _after_add($id){
        $log = [];
        $row = M($this->tbname)->where(['did'=>$id])->find();
        $log['happen'] = '添加了名称为"'.$row['domain'].'的域名信息';
        $log['desc'] = $this->fmdata($row);
        $this->log($log);
        S('domain_'.$row['domain'], null);
    }
    //
    protected function _after_save($data){
        $domain = trim($data['domain']);
        if(substr($domain, 0, 4) === 'www.'){
            $domain = substr($domain, 4);
        }
        S('domain_'.$domain, null);
        S('domain_'.$data['did'], null);
        $log = [];
        $log['happen'] = '修改了名称为"'.$data['domain'].'"的域名信息';
        $log['desc'] = $this->fmdata($data);
        $this->log($log);
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['did'] = array('in', I('get.ids'));
        }
        return $map;
    }
    //
    private function fmdata($data){
        if(!$data){
            return null;
        }
        $log = [];
        $log[] = '域名:'.$data['domain'];
        $log[] = '编号:'.$data['xname'];
        $tdname = M('tpl_domain')->where(['tdid'=>$data['tdid']])->getField('tname');
        $log[] = '域名模板:'.$tdname.'('.$data['tdid'].')';
        $log[] = '备案信息:'.$data['beian'];
        $log[] = '公司:'.$data['company'];
        $log[] = '地址:'.$data['address'];
        $log[] = '电话:'.$data['telno'];
        return implode(',', $log);
    }
}