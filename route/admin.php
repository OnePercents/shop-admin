<?php
use think\facade\Route;

// 不需要验证
Route::group('admin',function(){
  // 新建管理员
  Route::post('login','admin.Manager/login');
})->allowCrossDomain();  //跨域请求支持

// 需要验证
Route::group('admin',function(){
  
  // 退出登录
  Route::post('logout','admin.Manager/logout');
  
  // ------------------管理员
  // 修改管理员状态 禁用/启用
  Route::post('manager/:id/update_status','admin.Manager/updateStatus');
  // 删除管理员
  Route::post('manager/:id/delete','admin.Manager/delete');
  // 更新管理员
  Route::post('manager/:id','admin.Manager/update');
  // 新建管理员
  Route::post('manager','admin.Manager/save');
  // 管理员列表
  Route::get('manager/:page','admin.Manager/index');

  // ------------------角色
  // 设置角色权限
  Route::post('role/set_rule','admin.Role/setRule'); 
  // 修改管理员状态 禁用/启用     
  Route::post('role/:id/update_status','admin.Role/updateStatus');
  // 删除管理员
  Route::post('role/:id/delete','admin.Role/delete');
  // 更新管理员
  Route::post('role/:id','admin.Role/update');
  // 新建管理员
  Route::post('role','admin.Role/save');
  // 管理员列表
  Route::get('role/:page','admin.Role/index');
  
  
  // ------------------权限
  // 修改管理员状态 禁用/启用     
  Route::post('rule/:id/update_status','admin.rule/updateStatus');
  // 删除管理员
  Route::post('rule/:id/delete','admin.rule/delete');
  // 更新管理员
  Route::post('rule/:id','admin.rule/update');
  // 新增权限
  Route::post('rule','admin.Rule/save');
  // 权限列表
  Route::get('rule/:page','admin.rule/index');

})->middleware(app\middleware\checkManagerToken::class);
