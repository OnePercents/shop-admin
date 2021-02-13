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

    //设置角色权限
    public function setRule(array $ruleIds){
        // 获取当前角色所有的权限
        $roleRule = new \app\model\RoleRule();
        $allRuleIds = $roleRule->where('role_id',$this->id)->column('rule_id');
        // 要增加的权限
        $addRules = array_diff($ruleIds,$allRuleIds);
        // 如果有添加的权限
        if(count($addRules)){
            $add = [];
            foreach($addRules as $val){
                $add[] = [
                    'role_id' => $this->id,
                    'rule_id' => $val
                ];
            }
            // 批量新增
            $roleRule->saveAll($add);
        }
        // 要删除的权限
        $delRules = array_diff($allRuleIds,$ruleIds);
        // 如果有删除的权限
        if(count($delRules)){
            $roleRule->where([
                ['role_id','=',$this->id],
                ['rule_id','in',$delRules]
            ])->delete();
        }
    }
}
