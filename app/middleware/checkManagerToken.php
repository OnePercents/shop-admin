<?php
declare (strict_types = 1);

namespace app\middleware;

class checkManagerToken
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        $model = '\\app\\model\\Manager';
        // 获取token (请求时header中传入token)
        $token = $request->header('token');
        // 如果token 不存在,抛出异常
        if(!$token) apiException('token不存在,请先登录');
        // 根据token获取当前用户数据
        $user = cms_getUser([
            'token'=>$token
        ]);
        if(!$user) apiException('非法token,请先登录');
        // 挂载到$request->UserModel
        $request->UserModel = $model::find($user['id']);
        if(!$request->UserModel->status) apiException('该用户已被禁用');
        // 验证当前用户权限 超级管理员不需要验证
        if(!$user['super']){
            // 管理员权限验证...
            // halt('不是超级管理员');
        }
        return $next($request);
    }
}
