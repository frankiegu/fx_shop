<?php
namespace Home\Model;
use Think\Model;
class OrderModel extends Model{
    //数据表
    protected $tableName = 'order';
    //验证规则
    protected $_validate = [
        ['aid', 'require', '推广ID'],
        ['cname', 'require', 'cname不能为空'],
        ['telno', 'require', '电话号码不能为空'],
    ];
    //
}