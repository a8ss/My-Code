<?php
/**
 *
 */
class RegexController extends Controller
{
    public function index()
    {

        //去数据库读出数据
        $db = Model::getDB();
        $sql = 'select id,host,showname,name,url,md5url,sptime,oncenum,nextnum,listurl,regcurl,regex from sp_regex where id = 5';
        $query = $db->query($sql);
        $part = $query->fetch();

        //读取采集记录
        $query = $db->query("select md5url from sp_{$part['name']}");
        $md5urlArr = $query->fetchAll(PDO::FETCH_COLUMN);

        $contentURLs = array();
        //处理页数找出地址
        for ($i = $part['nextnum']; $i < ($part['oncenum'] + $part['nextnum']); $i++) {
            $thisPageURL = str_replace('(*)', $i, $part['listurl']);

            $cont = $this->getContent($thisPageURL);
            //获取内容页地址
            $contentURLs[] = $this->runMatchAll($cont, $part['regcurl']);
        }
        $contentURLs = $this->oneArr($contentURLs);


        $regexArr = json_decode($part['regex']);

        $count = count($contentURLs);
        $insertValueArr = array();
        for($i = 0; $i < $count; $i++) {
            //补全URL
            $curl = $part['host'] . $contentURLs[$i];
            $md5url = md5($curl);
            if (array_search($md5url, $md5urlArr) !== false) {
                //已经有记录了
                continue;
            }
            $cont = $this->getContent($curl);
            foreach ($regexArr as $val) {
                //这里$val是对象
                $insertValueArr[$i][$val->ename] = $this->runMatch($cont, $val->regex);
            }
            $insertValueArr[$i]['url'] = $curl;
            $insertValueArr[$i]['md5url'] = $md5url;
        }
        /**
        $insertValueArr 格式：
        Array(
        [0] => Array(
        [pianming] => 逃离德黑兰
        [xiazaidizhi] => ftp://dygod1:dygod1@d393.dygod.org:9075/[电影天堂-www.dy2018.net].逃离德黑兰.720p.BD中英双字幕.rmvb
        )
        [1] => Array(
        [pianming] => 亲密治疗/性福疗程
        [xiazaidizhi] => ftp://dygod1:dygod1@d068.dygod.org:1204/[电影天堂-www.dy2018.net].亲密治疗.720p.BD中英双字幕.rmvb
        )
        ）
         */


        //构造插入语句
        if (!empty($insertValueArr)) {

            $fieldStr = array_keys($insertValueArr[array_rand($insertValueArr)]);
            $fieldStr = implode(',', $fieldStr);

            $sql = "insert into sp_{$part['name']} ({$fieldStr}) values ";
            foreach ($insertValueArr as $val) {
                $sql .= "('" . implode("','", $val) . "'),";
            }
            $sql = rtrim($sql, ',');

            $this->smarty->assign('msg', '共入库：'. $db->exec($sql). '条');
        } else {
            $this->smarty->assign('msg', '操作完成，没有新增加内容');
        }


        $this->smarty->display('regex.html');
    }


    public function runMatch($cont, $reg)
    {
        preg_match($reg, $cont, $result);
        unset($result[0]);
        if (count($result) == 1) {
            $result = implode('', $result);
        }
        return $result;
    }

    public function runMatchAll($cont, $reg)
    {
        preg_match_all($reg, $cont, $result);
        unset($result[0]);
        if (count($result) == 1) {
            $result = array_values($result[1]);
        }
        return $result;
    }

    public function getContent($url)
    {
        if (file_exists('regexcache/' . md5($url))) {
            return file_get_contents('regexcache/' . md5($url));
        } else {
            $sp = new Spider($url);
            $cont = $sp->send();
            $cont = preg_replace('/\s{2,}/i', '', $cont); // 去换行空格保存到文件
            file_put_contents('regexcache/' . md5($url), $cont);
            return $cont;
        }
    }

    /**
     * 将一个多维数组合并成一维数组
     * @param $arr
     */
    public function oneArr($arr)
    {

        static $res = array();
        foreach ($arr as $val) {
            if (is_array($val)) {
                $this->oneArr($val);
            } else {

                $res[] = $val;
            }
        }

        return $res;

    }


    // 添加时预览
    public function getPageContent()
    {
        header('Content-Type:text/html; charset=utf-8');

        $url = $_POST ['url'];
        // echo '<pre>', $regex, '</pre>';
        // exit();

        $md5url = md5($url);
        if (file_exists("regexcache/{$md5url}")) {
            $contentStr = file_get_contents("regexcache/{$md5url}");
        } else {

            $spider = new Spider($url);

            $contentStr = $spider->send();

            // 去换行空格保存到文件
            $contentStr = preg_replace('/\s{2,}/i', '', $contentStr);
            file_put_contents("regexcache/{$md5url}", $contentStr);
        }

        echo $contentStr;
    }

    /**
     * 保存规则 列表页
     */
    public function saveList()
    {
        $url = isset ($_POST ['url']) ? $_POST ['url'] : die ('缺少参数');
        $name = isset ($_POST ['name']) ? $_POST ['name'] : die ('缺少参数');
        $showName = isset ($_POST ['showName']) ? $_POST ['showName'] : die ('缺少参数');
        $regex = isset ($_POST ['regex']) ? $_POST ['regex'] : die ('缺少参数');


        if (0 !== strpos($url, 'http')) {
            $url = 'http://' . $url;
        }
        $host = parse_url($url);
        $host = isset ($host['host']) ? $host ['host'] : die ('URL地址错误');


        // 添加栏目
        $db = Model::getDB();
        $query = $db->query("select id from sp_regex where md5('{$url}') = md5url");


        if ($query->fetch() !== false) {
            // 已经存在这个栏目
            die ('该地址已经存在！');
        } else {

            $sql = 'insert into sp_regex (host,showname,name,url,md5url,addtime,regcurl) values (';
            $sql .= "'$host','$showName','$name','$url','" . md5($url) . "'," . time() . ",'" . addslashes($regex) . "')";

            if ($db->exec($sql) > 0) {

                $redata['id'] = $db->lastInsertId();
                $redata['msg'] = '添加成功！';

                echo json_encode($redata);

            } else {

                $redata['msg'] = '添加失败！';
                echo json_encode($redata);
            }
        }
    }

    /**
     * 保存内容页字段
     */
    public function saveField()
    {
        $pdata = $this->testSavePostData();
        $url = $pdata ['url'];
        $name = $pdata ['name'];
        $regex = $pdata ['regex'];
        $hostid = $pdata['hostid'];

        $partid = isset ($_POST ['partid']) ? $_POST ['partid'] : die ('缺少参数');

        $cn = count($name);
        if ($cn == count($regex)) {
            $sql = 'insert into sp_regexmoive (partid' . implode(',', $name);
            $sql .= ') values (';
            foreach ($regex as $reg) {
                $sql .= '';
            }


        }

    }

}