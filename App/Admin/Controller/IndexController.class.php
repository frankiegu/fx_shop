<?php
/**
 * IndexController.class.php
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

class IndexController extends BaseController {
    //
    public function index(){
        layout(false);
        $menu = $this->menu();
        $this->assign('menu', $menu);
        $this->display();
    }
    //
    private function menu(){
        $menu = [];
        $c['name'] = '推广相关';
        //投放信息
        $c['tree'][0]['name'] = '推广信息';
        if(authcheck('Admin/Advert/index')){
            $c['tree'][0]['tree'][0]['name'] = '推广计划';
            $c['tree'][0]['tree'][0]['url'] = U('Advert/index');
        }
        if(authcheck('Admin/Report/index')){
            $c['tree'][0]['tree'][1]['name'] = '推广报数';
            $c['tree'][0]['tree'][1]['url'] = U('Report/index');
        }
        if(authcheck('Admin/Domain/index')){
            $c['tree'][0]['tree'][2]['name'] = '推广域名';
            $c['tree'][0]['tree'][2]['url'] = U('Domain/index');
        }
        if(authcheck('Admin/Tongji/index')){
            $c['tree'][0]['tree'][3]['name'] = '访问统计';
            $c['tree'][0]['tree'][3]['url'] = U('Tongji/index');
        }
        //屏蔽管理
        $c['tree'][2]['name'] = '屏蔽防御';
        if(authcheck('Admin/Blackip/index')){
            $c['tree'][2]['tree'][0]['name'] = 'IPV4屏蔽';
            $c['tree'][2]['tree'][0]['url'] = U('Blackip/index');
        }
        if(authcheck('Admin/Blackarea/index')){
            $c['tree'][2]['tree'][1]['name'] = '地区屏蔽';
            $c['tree'][2]['tree'][1]['url'] = U('Blackarea/index');
        }
        if(authcheck('Admin/Blockip/index')){
            $c['tree'][2]['tree'][2]['name'] = '下单屏蔽';
            $c['tree'][2]['tree'][2]['url'] = U('Blockip/index');
        }
        $c['tree'][3]['name'] = '模版相关';
        if(authcheck('Admin/Normaltpl/index')){
            $c['tree'][3]['tree'][0]['name'] = '推广模版';
            $c['tree'][3]['tree'][0]['url'] = U('Normaltpl/index');
        }
        if(authcheck('Admin/Domaintpl/index')){
            $c['tree'][3]['tree'][1]['name'] = '域名模版';
            $c['tree'][3]['tree'][1]['url'] = U('Domaintpl/index');
        }
        if(authcheck('Admin/Blacktpl/index')){
            $c['tree'][3]['tree'][2]['name'] = '审核模版';
            $c['tree'][3]['tree'][2]['url'] = U('Blacktpl/index');
        }
        if(authcheck('Admin/Ordertpl/index')){
            $c['tree'][3]['tree'][3]['name'] = '订单模板';
            $c['tree'][3]['tree'][3]['url'] = U('Ordertpl/index');
        }
        if(authcheck('Admin/Msgtpl/index')){
            $c['tree'][3]['tree'][4]['name'] = '短信模版';
            $c['tree'][3]['tree'][4]['url'] = U('Msgtpl/index');
        }
        $c['tree'][4]['name'] = '员工相关';
        if(authcheck('Admin/Member/index')){
            $c['tree'][4]['tree'][0]['name'] = '员工信息';
            $c['tree'][4]['tree'][0]['url'] = U('Member/index');
        }
        $c['tree'][5]['name'] = '商品相关';
        if(authcheck('Admin/Cate/index')){
            $c['tree'][5]['tree'][0]['name'] = '商品分类';
            $c['tree'][5]['tree'][0]['url'] = U('Cate/index');
        }
        if(authcheck('Admin/Product/index')){
            $c['tree'][5]['tree'][1]['name'] = '商品信息';
            $c['tree'][5]['tree'][1]['url'] = U('Product/index');
        }
        //
        $b['name'] = '统计相关';
        //报表相关
        $b['tree'][0]['name'] = '统计图表';
        if(authcheck('Admin/Count')){
            $b['tree'][0]['tree'][0]['name'] = '每日流量';
            $b['tree'][0]['tree'][0]['url'] = U('Count/index');
        }
        if(authcheck('Admin/Mine')){
            $b['tree'][0]['tree'][1]['name'] = '统计图表';
            $b['tree'][0]['tree'][1]['url'] = U('Mine/index');
        }
        if(authcheck('Admin/Count')){
            $b['tree'][0]['tree'][2]['name'] = '多订单IP';
            $b['tree'][0]['tree'][2]['url'] = U('Count/ip');
        }
        $b['tree'][1]['name'] = '报数相关';
        if(authcheck('Admin/Reportcate/index')){
            $b['tree'][1]['tree'][0]['name'] = '报数分类';
            $b['tree'][1]['tree'][0]['url'] = U('Reportcate/index');
        }
        if(authcheck('Admin/Reportplat/index')){
            $b['tree'][1]['tree'][1]['name'] = '推广平台';
            $b['tree'][1]['tree'][1]['url'] = U('Reportplat/index');
        }
        if(authcheck('Admin/Reportmember/index')){
            $b['tree'][1]['tree'][2]['name'] = '推广员工';
            $b['tree'][1]['tree'][2]['url'] = U('Reportmember/index');
        }
        if(authcheck('Admin/Reportplan/index')){
            $b['tree'][1]['tree'][3]['name'] = '计划分配';
            $b['tree'][1]['tree'][3]['url'] = U('Reportplan/index');
        }
        
        //系统管理
        $a['name'] = '系统相关';
        $a['tree'][0]['name'] = '系统设置';
        if(authcheck('Admin/Planset/index')){
            $a['tree'][0]['tree'][0]['name'] = '开关设置';
            $a['tree'][0]['tree'][0]['url'] = U('Planset/index');
        }
        if(authcheck('Admin/Baseset/index')){
            $a['tree'][0]['tree'][1]['name'] = '基础设置';
            $a['tree'][0]['tree'][1]['url'] = U('Baseset/index');
        }
        $a['tree'][3]['name'] = '系统日志';
        if(authcheck('Admin/Agentlog/index')){
            $a['tree'][3]['tree'][0]['name'] = '访问日志';
            $a['tree'][3]['tree'][0]['url'] = U('Agentlog/index');
        }
        if(authcheck('Admin/Actionlog/index')){
            $a['tree'][3]['tree'][1]['name'] = '操作日志';
            $a['tree'][3]['tree'][1]['url'] = U('Actionlog/index');
        }
        if(authcheck('Admin/Loginlog/index')){
            $a['tree'][3]['tree'][2]['name'] = '登陆日志';
            $a['tree'][3]['tree'][2]['url'] = U('Loginlog/index');
        }
        $a['tree'][1]['name'] = '帐号相关';
        if(authcheck('Admin/Admin/index')){
            $a['tree'][1]['tree'][0]['name'] = '帐号管理';
            $a['tree'][1]['tree'][0]['url'] = U('Admin/index');
        }
        if(authcheck('Admin/Group/index')){
            $a['tree'][1]['tree'][1]['name'] = '群组管理';
            $a['tree'][1]['tree'][1]['url'] = U('Group/index');
        }
        if(authcheck('Admin/Rule/index')){
            $a['tree'][1]['tree'][2]['name'] = '认证规则';
            $a['tree'][1]['tree'][2]['url'] = U('Rule/index');
        }
        $a['tree'][2]['name'] = '其他信息';
        if(authcheck('Admin/Index/main')){
            $a['tree'][2]['tree'][1]['name'] = '关于系统';
            $a['tree'][2]['tree'][1]['url'] = U('Index/main');
        }
        //
        $menu[0]['name'] = '主菜单';
        $menu[0]['tree'][0] = $c;
        $menu[0]['tree'][1] = $b;
        $menu[0]['tree'][2] = $a;
        return $menu;
    }
    //
    public function main(){
        //$Ip = new \Org\Net\IpLocation();
        //
        $info = $this->sysInfo();
        //$info['IP地址'] = get_client_ip();
        //$info['地理位置'] = $Ip->getlocation()['country'];
        $this->assign('info', $info);
        $this->display();
    }
    //
    private function sysInfo(){
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION.' [ <a href="javascript:void(0)" >查看最新版本</a> ]',
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年m月d日 H:i:s"),
            '北京时间'=>gmdate("Y年m月d日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((@disk_free_space(".")/(1024*1024*1024)),2).'G',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
            '风行者系统版本'=>'2.0.22',
            '系统官网'=>'https://fxz.nixi.win/',
        );
        return $info;
    }
}
