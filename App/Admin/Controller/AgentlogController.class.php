<?php
/**
 * AgentlogController.class.php
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
class AgentlogController extends BaseController{
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'agent_log';
    }
    //
    protected function _before_index(){
        $thead = [
            'alid' => 'ALID',
            'tname' =>'二级目录',
            'ip' => '客户IP地址',
            'city' => '客户IP所在地区',
            'block' => '页面屏蔽的地区',
            'deny' => '客户看到的模板',
            'types' => '页面屏蔽规则',
            'addtime' =>'客户访问时间',
            'agent' => 'Agent',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _after_list($volist){
        if($volist){
            foreach($volist as $key => $row){
                $volist[$key]['deny'] = $row['deny'] == '1' ? '<span style="color:red">审核模板</span>' : '<span style="color:green">推广模板</span>';
                $row['tpl'] ? $volist[$key]['deny'] .= ' ('.$row['tpl'].')' : null;
                $volist[$key]['types'] = $this->xtype($row['types']);
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
                authcheck('Huo_login') && $row['isp'] ? $volist[$key]['agent'] = $row['isp'].' '.$row['agent'] : null;
            }
        }
        return $volist;
    }
    //
    protected function _filter($map){
        $keys = I('post.keys', '');
        if((isset($keys['from']) && $keys['from']) || (isset($keys['to']) && $keys['to'])){
            $condition = [];
            if($keys['from']){
                $condition[] = is_numeric($keys['from']) ? ['egt', $keys['from']] : ['egt', strtotime($keys['from'])];
            }
            if($keys['to']){
                $condition[] = is_numeric($keys['to']) ? ['elt', $keys['to']] : ['elt', strtotime($keys['to'])];
            }
            if($condition){
                $map['_complex']['addtime'] = $condition;
            }
        }
        return $map;
    }
    //
    private function xtype($type){
        $res = '';
        switch($type){
            case '1' :
                $res = '强制屏蔽';
                break;
            case '2' :
                $res = '爬虫屏蔽';
                break;
            case '3':
                $res = '根据IP归属地屏蔽';
                break;
            case '4':
                $res = '基站IP屏蔽';
                break;
            case '5':
                $res = 'UA屏蔽';
                break;
            case '6':
                $res = '爬虫1屏蔽';
                break;
            case '7':
                $res = '电脑端屏蔽';
                break;
            case '8':
                $res = 'IP黑名单屏蔽';
                break;
            default :
                $res = '未设置屏蔽';
                break;
        }
        return $res;
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['alid'] = ['in', I('get.ids')];
        }
        return $map;
    }
}
