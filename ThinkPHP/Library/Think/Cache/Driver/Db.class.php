<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think\Cache\Driver;
use Think\Cache;
defined('THINK_PATH') or exit();
/**
 * 数据库方式缓存驱动
 *    CREATE TABLE think_cache (
 *      cachekey varchar(255) NOT NULL,
 *      expire int(11) NOT NULL,
 *      data blob,
 *      datacrc int(32),
 *      UNIQUE KEY `cachekey` (`cachekey`)
 *    );
 */
class Db extends Cache {

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options=array()) {
        $this->options  =   $options;   
        $this->options['table']     =   isset($options['table']) ?  $options['table']   :   C('DATA_CACHE_TABLE');
        $this->options['prefix']    =   isset($options['prefix'])?  $options['prefix']  :   C('DATA_CACHE_PREFIX');
        $this->options['length']    =   isset($options['length'])?  $options['length']  :   0;        
        $this->options['expire']    =   isset($options['expire'])?  $options['expire']  :   C('DATA_CACHE_TIME');
        $this->handler   = \Think\Db::getInstance();
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name) {
        $name       =  $this->options['prefix'].addslashes($name);
        N('cache_read',1);
        //数组方式，防注入攻击
        $map = [];
        $map['cachekey'] = $name;
        $map['expire'] = [['eq', 0], ['gt', time()], 'or'];
        $result = M($this->options['table'], null)->where($map)->field(['data','datacrc'])->limit(1)->find();
        if(false !== $result ) {
            if(C('DATA_CACHE_CHECK')) {//开启数据校验
                if($result['datacrc'] != md5($result['data'])) {//校验错误
                    return false;
                }
            }
            $content   =  $result['data'];
            if(C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
                //启用数据压缩
                $content   =   gzuncompress($content);
            }
            $content    =   unserialize(stripslashes($content));
            return $content;
        }
        else {
            return false;
        }
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value,$expire=null) {
        $data   =  addslashes(serialize($value));
        $name   =  $this->options['prefix'].addslashes($name);
        N('cache_write',1);
        if( C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
            //数据压缩
            $data   =   gzcompress($data,3);
        }
        if(C('DATA_CACHE_CHECK')) {//开启数据校验
            $crc  =  md5($data);
        }else {
            $crc  =  '';
        }
        if(is_null($expire)) {
            $expire  =  $this->options['expire'];
        }
        $expire	    =   ($expire==0) ? 0: (time()+$expire) ;
        $result     =   M($this->options['table'], null)->where(['cachekey'=>$name])->field(['cachekey'])->limit(1)->select();
        if(!empty($result) ) {
        	//更新记录
            $result  =  M($this->options['table'], null)->where(['cachekey'=>$name])->data(['data'=>$data,'datacrc'=>$crc,'expire'=>$expire])->save();
        }else {
        	//新增记录
            $result  =  M($this->options['table'], null)->data(['cachekey'=>$name,'data'=>$data,'datacrc'=>$crc,'expire'=>$expire])->add();
        }
        if($result) {
            if($this->options['length']>0) {
                // 记录缓存队列
                $this->queue($name);
            }
            return true;
        }else {
            return false;
        }
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name) {
        $name  =  $this->options['prefix'].addslashes($name);
        return M($this->options['table'], null)->where(['cachekey'=>$name])->delete();
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear() {
        return $this->handler->execute('TRUNCATE TABLE `'.$this->options['table'].'`');
    }

}