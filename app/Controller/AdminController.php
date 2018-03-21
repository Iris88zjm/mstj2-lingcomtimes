<?php 
namespace app\Controller;

use app\core\Wb_Controller;
use system\core\Config;
use system\core\Page;

/**
 * 后台控制器
 */
class AdminController extends Wb_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 后台首页
     */
    public function index()
    {
        view('admin/index');
    }

    /**
     * 拼接查询条件
     * @return array
     */
    public function _getOrderSearch()
    {
        if(get('username')) {
            $where['username[~]'] = get('username');
        }

        if(get('c') || get('c') == 0) {
            $where['c[~]'] = get('c');
        }

        if(get('phone')) {
            $where['phone[~]'] = get('phone');
        }

        if(get('start_date') && get('end_date')) {
            $where['time[<>]'] = [strtotime(get('start_date')), strtotime(get('end_date'))];
        } else {
            if(get('start_date')) {
                $where['time[>]'] = strtotime(get('start_date'));
            } else if(get('end_date')){
                $where['time[<]'] = strtotime(get('end_date'));
            }
        }
        return $where;
    }

    /**
     * 申请列表
     */
    public function orderList()
    {
        // 取出查询条件
        $where = $this->_getOrderSearch();
        // 取出查询参数uri
        $parameter = getSearchParam();
        if(isset($_GET['page'])) {
            $now_page = intval($_GET['page']) ? intval($_GET['page']) : 1;
        }else {
            $now_page = 1;
        }
        // 取得每页条数
        $pageNum           = Config::get('PAGE_NUM', 'page');
        // 计算偏移量
        $offset            = $pageNum * ($now_page - 1);

        $data['count']     = parent::$model->count('contect', $where);
        $where['LIMIT']    = [$offset, $pageNum];

        $data['orderData'] = parent::$model->select('contect', '*', $where);
        // 分页处理
        $objPage           = new page($data['count'], $pageNum, $now_page, '?page={page}' . $parameter);
        $data['pageNum']   = $pageNum;
        $data['pageList']  = $objPage->myde_write();

        // 取出导出uri参数
        if($parameter) {
            $data['exportUri'] = '?' . ltrim($parameter, '&');
        }
        // 计算序号
        if($now_page == 1) {
            $data['number'] = 1;
        }else {
            $data['number'] = $pageNum * ($now_page - 1) + 1;
        }
        $data['province_list'] = parent::$model->select('province', ['id', 'province_name']);
        view('admin/order', $data);
    }

    /**
     * 删除申请
     */
    public function deleteOrderByIds()
    {
        if(post('order') && is_array(post('order'))) {
            $orderIds = post('order');
            foreach ($orderIds as $orderId) {
                parent::$model->delete('contect', ['id' => $orderId]);
            }
        }
        redirect('admin/orderList');
    }

    /**
     * 导出CSV
     */
    public function downloadOrder()
    {
        header("Content-Type: application/force-download");
        header("Content-type:text/csv;charset=utf-8");  
        header("Content-Disposition:filename=".date("YmdHis").".csv");  
        $where    = $this->_getOrderSearch();
        $orderIds = post('order');
        if($orderIds && is_array($orderIds)) {
            $where['id'] = $orderIds;
        } else {
            $where = $this->_getOrderSearch();
        }
        $orderData  = parent::$model->select('contect', '*', $where);

        echo "\xEF\xBB\xBF子链接,用户名,电话,提交时间\r";
        ob_end_flush();  
        foreach($orderData as $order) {  
            echo $order['c'] . "," . "\"\t" . $order['username'] . "\",\"\t" . $order['phone'] . "\",\"\t" .  get_date($order['time']). "\"\t\r";  
            flush();  
        }  
    }
}