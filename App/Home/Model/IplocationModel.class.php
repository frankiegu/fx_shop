<?php
/**
 * IplocationModel.class.php
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

class IplocationModel{
    //
    public function search($ip, $flag = false){
        $corrected = $this->corrected($ip);
        if($corrected !== false){
            return $corrected;
        }
        if($flag){
            return $this->_local_ipLocation($ip);
        }
        $location = $this->_ip138_iplocation($ip);
        if($location){
            return $location;
        }elseif(is_null($location)){
            $location = $this->_ip138_iplocation($ip);
            if($location){
                return $location;
            }
        }
        $location = $this->_baidu_ipLocation($ip);
        if($location){
            return $location;
        }elseif(is_null($location)){
            $location = $this->_baidu_ipLocation($ip);
            if($location){
                return $location;
            }
        }
        $location = $this->_local_ipLocation($ip);
        return $location;
    }
    //ip138IP定位API
    protected function _ip138_iplocation($ip){
        $url = 'https://api.ip138.com/query/?ip='.$ip.'&datatype=jsonp';
        $header = ['token:*************************'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $res = curl_exec($ch);
        curl_close($ch);
        $resobj = json_decode($res, true);
        if($resobj['ret'] == 'err'){
            return false;
        }elseif(!isset($resobj['data'])){
            return null;
        }
        $data = $resobj['data'];
        $location = ['country'=>$data[0],'province'=>$data[1],'city'=>$data[2],'isp'=>$data[3]];
        return $location;
    }
    //百度地图IP定位API
    protected function _baidu_ipLocation($ip){
        $options = ['http'=>['method'=>'GET', 'timeout'=>3]];
        $context = stream_context_create($options);
        $ak = '**************************';
        $link = 'http://api.map.baidu.com/location/ip?ak='.$ak.'&ip='.$ip;
        $tt = file_get_contents($link, false, $context);
        $arr = json_decode($tt, true);
        if(!isset($arr['status'])){
            return null;
        }elseif($arr['status'] != '0'){
            return false;
        }
        $data = explode('|', $arr['address']);
        $location = ['country'=>'中国','province'=>$data[1],'city'=>$data[2],'isp'=>$data[4]];
        return $location;
    }
    //获取本地IP地址库数据
    protected function _local_ipLocation($ip){
        $location = [];
        $Ip = new \Org\Net\IpLocation();
        $Arr = $Ip->getlocation($ip);
        $Arr['country'] = iconv('gbk', 'utf-8', $Arr['country']);
        $Arr['area'] = iconv('gbk', 'utf-8', $Arr['area']);
        if((strpos($Arr['country'], '市') === false) && (strpos($Arr['country'], '省') === false)){
            $location['city'] = '未知';
        }else{
            $tArr = explode('市', $Arr['country']);
            $tt = $tArr[0];
            if(strpos($tt, '省') === false){
                $location['city'] = $tt;
            }else{
                $tArr = explode('省', $tt);
                $location['city'] = trim($tArr[1]) == '' ? '未知' : $tArr[1];
                $location['province'] = $tArr[0];
            }
        }
        $location['country'] = '中国';
        return $location;
    }
    //修正
    protected function corrected($ip){
        $arr = [
            '120.230.105.179' => ['country'=>'中国','province'=>'广东','city'=>'广州','isp'=>'移动'],
        ];
        if(isset($arr[$ip])){
            return $arr[$ip];
        }
        return false;
    }
}
