<?php 
namespace app\Controller;
use system\core\Config;
use system\core\Page;
use app\core\Wb_Controller;

/**
 * 用户模块
 * @author 命中水、
 * @date(2017.9.24)
 */
class BoardController extends Wb_Controller
{
    private $_userModel;
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 拼接查询条件
     * @return array
     */
    public function _getSearch()
    {
        if(get('username')) {
            $where['username[~]'] = get('username');
        }

        if(get('email')) {
            $where['email[~]'] = get('email');
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
     * 用户列表
     */
    public function index()
    {
        $where = $this->_getSearch();
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

        $data['count']     = parent::$model->count('board', $where);
        $where['LIMIT']     = [$offset, $pageNum];

        $data['boardData']  = parent::$model->select('board', '*', $where);
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
        view('admin/board', $data);
    }

    /**
     * 根据主键删除
     */
    public function deleteById()
    {
        $id = intval(get('id'));
        if($id) {
            $flag = parent::$model->delete('board', ['id' => $id]);
            if($flag) redirect('user');
        }
    }


    /**
     * 删除申请
     */
    public function deleteBoardByIds()
    {
        if(post('board') && is_array(post('board'))) {
            $boardIds = post('board');
            foreach ($boardIds as $boardId) {
                parent::$model->delete('board', ['id' => $boardId]);
            }
        }
        redirect('board');
    }

    /**
     * 导出CSV
     */
    public function downloadBoard()
    {
        header("Content-Type: application/force-download");
        header("Content-type:text/csv;charset=utf-8");  
        header("Content-Disposition:filename=" . date("YmdHis") . ".csv");  
        $where    = $this->_getSearch();
        $orderIds = post('board');
        if($orderIds && is_array($orderIds)) {
            $where['id'] = $orderIds;
        } else {
            $where = $this->_getSearch();
        }
        $boardData  = parent::$model->select('board', '*', $where);
        echo "\xEF\xBB\xBF用户名,电话,邮箱,内容,ip,提交时间\r";
        ob_end_flush();  
        foreach($boardData as $board) {  
            echo $board['username'] . "," . "\"\t". $board['phone'] . "\",\"\t" . $board['email'] . "\",\"\t" . $board['contect'] ."\",\"\t" . $board['ip'] . "\",\"\t" .  get_date($board['time']). "\"\t\r";  
            flush();  
        }  
    }
}