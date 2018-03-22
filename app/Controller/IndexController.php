<?php 

namespace app\Controller;
use app\library\YunPianSms;
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
        if (empty(intval($data['code']))) {
            ajaxReturn(202, '请填写验证码');
        }
        if(empty($data['username'])) {
            ajaxReturn(202, '请填写姓名');
        }
        if(empty($data['phone'])) {
            ajaxReturn(202, '请填写手机号码');
        }else {
            if($this->checkPhoneNumber($data['phone']) == false) {
                ajaxReturn(202, '请填写正确的手机号码');
            }
            $code = parent::$model->select('sms_check', 'code', [
                'date'  => date('Ymd', time()),
                'phone' => $data['phone']
            ])[0];

            if ($code != intval($data['code'])) {
                ajaxReturn(202, '验证码填写错误');
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
        unset($postData['code']);
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

    /**
     * 生成验证码
     * @param  integer $length 验证码长度
     * @return string          验证码字符串
     */
    public function createSmsCode($length = 6)
    {
        $min = pow(10, ($length -1));
        $max = pow(10, $length) -1;
        return rand($min, $max);
    }

    /**
     * 发送短信前验证
     * 1、验证手机号码是否正确
     * 2、验证手机短信次数
     * @param  int $phone 手机号
     * @return json
     */
    public function checkSms($phone)
    {

        if($this->checkPhoneNumber($phone) == false) {
            ajaxReturn(202, '请填写正确的手机号码');
        }

        $date = date('Ymd', time());
        $smsCheckInfo = parent::$model->select('sms_check', ['num', 'time'], ['date' => $date, 'phone' => $phone])[0];

        if ($smsCheckInfo['num'] >= 5) {
            ajaxReturn(202, '今日发送短信已到上限，请明天再试');
        }

        if (intval(time()) - intval($smsCheckInfo['time']) < 60) {
            ajaxReturn(202, '短信已发送，请60s后重试');
        }
    }

    /**
     * 发送短信接口
     * @return json
     */
    public function sendSms()
    {
        $phone = post('phone');
        // $phone = 18336344600;
        $this->checkSms($phone);

        $yunPian     = new YunPianSms('6da328d306cfa93b8fd6a1c1e003aa84');
        // $yunPianUser = $yunPian->getUser();
        $smsCode     = $this->createSmsCode();
        $result      = $yunPian->sendCode("【慕尚天街】您的验证码是". $smsCode ."。如非本人操作，请忽略本短信", $phone);
        // $result['code'] = 0;
        // $result['msg'] = '发送成功';
        if ($result['code'] === 0 && $result['msg'] == '发送成功') {
            $date  = date('Ymd', time());
            $attr  = ['date' => $date, 'phone' => $phone];
            $smsCheckInfo = parent::$model->select('sms_check', '*', $attr)[0];

            if ($smsCheckInfo && is_array($smsCheckInfo)) {
                parent::$model->update('sms_check', [
                    'num'  => $smsCheckInfo['num'] + 1,
                    'time' => time(),
                    'code' => $smsCode
                ], $attr);
            } else {
                parent::$model->delete('sms_check', ['phone' => $phone]);
                parent::$model->insert('sms_check', [
                    'phone' => $phone,
                    'num'   => 1,
                    'time'  => time(),
                    'date'  => date('Ymd', time()),
                    'code'  => $smsCode
                ], $attr);
            }

            ajaxReturn(200, '发送成功');
        } else {
            ajaxReturn(202, '发送失败');
        }
    }
}