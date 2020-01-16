<?php
/**
 * AdvertModel.class.php
 * 风行者广告推广系统
 * Copy right 2020-2030 风行者 保留所有权利。
 * 官方网址: https://fxz.nixi.win/
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * @author John Doe <john.doe@example.com>
 * @date 2020-01-20
 * @version v2.0.22
 */
namespace Home\Model;

class AdvertModel{
    protected $config;
    //
    function __construct($config){
        $this->config = $config;
    }
    //获取广告信息
    public function getAdvert($tname){
        $tmp = S('advert_'.$tname);
        if($tmp === false){
            $tmp = M('advert')->where(['tname'=>$tname])->find();
            if(!$tmp){
                return false;//页面不存在
            }
            $tmp['pids'] = unserialize($tmp['pids']);
            $tmp['beian'] = htmlspecialchars_decode($tmp['beian']);
            if($tmp['color']){
                $tmp['beian'] = '<div style="color:'.$tmp['color'].'">'.$tmp['beian'].'</div>';
            }
            $tmp['baids'] = unserialize($tmp['baids']);
            $tmp['jscode'] = htmlspecialchars_decode($tmp['jscode']);
            if($tmp['baids']){
                $tmp['black_area'] = [];
                $tt = M('black_area')->where(['baid'=>['in',$tmp['baids']]])->field('area')->select();
                if($tt){
                    foreach($tt as $row){
                        $tmp['black_area'][] = $row['area'];
                    }
                }
            }
            if($tmp['pids']){
                $tt = M('product')->where(['pid'=>['in',$tmp['pids']]])->field(['pid','pname','price','oprice'])->order('price asc')->select();
                $tmp['pids'] = [];
                if($tt){
                    foreach($tt as $row){
                        $tmp['pids'][] = $row;
                    }
                }
            }
            if(trim($tmp['link']) != ''){
                if(strpos($tmp['link'], 'http://') === false && strpos($tmp['link'], 'https://') === false){
                    $tmp['link'] = 'http://'.$tmp['link'];
                }
            }
            if($this->config['plan']['web_cache'] == '1'){
                S('advert_'.$tname, $tmp, ['expire'=>$this->config['base']['cache_time']]);
            }
        }
        return $tmp;
    }
    //获取域名信息
    public function getDomain($did){
        $tmp = S('domain_'.$did);
        if($tmp === false){
            $tmp = M('domain')->where(['did'=>$did])->find();
            $tmp['jscode'] = htmlspecialchars_decode($tmp['jscode']);
            if($this->config['plan']['web_cache'] == '1'){
                S('domain_'.$did, $tmp, ['expire'=>$this->config['base']['cache_time']]);
            }
        }
        return $tmp;
    }
    //获取审核模版信息
    public function getBlackTpl($tbid){
        $tmp = S('black_tpl_'.$tbid);
        if($tmp === false){
            $tmp = M('tpl_black')->where(['tbid'=>$tbid])->find();
            if(!$tmp){
                return false;//屏蔽模板不存在
            }
            if($this->config['plan']['web_cache'] == '1'){
                S('black_tpl_'.$tbid, $tmp, ['expire'=>$this->config['base']['cache_time']]);
            }
        }
        return $tmp;
    }
    //获取正常页面模版
    public function getNormalTpl($tnid){
        $tmp = S('normal_tpl_'.$tnid);
        if($tmp === false){
            $tmp = M('tpl_normal')->where(['tnid'=>$tnid])->find();
            if(!$tmp){
                return false;//正常模板不存在!
            }
            if($this->config['plan']['web_cache'] == '1'){
                S('normal_tpl_'.$tnid, $tmp, ['expire'=>$this->config['base']['cache_time']]);
            }
        }
        return $tmp;
    }
    //计数器
    public function wxPic($aid){
        $tmp = S('wx_c_'.$aid);
        $sArr = [1=>10,2=>10,3=>10,4=>10,5=>10];
        if($tmp === false){
            $tmp = ['who'=>2,'sum'=>0];
        }
        $tmp['sum']++;
        if($tmp['sum'] > $sArr[$tmp['who']]){
            $tmp['who']++;
            $tmp['sum'] = 0;
        }
        if($tmp['who'] > 5){
            $tmp['who'] = 1;
        }
        S('wx_c_'.$aid, $tmp);
        return $tmp['who'];
    }
}