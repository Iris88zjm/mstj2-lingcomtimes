<?php 

namespace app\Controller;

use app\core\Home_Controller;
/**
 * 默认控制器
 */
class IndexController extends Home_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
        $province_list = parent::$model->select('province', ['id', 'province_name']);
		view('home/index', ['c' => get('c'), 'province_list' => $province_list]);
	}

    /**
     * 验证申请参数
     * @param  array $data 申请参数
     */
    private function _ckeckData($data)
    {
        if(empty($data['username'])) {
            ajaxReturn(202, '请填写姓名');
        }
        if(empty($data['phone'])) {
            ajaxReturn(202, '请填写手机号码');
        }else {
            if($this->checkPhoneNumber($data['phone']) == false) {
                ajaxReturn(202, '请填写正确的手机号码');
            }
        }

        // 根据 姓名+手机号判断申请是否存在
        $count = parent::$model->count('contect', ['username' => $data['username'], 'phone' => $data['phone']]);
        if (!empty($count)) {
            ajaxReturn(202, '请勿重复领取！');
        }
    }

    /**
     * 提交申请
     */
    public function submitContect()
    {
        $postData = post();
        $this->_ckeckData($postData);
        
        $postData['time'] = time();
        $postData['ip']   = getIp();
        parent::$model->insert('contect', $postData);
        if(parent::$model->id()) {
            ajaxReturn(200);
        }else {
            ajaxReturn(202, '申请失败');
        }
    }

    public function get_city()
    {
        $province_id = intval(post('province_id'));

        if ($province_id) {
            $city_list = parent::$model->select('city', ['id', 'city_name'], ['province_id' => $province_id]);
            ajaxReturn(200, $city_list);
        }

        ajaxReturn(400);
    }

}