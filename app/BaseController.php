<?php
declare (strict_types = 1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    // --------------------------------------------------自定义属性----------------------------------------------------

    // 存储自动实例化模型对象
    protected $M = null;
    // 是否自动实例化对象
    protected $autoModel = true;
    // 模型路径
    protected $modelPath = null;

    // 自动实例化验证器
    protected $autoValidate = true;
    // 自定义验证场景
    protected $autoValidateScene = [];
    // 不需要自动验证的方法
    protected $excludeAutoValidate = [];

    // 存储当前控制器的相关信息
    protected $cInfo = [];

    // ----------------------------------------------------自定义属性----------------------------------------------------

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        $this->cInfo = [
            'name' => class_basename($this), //获取当前类名
            'path' => str_replace('.', '\\', $this->request->controller()), //获取当前控制器
            'action' => $this->request->action(), //获取当前方法
        ];
        // dump($this->cInfo);
        $this->getAutoModel();
        $this->getAutoValidate();
    }

    // ----------------------------------------------------自定义方法----------------------------------------------------

    // 自动实例化模型
    protected function getAutoModel()
    {
        if ($this->autoModel) {
            $modelName = $this->modelPath  ? str_replace('/', '\\', $this->modelPath) : $this->cInfo['name'];
            $this->M = app('app\model\\' . $modelName);
        }
    }

    // 自动实例化验证器
    protected function getAutoValidate()
    {
        // 参数验证
        if ($this->autoValidate && !in_array($this->cInfo['action'],$this->excludeAutoValidate)) {
            // 实例化验证器
            $V = app('app\validate\\' . $this->cInfo['path']);
            // 验证场景
            $scene = array_key_exists($this->cInfo['action'], $this->autoValidateScene) ? $this->autoValidateScene[$this->cInfo['action']] : $this->cInfo['action'];
            if (!$V->scene($scene)->check($this->request->param())) {
                apiException($V->getError());
            }
        }
    }

    // ----------------------------------------------------自定义方法----------------------------------------------------

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    // 新增方法
    public function save(Request $request)
    {
        $param = $this->request->param();
        return showSuccess($this->M->save($param));
    }
}
