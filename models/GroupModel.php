<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jiefan
 * Date: 13-5-22
 * Time: 下午2:17
 * To change this template use File | Settings | File Templates.
 */

class GroupModel extends Model
{

    private $tableName = 'group';
    private $groupInfo = '';


    public function __construct($groupId = 0)
    {
        parent::__construct();

        if ($groupId) {
            $this->getGroup($groupId);
            $this->getGroupIcon();
            $this->getGroupCate();
            $this->getGroupDance();
            $this->getGroupAddress();
        }
    }

    /**
     * 获取最热舞友圈  默认取5条
     * @param $num
     * @return mixed
     */
    public function getHotGroup($num = 5)
    {
//        $sql = "select * from " . dbprefix . "group where !isnull(groupicon) or !isnull(icon) order by hot desc limit {$num}";
//        $allInfo = $this->db->fetch_all_assoc($sql);

        $allInfo = $this->findAll('group', "groupicon != '' or icon != ''", "hot desc", null, $num);

        return $allInfo;
    }


    /**
     * id 获取圈子信息
     *
     * @param $groupId
     * @return bool|mixed
     */
    public function getGroup($groupId)
    {

        $group = $this->find($this->tableName, array('groupid' => intval($groupId)), null, null);

        //$group['groupPic'] = $this->getGroupIcon($group);

        $this->groupInfo = $group;
        return $group;
    }

    /**
     * 获取圈子的头像
     *
     * @param array|int $group
     * @return array|void
     */
    public function getGroupIcon($group = null)
    {
        if (empty($group)) {
            $groupPic = $this->getPrevGroupInfo('groupPic');
            if (!$groupPic) {
                $pic['icon'] = $this->getPrevGroupInfo('icon');
                $pic['groupicon'] = $this->getPrevGroupInfo('groupicon');
                $pic['path'] = $this->getPrevGroupInfo('path');
            } else {
                return $groupPic;
            }
        }
        if (is_int($group)) {
            $pic = $this->find('group', array('groupid' => $group), 'path,groupicon,icon');
        }
        if (!empty($pic['icon'])) {
            $this->setPrevGroupInfo('groupPic', $pic['icon']);
            return $pic['icon'];
        } elseif (!empty($pic['path']) && !empty($pic['groupicon'])) {
            $groupPic = SITE_URL . tsXimg($pic['groupicon'], 'group', '120', '120', $pic['path'], '1');
            $this->setPrevGroupInfo('groupPic', $groupPic);
            return $groupPic;
        } else {
            return false;
        }
    }

    /**
     * 获取圈子的类型
     *
     * @param int $cateId
     * @param bool $rType   true只返回分类名
     * @return bool|mixed
     */
    public function getGroupCate($cateId = null, $rType = false)
    {
        if (empty($cateId)) {
            $cate = $this->getPrevGroupInfo('groupCate');
            if (!$cate) $cateId = $this->getPrevGroupInfo('cateid2');
        }

        if ($cateId && !$cate) {
            $cate = $this->find('group_cates', array('cateid' => $cateId));
            $this->setPrevGroupInfo('groupCate', $cate);
        }

        if ($rType) {
            return $cate['catename'];
        } else {
            return $cate;
        }

    }

    /**
     * 获取该圈子的舞蹈
     *
     * @param null $dances
     * @param bool $rType       true 返回舞蹈名组成的字符串
     * @param string $delimit   $rType=true时有效 多个舞蹈名之间分割符
     * @return array|bool|string
     */
    public function getGroupDance($dances = null, $rType = false, $delimit = ' ')
    {
        if (empty($dances)) {
            $danceStrArr = $this->getPrevGroupInfo('danceStrArr');
            if (!$danceStrArr) $dances = $this->getPrevGroupInfo('kdance');
        }
        if ($dances && !$danceStrArr) {
            $danceStrArr = $this->getMenu($dances);
            $this->setPrevGroupInfo('danceStrArr', $danceStrArr);
        }

        if ($rType) {
            return is_array($danceStrArr) ? implode($delimit, $danceStrArr) : $danceStrArr;
        } else {
            return $danceStrArr;
        }
    }


