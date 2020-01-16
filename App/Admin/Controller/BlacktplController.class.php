<?php
/**
 * BlacktplController.class.php
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
class BlacktplController extends BaseController{
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'tpl_black';
    }
    //
    protected function _before_index(){
        $thead = [
            'tbid' => 'TBID',
            'tname' => '模版名称',
            'tbno' => '模版路径',
            'used' => '使用状态',
            'addtime' => '创建时间',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _after_list($volist){
        if($volist){
            foreach($volist as $key => $row){
                $volist[$key]['used'] = $row['used'] == '1' ? '在用' : '未用';
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
            }
        }
        return $volist;
    }
    //
    protected function _before_insert($data){
        $data['addtime'] = time();
        return $data;
    }
    //
    protected function _after_add($id){
        $log = [];
        $row = M($this->tbname)->where(['tbid'=>$id])->find();
        $log['happen'] = '添加了模板名为"'.$row['tname'].'的审核模板信息';
        $log['desc'] = $this->fmdata($row);
        $this->log($log);
    }
    //
    protected function _after_save($data){
        S('black_tpl_'.$data['tbid'], null);
        $log = [];
        $log['happen'] = '修改了模板名为"'.$data['tname'].'"的审核模板信息';
        $log['desc'] = $this->fmdata($data);
        $this->log($log);
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['tbid'] = array('in', I('get.ids'));
        }
        return $map;
    }
    //
    private function fmdata($data){
        if(!$data){
            return null;
        }
        $log = [];
        $log[] = '模板名称:'.$data['tname'];
        $log[] = '编号:'.$data['tbno'];
        $log[] = '状态:'.($data['used'] ? '可用' : '禁用');
        return implode(',', $log);
    }
}
