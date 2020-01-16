<?php
/**
 * EmptyController.class.php
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
use Home\Model\DenyModel;
use Home\Model\AdvertModel;
class EmptyController extends BaseController{
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'advert';
        $this->Advert = new AdvertModel($this->config);
        $this->Deny = new DenyModel($this->config);
    }
    //
    public function index(){
        $tname = I('server.PATH_INFO', '', 'trim,addslashes');
        $ip = $this->clientIP();
        //$ip = '113.109.55.92';//用于本地测试
        $is_deny = $this->Deny->isIP($ip, $this->config['base']['connect']);
        $skip = I('get.skip/d', 0);
        $advert = $this->Advert->getAdvert($tname);
        if($advert === false){
            $this->error('这个页面不存在');
        }
        $advert['price'] = (int)$advert['pids'][0]['price']; //价格
        $domain = $this->Advert->getDomain($advert['did']);
        $advert['jscode'] = $advert['jscode']."\r\n".$domain['jscode'];//追加域名统计代码
        if($advert['ismo'] == '1'){//统计页面访问情况
            $moid = $this->ttVisit($advert['aid'], $skip);//统计访问总数
            $js = '<script type="text/javascript">var acUrl = "'.U('Tongji/index').'",tjMoid = "'.$moid.'";</script>';
            $js .= "\r\n".'<script type="text/javascript" src="/Public/Lib/Js/tongji.js?3.0"></script>';
	    $advert['jscode'] .= "\r\n".$js;
        }
        $log = [];
        $log['tname'] = $tname;
        $log['ip'] = $ip;
        $deny = false;
        if($skip == 1){
            $deny = false;
        }elseif($advert['deny'] == '1' || $is_deny){
            $deny = true;
            $log['types'] = $advert['deny'] == '1' ? '1' : '8';
        }elseif($this->Deny->isDv($tname)){
            $deny = true;
            $log['types'] = '2';
        }elseif($advert['defend'] == '1' && $this->Deny->isAgent()){//是否禁止了客户端
            $deny = true;
            $log['types'] = '2';
        }elseif($advert['banpc'] == '1' && $this->Deny->isPC()){//屏蔽电脑端
            $deny = true;
            $log['types'] = '7';
        }elseif(trim($advert['useragent']) != '' && $this->Deny->isUA($advert['useragent'])){//屏蔽UA
            $log['types'] = '5';
            $deny = true;
        }elseif((isset($advert['black_area']) && $advert['black_area'])){//是否屏蔽了地区
            $ip_location = $this->ipLocation($ip);
            if(isset($ip_location['isp'])){
                $log['isp'] = $ip_location['isp'];
                unset($ip_location['isp']);
            }
            $log['types'] = '3';
            $log['city'] = implode(',', $ip_location);
            if($this->Deny->isBase($ip_location)){//是否屏蔽了基站
                $log['types'] = '4';
                $deny = true;
            }elseif($advert['defend'] == '1' && isset($log['isp']) && $this->Deny->isNet($log['isp'])){//拦截宽带
                $log['types'] = '6';
                $deny = true;
            }elseif($advert['black_area'] && $this->Deny->isArea($ip_location, $advert['black_area'])){//是否是屏蔽的地区
                $deny = true;
            }
        }
        $log['deny'] = $deny ? '1' : '0';
        $log['block'] = implode(',', $advert['black_area']);
        //$skip ? null : $this->agentLog($log);
        $skip ? $advert['mode'] = 0 : null;
        if($advert['mode'] == 1){
            $advert['skip'] = 0;
            if(!$deny && $skip != 1){
                $advert['skip'] = 1;
		$deny = true;
  	    }
        }elseif($advert['mode'] == 2){
            $advert['skip'] = 0;
            if(!$deny && $skip != 1){
                $advert['skip'] = 1;
            }
        }
        if($deny){
            $btpl = $this->Advert->getBlackTpl($advert['tbid']);
            if($btpl === false){
                $this->error('审核模板未添加到后台!');
            }
            $log['tpl'] = $btpl['tname'];
            $skip ? null : $this->agentLog($log);
            $advert['path'] = '/Tpl/Shield/'.$btpl['tbno'];
            $advert['domain'] = I('server.HTTP_HOST', '', 'trim');
            $this->assign('tpl', $advert);
            if(file_exists('./Tpl/Shield/'.$btpl['tbno'].'/default.html')){
                $this->display('Shield/'.$btpl['tbno'].'/default');
            }else{
                if(!file_exists('./Tpl/Shield/'.$btpl['tbno'].'/index.html')){
                    $this->error('审核模板文件未上传~~~');
                }
                $this->display('Shield/'.$btpl['tbno'].'/index');
            }
            exit;
        }
        $ntpl = $this->Advert->getNormalTpl($advert['tnid']);
        $log['tpl'] = $ntpl['tname'];
        $this->agentLog($log);
        if($ntpl === false){
            $this->error('推广模板未添加到后台');
        }
        $advert['path'] = '/Tpl/Standard/'.$ntpl['tnno']; //图片和js路径
        //$advert['yhcode'] = $this->createCode($advert['aid']);
        //$advert['wxwho'] = $this->Advert->wxPic($advert['aid']);
        $this->assign('tpl', $advert);
        if(!file_exists('./Tpl/Standard/'.$ntpl['tnno'].'/index.html')){
            $this->error('推广模板文件未上传~~~');
        }
        $this->display('Standard/'.$ntpl['tnno'].'/index');
    }
    //优惠代码
    protected function createCode($aid){
        $len = strlen($aid);
        switch($len){
            case 1:
                $code = '000'.$aid;break;
            case 2:
                $code = '00'.$aid;break;
            case 3:
                $code = '0'.$aid;break;
            default:
                $code = $aid;
        }
        return $code;
    }
    //记录客户端日志
    private function agentLog($log){
        $log['agent'] = I('server.HTTP_USER_AGENT', '', 'trim');
        $log['addtime'] = time();
        try{
            M('agent_log')->data($log)->add();
        }catch(\Exception $e){}
    }
    //统计访问次数
    protected function ttVisit($aid, $skip){
        if(is_numeric($aid)){
            try{
                $model = M('monitor');
                $row = $model->where(['aid'=>$aid, 'xdate'=>date('Y-m-d')])->field('moid')->find();
                if(!$row){
                    $row['moid'] = $model->data(['xdate'=>date('Y-m-d'), 'aid'=>$aid])->add();
                    if(!$row['moid']){
                        return false;
                    }
                }
                $skip ? null : $model->where(['moid'=>$row['moid']])->setInc('top');
                return $row['moid'];
            }catch(\Exception $e){}
        }
        return false;
    }
}
