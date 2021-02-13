<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\BaseController;
use think\Request;

class Rule extends BaseController
{
    /**
     * 权限列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取分页等参数
        $param = $this->request->param();
        $page = getValByKey('page',$param,1);
        $limit = getValByKey('limit',$param,100);
        $total = $this->M->count();
        $list = $this->M->page($page,$limit)->order(['id'=>'desc'])->select();
        return showSuccess([
            'list' => list_to_tree2($list->toArray(),'rule_id'),
            'total' => $total,
            'rules' => list_to_tree($list->toArray(),'rule_id')
        ]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    // public function save(Request $request)
    // {
    //     //
    // }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
