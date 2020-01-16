<?php
/**
 * CateController.class.php
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

class CateController extends BaseController{
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'category';
    }
    //
    protected function _before_index(){
        $thead = [
            'cid' => 'CID',
            'cname' => '分类名称',
            'top' => '顶级分类',
            'tmid' => '短信模版',
            'lock' => '是否可用',
            'desc' => '备注信息',
            'addtime' => '创建时间',
        ];
        $this->assign('thead', $thead);
        $topArr = M($this->tbname)->where(['top'=>'1'])->field(['cid', 'cname'])->select();
        $this->assign('topArr', $topArr);
    }
    //
    protected function _after_list($volist){
        if($volist){
            $Mt = M('tpl_msg');
            foreach($volist as $key => $row){
                $volist[$key]['tmid'] = $Mt->where(['tmid'=>$row['tmid']])->getField('title');
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
                $volist[$key]['top'] = $row['top'] ? '是' : '否';
                $volist[$key]['lock'] = $row['lock'] ? '否' : '是';
            }
        }
        return $volist;
    }
    //
    protected function _before_add(){
        $tmArr = M('tpl_msg')->where(['used'=>'1'])->field(['tmid','title'])->select();
        $this->assign('tmArr', $tmArr);
    }
    //
    protected function _before_insert($data){
        $data['addtime'] = $data['uptime'] = time();
        return $data;
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['cid'] = array('in', I('get.ids'));
        }
        return $map;
    }
    //
    protected function _before_edit(){
        $this->_before_add();
    }
    //
    protected function _before_save($data){
        $data['uptime'] = time();
        return $data;
    }
    //
    protected function _after_add($id){
        $log = [];
        $row = M($this->tbname)->where(['cid'=>$id])->find();
        $log['happen'] = '添加了名称为"'.$row['cname'].'的商品分类信息';
        $log['desc'] = $this->fmdata($row);
        $this->log($log);
    }
    //
    protected function _after_save($data){
        $log = [];
        $log['happen'] = '修改了名称为"'.$data['cname'].'"的商品分类信息';
        $log['desc'] = $this->fmdata($data);
        $this->log($log);
    }
    //
    private function fmdata($data){
        if(!$data){
            return null;
        }
        $log = [];
        $log[] = '分类名称:'.$data['cname'];
        $tmname = M('tpl_msg')->where(['tmid'=>$data['tmid']])->getField('title');
        $log[] = '短信模板:'.$tmname;
        $log[] = '状态:'.($data['lock'] ? '冻结' : '可用');
        $log[] = '描述:'.$data['desc'];
        return implode(',', $log);
    }
}
