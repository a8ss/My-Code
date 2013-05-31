<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jiefan
 * Date: 13-5-23
 * Time: 上午10:01
 * To change this template use File | Settings | File Templates.
 */

class Model extends tsApp
{

    public function __construct()
    {
        global $db;
        parent::__construct($db);
    }


    public function getPager($tableName, $where, $page, $pageSize)
    {
        $pager['page'] = $page;
        $pager['pageSize'] = $pageSize;
        $pager['total'] = parent::findCount($tableName, $where);
        $pager['maxPage'] = ceil($pager['total'] / $pageSize);
        $pager['offset'] = (($page - 1) * $pageSize) . ',' . $pageSize;
        return $pager;
    }

    /**
     * 获取主站 menu 表
     *
     * @param int|array|sering $ids   string: 1|2|3  1,2,3  array: array(1,2,3)
     * @param bool $rType       多个ID默认返回array ，true：返回字符串
     * @param string $delimit   返回字符串的分割符
     * @return array|string
     */
    public function getMenu($ids)
    {
        if (is_int($ids)) {
            $dances = S('util.menu')->getName($ids);
            return $dances;
        }else{
            $idArr = strstr($ids, '|') ? explode('|', $ids) : null ;
            $idArr = strstr($ids, ",") ? explode(',', $ids) : null;
            $dances = is_array($idArr) && !empty($idArr) ? S('util.menu')->getIn($idArr) : null;
        }

        if(!empty($dances)){
            return $dances;
        }else{
            return false;
        }
    }

}