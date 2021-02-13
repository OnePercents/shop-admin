<?php
// 应用公共文件

// 全局异常处理
function apiException($msg = '请求错误', $errorCode = 20000, $statusCode = 400)
{
    abort($errorCode, $msg, [
        'statusCode' => $statusCode,
    ]);
}

// 成功返回
function showSuccess($data = '', $msg = 'ok', $code = 200)
{
    return json([
        'msg' => $msg,
        'data' => $data,
    ], $code);
}

// 失败返回
function showError($msg = 'ok', $code = '400')
{
    return json([
        'msg' => $msg,
    ], $code);
}

// 获取数组指定key的值
function getValByKey($key, $arr, $default = false)
{
    return array_key_exists($key, $arr) ? $arr[$key] : $default;
}

// 登录
function cms_login(array $param)
{
    // 获取参数
    $data = getValByKey('data', $param);
    if (!$data) {
        return false;
    }

    // 标签分组 标识是普通用户还是管理员
    $tag = getValByKey('tag', $param, 'manager');
    // 是否返回密码
    $password = getValByKey('password', $param);
    // 登录有效时间 0为永久
    $expire = getValByKey('expire', $param, 0);
    // 获取缓存类型
    $cacheType = \think\facade\Cache::store(config('cms.' . $tag . '.token.store'));
    // 生成唯一token
    $token = sha1(md5(uniqid(md5(microtime(true)), true)));
    // 拿到当前用户数据
    $user = is_object($data) ? $data->toArray() : $data;
    // 获取之前的token 防止重复登录
    $beforeToken = $cacheType->get($tag.'_'.$user['id']);
    if($beforeToken){
      cms_logout([
        'token' => $token,
        'tag' => $tag
      ]);
    }
    // 存储当前用户数据
    $cacheType->set($tag . '_' . $token, $user, $expire);
    // 存储token
    $cacheType->set($tag . '_' . $user['id'], $token, $expire);
    // 隐藏密码
    if (!$password) {
        unset($user['password']);
    }

    // 返回当前用户数据
    $user['token'] = $token;
    return $user;
}

// 根据token获取当前用户数据
function cms_getUser(array $param)
{
  $tag = getValByKey('tag',$param,'manager');
  $token = getValByKey('token',$param);
  $password = getValByKey('password',$param);
  $user = \think\facade\Cache::store(config('cms.'.$tag.'.token.store'))->get($tag.'_'.$token);
  if(!$password) unset($user['password']);
  return $user;
}

// 退出登录
function cms_logout(array $param)
{
  $tag = getValByKey('tag',$param,'manager');
  $token = getValByKey('token',$param);
  $user = \think\facade\Cache::store(config('cms.'.$tag.'.token.store'))->pull($tag.'_'.$token);
  if(!empty($user)) \think\facade\Cache::store(config('cms.'.$tag.'.token.store'))->pull($tag.'_'.$user[id]);
  return $user;
}

// 数据集组合分类树(一维数组)
function list_to_tree($array,$field = 'pid',$pid = 0,$level = 0){
    //声明静态数组,避免递归调用时,多次声明导致数组覆盖
    static $list = [];
    foreach ($array as $key => $value){
        if ($value[$field] == $pid){
            $value['level'] = $level;
            $list[] = $value;
            unset($array[$key]);
            list_to_tree($array,$field,$value['id'], $level+1);
        }
    }
    return $list;
}

// 数据集组合分类树(多维数组)
function list_to_tree2($cate,$field = 'pid',$child = 'child',$pid = 0,$callback = false){
    if(!is_array($cate)) return [];
    $arr = [];
    foreach($cate as $v){
        $extra = true;
        if(is_callable($callback)){
            $extra = $callback($v);
        }
        if($v[$field] == $pid && $extra){
            $v[$child] = list_to_tree2($cate,$field,$child,$v['id'],$callback);
            $arr[]     = $v;
        }
    }
    return $arr;
}
