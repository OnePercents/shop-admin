<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class BaseValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];

    // 自定义验证规则
    protected function isExist($value, $rule, $data = [], $field, $title)
    {
        if (!$value) {
            return true;
        }

        //查找数据
        $model = '\app\model\\' . $rule;
        $m = $model::find($value);
        // 如果数据不存在
        if (!$m) {
            return '该记录不存在';
        }
        // 存在，挂载到request->Model上(错误:request->m,因为不存在)
        request()->Model = $m;
        return true;
    }
}
