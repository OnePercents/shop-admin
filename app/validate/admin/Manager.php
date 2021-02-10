<?php
declare (strict_types = 1);

namespace app\validate\admin;

use app\validate\BaseValidate;

class Manager extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => 'require|integer|>:0|isExist:Manager', // id|别名
        'page' => 'require|integer|>:0', //分页
        'username' => 'require|min:5|max:20', // uniqid:manager 验证字段唯一性
        'password' => 'require|min:5|max:20',
        'avatar' => 'url',
        'status' => 'require|integer|in:0,1',
        'role_id' => 'require|integer|>:0', //角色id
        'limit' => 'integer|>:0', //限制分页条数
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
        'index' => ['page', 'limit'], //管理员列表
        'delete' => ['id'], //删除
        'updateStatus' => ['id', 'status'], //修改用户状态
    ];
    // 新增管理员
    public function sceneSave()
    {
        return $this->only(['username', 'password', 'avatar', 'role_id', 'status'])->append('username', 'unique:manager');
    }
    // 更新管理员
    public function sceneUpdate()
    {
        $id = request()->param('id');
        return $this->only(['id', 'username', 'password', 'avatar', 'role_id', 'status'])->append('username', 'unique:manager,username,' . $id);
    }
    // 登录
    public function sceneLogin()
    {
        return $this->only(['username', 'password'])->append('password', 'checkLogin');
    }

    public function checkLogin($value, $rule, $data = [], $field, $title)
    {
        // 查找数据库中是否有这个对象
        $manager = \app\model\Manager::where('username', $data['username'])->find();
        if (!$manager) return '该账户不存在';
        if (!password_verify($data['password'], $manager->password)) return '密码错误';
        // 账号密码验证成功 将当前对象挂载到request
        request()->UserModel = $manager;
        return true;
    }
}
