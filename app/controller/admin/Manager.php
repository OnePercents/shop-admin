<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\BaseController;
use think\Request;

class Manager extends BaseController
{

    // 实例化模型对象 -----> $this-M ----->BaseController.php 控制器中定义
 
    // 根据id自动查找对象 -----> $this->request->Model ----->BaseValidate.php 验证器中定义

    // 自动验证登录对象 -----> $this->request->UserModel ----->Manager.php 验证器中定义
    // ！！这两个对象 不是同一对象 
    // 中间件验证 -----> $this->request->UserModel ----->checkManagerToken.php 中间件中定义

    // 不需要自动实例化模型时
    // protected $autoModel = false;
    // 需要自定义模型路径时
    // protected $modelPath = 'admin/path';
    // 不需要自动实例化验证器
    // protected $autoValidate = false;
    // 需要自定义验证场景时
    // protected $autoValidateScene = ['save'=>'save1'];
    // 不需要自动验证参数的方法
    protected $excludeAutoValidate = ['logout'];

    // 管理员登陆
    public function login(Request $request){ 
        $user = cms_login([
            'data' => $request->UserModel
        ]);
        return showSuccess($user);
    }

    // 管理员退出
    public function logout(Request $request){
        return showSuccess(cms_logout([
            'token' => $request->header['token']
        ]));
    }

    /**
     * 管理员列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取所有参数
        $param = $this->request->param();
        // 每页条数
        $limit = getValByKey('limit',$param,10);
        // 搜索关键词
        $keyword = getValByKey('keyword',$param,'');
        // 搜索条件
        $where = [
            ['username','like','%'.$keyword.'%']
        ];
        // 数据总条数
        $total = $this->M->where($where)->count();
        // 查询数据 page()分页  with()关联查询  order()排序  hidden()隐藏字段
        $list = $this->M->page($param['page'],$limit)->where($where)->with('role')->order(['id'=>'desc'])->select()->hidden(['password']);
        return showSuccess([
            $list,
            $total
        ]);
    }

    /**
     * 创建管理员
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 获取传过来的数据并保存到数据库
        // 数据过滤
        $param = $request->only([
            'username',
            'password',
            'avatar',
            'role_id',
            'status',
        ]);
        // 新增管理员返回管理员数据
        return showSuccess($request->Model->save($param));
    }

    /**
     * 更新管理员
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $param = $request->only([
            'username',
            'password',
            'avatar',
            'role_id',
            'status',
        ]);
        // 更新管理员,返回结果 
        return showSuccess($request->Model->save($param));
    }

    /**
     * 删除管理员
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // 根据id查找到的用户数据
        $manager = $this->request->Model;
        // 管理员不能删除自己  验证登录账号与查找到的用户账号是否是同一账号
        if($this->request->UserModel->id == $manager->id){
            apiException('不能删除自己');
        }
        // 不能删除超级管理员
        if($manager->super === 1){
            apiException('不能删除超级管理员');
        }
        return showSuccess($manager->delete());
    }

    // 修改用户状态
    public function updateStatus(){
        // 获取参数
        $manager = $this->request->Model;
        // 不能修改自己的状态
        if($this->request->UserModel->id == $manager->id) apiException('不能修改自己的状态');
        // 不能修改超级管理员的状态
        if($manager->super == 1) apiException('不能修改超级管理员的状态');
        $manager->status = $this->request->param('status');
        return showSuccess($manager->save());
    }
    
}
