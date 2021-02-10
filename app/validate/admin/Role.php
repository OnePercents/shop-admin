<?php
declare (strict_types = 1);

namespace app\validate\admin;

use app\validate\BaseValidate;

class Role extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => 'require|integer|>:0|isExist:Role',
        'page' => 'require|integer|>:0', //分页
        'limit' => 'integer|>:0', //限制分页条数
        'name' => 'require|max:20',
        'status' => 'require|integer|in:0,1',
        
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];

    // 验证场景
    protected $scene = [
        'index' => ['page', 'limit'], //角色列表
        'updateStatus'=> ['id','status']
    ];
    public function sceneSave()
    {
        return $this->only(['name', 'status'])->append('name', 'unique:role');
    }
    // 更新管理员
    public function sceneUpdate()
    {
        $id = request()->param('id');
        return $this->only(['id', 'name', 'status'])->append('name', 'unique:role,name,' . $id);
    }
}