    /**
     * 舞友圈中地址处理 对应数据库中字段
     *
     * @param null $p
     * @param null $c
     * @param null $a
     * @param bool $rType       是否返回字符串
     * @return array|mixed|string
     */
    public function getGroupAddress($p = null, $c = null, $a = null, $rType = false)
    {
        if (empty($p) && empty($c) && empty($a)) {
            $address = $this->getPrevGroupInfo('address');
            if (!$address) {
                $ids = $this->getPrevGroupInfo('province') . ',';
                $ids .= $this->getPrevGroupInfo('city') . ',';
                $ids .= $this->getPrevGroupInfo('area') . ',';
            }
        } else {
            $ids = $p . ',';
            $ids .= $c . ',';
            $ids .= $a . ',';
        }

        if ($ids && !$address) {
            $ids = trim($ids, ',');
            $address = $this->getMenu($ids);
            $this->setPrevGroupInfo('address', $address);
        }

        if ($rType) {
            return implode(' ', $address);
        } else {
            return $address;
        }
    }

    /**
     * 获取 $this->groupInfo 中的字段
     * @param $field
     * @return mixed
     */
    protected function getPrevGroupInfo($field)
    {
        if (!empty($this->groupInfo) && array_key_exists($field, $this->groupInfo)) {
            return $this->groupInfo[$field];
        } else {
            return false;
        }
    }

    /**
     * 设置 $this->groupInfo 中的字段
     *
     * @param $filed
     * @param $value
     */
    protected function setPrevGroupInfo($filed, $value)
    {
        $this->groupInfo[$filed] = $value;
    }

    /**
     * $this->groupInfo;
     * @return string
     */
    public function getGroupInfo()
    {
        return $this->groupInfo;
    }

    /**
     * 根据用户ID获得官方舞友圈
     * @param $userId
     * @return bool|mixed
     */
    public function getGover($userId)
    {
        $group = $this->find('group', array('userid' => $userId, 'isgover' => 1));
        return $group;
    }

    /**
     * 加入舞友圈
     *
     * @param $groupId
     * @param $userId
     * @return int      1：等待管理员审核 2：加入成功
     */
    public function joinGroup($groupId, $userId)
    {
        $groupInfo = $this->getGroup($groupId);
        $joinData = array('groupid' => $groupId, 'userid' => $userId, 'addtime' => time());
        if ($groupInfo['joinway']) {
            $joinData['isaudit'] = 1;
            $msg = '您已提交申请，等待管理员审核！';
            $code = 1;
        } else {
            $msg = '加入成功！';
            $code = 2;
        }

        $this->create('group_users', $joinData);

        $this->upUser($groupId);

        return $code;
    }


    /**
     * 退出舞友圈
     * @param $groupId
     * @param $userId
     * @return int
     */
    public function outGroup($groupId, $userId)
    {
        $this->delete('group_users', array(
            'userid' => intval($groupId),
            'groupid' => intval($userId),
        ));

        $this->upUser($groupId);
//        echo json_encode(array('code' => 2, 'msg' => '退出成功!'));
        return 2;
    }

    /**
     * 更新group表中的用户数
     * @param $groupId
     * @return bool
     */
    protected function upUser($groupId)
    {
        $num = $this->findCount('group_users', array(
            'groupid' => $groupId,
        ));
        $this->update('group', array(
            'groupid' => $groupId,
        ), array(
            'count_user' => $num,
        ));
        return true;
    }

    /**
     * 获取用户在圈子中的身份
     *
     * @param $groupId
     * @param $userId
     * @return bool|string   false 无身份  founder 所有者 admin管理员 member会员 audit 待认证
     */
    public function getUserIdentity($groupId, $userId)
    {
        $info = $this->find('group_users', array('userid' => intval($userId), 'groupid' => intval($groupId)));
        if ($info) {
            if ($info['isfounder']) return 'founder';
            if ($info['isadmin']) return 'admin';
            if ($info['isaudit']) return 'audit';
            return 'member';
        } else {
            return false;
        }
    }

    /**
     * 获取圈子一天内的 发帖数和新家会员数之和
     *
     * @param $groupId
     */
    public function getDayNum($groupId)
    {
        $dayTime = date('Y-m-d') . " 00:00:00";
        //发帖数
        $sql = "select count(*) as num from " . dbprefix . "group_topics where groupid={$groupId} and addtime > unix_timestamp('{$dayTime}')";
        $dayTopNum = $this->db->fetch_all_assoc($sql);
        $dayTopNum = $dayTopNum[0]['num'];

        //回复数
        $sql = "select count(*) as num from " . dbprefix . "comment where group_id={$groupId} and addtime > '{$dayTime}'";
        $comment = $this->db->fetch_all_assoc($sql);
        $comment = $comment[0]['num'];

        return $dayTopNum + $comment;
    }
}