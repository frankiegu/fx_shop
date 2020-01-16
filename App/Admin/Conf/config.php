<?php
return array(
    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING'=>array(          
	'[!Public]'=>__ROOT__.'/App/Admin/View/Public',
        'DEFAULT_FILTER'        => 'htmlspecialchars'
	  ),
    'LAYOUT_ON' => true,
    'PERPAGE' => 20,
    'siteName' => '风行者推广系统',
    'SESSION_OPTIONS' =>  array('expire'=>14400),//session 保存4小时
);