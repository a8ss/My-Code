<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jiefan
 * Date: 13-5-21
 * Time: 下午2:27
 * To change this template use File | Settings | File Templates.
 */
class TopicModel extends Model
{

    private $tableName = 'group_topics';

    /**
     * ID获取一篇帖子
     *
     * @param $topicId
     * @return bool|mixed
     */
    public function getTopic($topicId)
    {
        $topicId = intval($topicId);
        $topic = $this->find($this->tableName, array('topicid' => $topicId));

        $this->getOtherInfo($topic);

        return $topic;
    }

    /**
     * 根据圈子ID获取该圈子的帖子
     *
     * @param int $groupId
     * @param string $order
     * @param int $page
     * @param int $pageSize
     * @param array|string $where
     * @return array
     */
    public function getTopics($groupId = 0, $page = 1, $pageSize = 10, $order = 'addtime desc', $where = array())
    {
        if ($where) {
            if (is_array($where) && $groupId) {
                $where['groupid'] = intval($groupId);
            }elseif(is_string($where) && $groupId){
                $where = addslashes($where);
                $groupId = intval($groupId);
                $where .= " and groupid={$groupId}";
            }
        }elseif($groupId){
            $where['groupid'] = intval($groupId);
        }else{
            $where = null;
        }
        $pager = $this->getPager($this->tableName, $where, $page, $pageSize);
        $topics = $this->findAll($this->tableName, $where, $order, null, $pager['offset']);
        if (!empty($topics)) {
            $this->getOtherInfo($topics);
            return array('data' => $topics, 'pager' => $pager);
        }

    }


    /**
     * 搜索帖子
     *
     * @param $words
     * @param int $groupId
     * @param int $page
     * @param int $pageSize
     * @param string $order
     * @return array
     */
    public function searchTopic($words, $groupId = 0, $page = 1, $pageSize = 10, $order = 'addtime desc')
    {
        return $this->getTopics($groupId, $page,$pageSize,$order," title like '%{$words}%'");
    }

    /**
     * 获取帖子的其他信息
     */
    protected function getOtherInfo(&$topic)
    {
        if (!is_array($topic)) return false;
        if (isset($topic[0]) && is_array($topic[0])) {
            foreach ($topic as $k => &$v) {
                $this->getOtherInfo($v);
            }
        }
        if (isset($topic['typeid'])) {
            $topic['typeName'] = $this->getTopicType($topic['typeid'], true);
        }
        if (isset($topic['content'])) {
            $topic['contentInfo'] = $this->contentInfo($topic['content']);
        }
//        var_dump($topic);exit();
    }

    /**
     * 获取帖子的回复  默认按时间到序排列
     *
     * @param int $topicId
     * @param int $page
     * @param int $pageSize
     * @param string $order
     * @return mixed
     */
    public function getReply($topicId, $page = 1, $pageSize = 10, $order = 'addtime desc')
    {
        $where = array('appid' => $topicId, 'appkey' => 'group');
        if ($pageSize == 1) {
            //最新回复
            return $this->find('comment', $where, null, $order);
        } else {
            $pager = $this->getPager('comment', $where, $page, $pageSize);
            $reply = $this->findAll('comment', $where, $order, null, $pager['offset']);
            return array('data' => $reply, 'pager' => $pager);
        }
    }


    /**
     * 获取30日内的最热话题 Topic
     *  字段中 hot算法 见 script/topicHot.php
     * @param int $num         获取多少条
     * @param bool $groupId    指定圈子
     * @return bool|mixed
     */
    public function getHotTopic($num = 1, $groupId = false)
    {
        $time = strtotime("-30 day");
        $where = "addtime > {$time} ";
        if ($groupId) {
            $groupId = intval($groupId);
            $where = $where . ' and groupid=' . $groupId;
        }
        //$sql = "select * from " . dbprefix . "group_topics where " . $where . ' order by hot desc limit ' . $num;
//        echo $sql,"<br/>";
//        $hotTopic = $this->db->fetch_all_assoc($sql);
        $hotTopic = $this->findAll('group_topics', $where, 'hot desc,addtime desc', null, $num);
        $this->getOtherInfo($hotTopic);

        return $hotTopic;
    }


    /**
     * 获取帖子类型
     * @param $typeId
     * @param bool $onlyName       true 只返回分类名
     * @return bool|mixed
     */
    public function getTopicType($typeId, $onlyName = false)
    {
        $type = $this->find('group_topics_type', array('typeid' => intval($typeId)));
        if ($onlyName) {
            return $type['typename'];
        } else {
            return $type;
        }
    }

    /**
     * 获取帖子内容信息 内容简介 是否有图片（地址）、是否有视频（地址）
     *
     * @param string $topicContent     帖子的内容
     * @param int $descNum      内容简介字数 30个字
     * @param string $descChr   内容的编码  utf8
     * @return mixed
     */
    public function contentInfo($topicContent, $descNum = 30, $descChr = 'utf8')
    {
        $imgRegex = "/.*(<img.*?src=[\"\'](.*?)[\"\'].*?>).*/i";
        $videoRegex = "/.*?(<embed.*?src=[\"\'](.*?)[\"\'].*?>).*?/i";
        $musicRegex = "/(<object.*?>.*?value=.*(http\:\/\/.*?\.mp3).*?<\/object>)/i";
        if ($info['img'] = preg_match($imgRegex, $topicContent, $imgUrl)) {
            $info['imgHtml'] = $imgUrl[1];
            $info['imgUrl'] = $imgUrl[2];
        }
        if ($info['video'] = preg_match($videoRegex, $topicContent, $videoUrl)) {
            $info['videoHtml'] = $videoUrl[1];
            $info['videoUrl'] = $videoUrl[2];
        }
        if ($info['music'] = preg_match($musicRegex, $topicContent, $musicUrl)) {
            $info['musicHtml'] = $musicUrl[1];
            $info['musicUrl'] = $musicUrl[2];
        }

        //简介
        $noTagsDesc = trim(strip_tags($topicContent));
        $info['desc'] = mb_substr($noTagsDesc, 0, $descNum, $descChr);
        if (strlen($noTagsDesc) > $descNum) {
            $info['desc'] .= '...';
        }

        return $info;
    }

}