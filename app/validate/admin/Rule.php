<?php
declare (strict_types = 1);

namespace app\validate\admin;

use think\Validate;

class Rule extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'page' => 'require|integer|>:0',
        'limit' => 'integer|>:0',
        'rule_id' => 'require|integer|isExist:Rule,false',
        'status' => 'require|in:0,1',
        'name' => 'require|unique:rule',
        'menu' => 'require|in:0,1',
        'order' => 'integer',
        'method' => 'require|in:GET,POST,PUT,DELETE'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [

    ];

    protected $scene = [
        'index' => ['page','limit'],
        'save' => ['rule_id','name','status','menu','order','method'],
    ];
}
