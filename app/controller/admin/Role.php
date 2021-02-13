<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\BaseController;
use think\Request;

class Role extends BaseController
{
    /**
     * 角色列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //获取所有参数
        $param = request()->param();
        // 每页条数
        $limit = getValByKey('limit', $param, 10);
        // 数据条数
        $total = $this->M->count();
        // alias 设置别名    with 关联预载入
        $list = $this->M->page($param['page'], $limit)->with(['rules' => function ($query) {$query->alias('a')->field('a.id');}])->order(['id' => 'desc'])->select();
        return showSuccess([
            'list' => $list,
            'total' => $total,
        ]);
    }

    /**
     * 修改角色
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $param = $request->only([
            'name',
            'status',
            'desc',
        ]);
        return showSuccess($request->Model->save($param));
    }

    /**
     * 修改角色状态
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $request->Model->status = $request->param('status');
        return showSuccess($request->Model->save());
    }

    /**
     * 删除角色
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // 判断该角色是否已使用
        $count = request()->Model->managers->count();
        if($count) apiException('该角色已被使用！'); 
        return showSuccess($request->Model->delete());
    }
    
    // 设置角色权限
    public function setRule(){
        $ruleIds = request()->param('rule_ids');
        return showSuccess(request()->Model->setRule($ruleIds));
    }
}
