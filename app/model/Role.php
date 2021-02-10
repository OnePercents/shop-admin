<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Role extends Model
{
    // 多个用户可能属于同一角色  一对多关系
    public function managers(){
        return $this->hasMany('Manager');
    }

    // 一个角色可能有多个权限,多个角色可能有同一个权限 多对多
    // belongsToMany('Rule','Role_Rule','Rule_id','Role_id');
    public function rules(){
        return $this->belongsToMany('Rule','role_rule');
    }
}
