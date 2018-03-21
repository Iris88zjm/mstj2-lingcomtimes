<?php echo view('admin/header') ?>
    <div class="container clear">
        <?php echo view('admin/sidebar') ?>
        <div class="main fr">
            <h1>留言板</h1>
            <div class="operate">
                <a href="javascript:document.search.submit()" class="btn search">查询</a>
                <a href="javascript:;" class="btn export" onClick="export_order()">导出</a>
                <a href="javascript:;" class="btn delete" onClick="delete_board()">删除</a>
                <a href="javascript:location.reload();" class="btn reflash">刷新</a>
            </div>

            <div class="search clear">
                <form action="<?php echo base_url('board');?>" class="searchForm" method="GET" name="search">
                    <div class="entry">
                        <label>用户名:</label>
                        <input type="text" name="username" placeholder="">
                    </div>
                    <div class="entry">
                        <label>邮箱:</label>
                        <input type="text" name="email" placeholder="">
                    </div>
                    <div class="entry">
                        <label>手机号:</label>
                        <input type="text" name="phone" placeholder="">
                    </div>
                    <div class="entry">
                        <!-- <label>邮箱:</label>
                        <input type="text" name="email" placeholder=""> -->
                    </div>
                    <div class="entry">
                        <label>时间段:</label>
                        <input type="text" name="start_date" id="start_date" placeholder="" onclick="WdatePicker()"> - 
                        <input type="text" name="end_date" id="end_date" placeholder="" onclick="WdatePicker()">
                    </div>    
                </form>
            </div>

            <div class="table">
                <form action="" id="board_form" method="post">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="" id="chkall"></th>
                                <th>序号</th>
                                <th>用户名</th>
                                <th>手机号</th>
                                <th>邮箱</th>
                                <th>留言内容</th>
                                <th>ip</th>
                                <th>留言时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($boardData as $key => $board): ?>
                                <tr>
                                    <td><input type="checkbox" class="chk" name="board[]" value="<?php echo $board['id'] ?>"</td>
                                    <td><?php echo $number++ ?></td>
                                    <td><?php echo $board['username'] ?></td>
                                    <td><?php echo $board['phone'] ?></td>
                                    <td><?php echo $board['email'] ?></td>
                                    <td><?php echo $board['contect'] ?></td>
                                    <td><?php echo $board['ip'] ?></td>
                                    <td><?php echo get_date($board['time']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </form>
                <div class="paginate">
                    <ul class="clear">
                       <?php if ($count > $pageNum): ?>
                            <?=$pageList?>
                        <?php endif ?>
                    </ul>
                </div>
            </div> <!-- end table -->
        </div><!-- end main -->
    </div>
    <?php echo js('My97DatePicker/WdatePicker.js') ?>
    <script>
        /**
         * 导出csv
         */
        function export_order()
        {
            $('#board_form').attr('action', "<?php echo base_url('board/downloadBoard') . $exportUri ?>");
            $('#board_form').submit();
        }

        /**
         * 删除留言
         */
        function delete_board(id)
        {
            if(confirm('确定删除？') == true){
                $("#board_form").attr('action', '<?=base_url('board/deleteBoardByIds')?>');
                $("#board_form").submit();
            }
        }

    </script>
<?php echo view('admin/footer') ?>

