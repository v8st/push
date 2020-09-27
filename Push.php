<?php

/**
 * @author zzw
 * @date 2015-03-26
 * @note
 */
if (!defined('API_ROOT_PATH')) {
    define('API_ROOT_PATH', dirname(__FILE__));
}
require_once ( API_ROOT_PATH . '/Push/Channel.class.php' );

class Push {

    public static $_instance = NULL;
    //接收人的user_id
    public $_user_id = '';
    //接收类型
    public $_type = '';
    //标签
    public $_tag = '';
    //ios
    const IOS = 1;
    //android
    const Android = 2;
    //appkey
    static $_app = array( 
        IOS => array(
                        
        ),
        Android => array(
        )
    );

    /**
     * 初始化
     * @return type
     */
    public static function getInstance() {
        if (NULL === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 设置接收用户id
     * @param type $user_id
     * @return \Push
     */
    function setUser($user_id) {
        $this->_user_id = $user_id;
        return $this;
    }

    /**
     * 取得接收用户id
     * @return type
     */
    function getUser() {
        return $this->_user_id;
    }

    /**
     * 设置类型
     * @param type $type
     * @return \Push
     */
    function setType($type) {
        $this->_type = $type;
        return $this;
    }

    /**
     * 获取类型
     * @return type
     */
    function getType() {
        return $this->_type;
    }

    /**
     * 设置标签
     * @param type $tag
     * @return \Push
     */
    function setTag($tag) {
        $this->_tag = $tag;
        return $this;
    }

    /**
     * 获取标签
     * @return type
     */
    function getTag() {
        return $this->_tag;
    }
    /**
     * setTag: 创建消息标签
     * 
     * 用户关注: 
     *
     * @access public
     * @param string $tagName 标签名称
     * @param array $optional 可选参数，支持的可选参数包括 self::USER_ID，如果指定user_id，服务器会完成与tag的绑定操作
     * @return 成功: array; 失败: false
     * 
     * @version 1.0.0.0
     */
    public function setTags($tagName, $optional = null)
    {
        //安卓        
        //苹果
    }
    /**
     * 检测关联用户id
     * @global type $apiKey
     * @global type $secretKey
     * @param type $userId
     */
    public function test_verifyBind($channelid) {
        $channel = new Channel(self::$_app[Android]['apiKey'], self::$_app[Android]['secretKey']);
        $optional [Channel::CHANNEL_ID] = $channelid;
        $ret = $channel->verifyBind($this->getUser(), $optional);
        if (false === $ret) {
            return false;
        } else {
            return $ret;
        }
    }

    /**
     * 用户和标签进行绑定
     * @param type $tag_name
     * @param type $user_id
     * @return boolean
     */
    public function test_setTag($tag_name, $user_id) {
        $channel = new Channel(self::$_app[Android]['apiKey'], self::$_app[Android]['secretKey']);
        $optional[Channel::USER_ID] = $user_id;
        $ret = $channel->setTag($tag_name, $optional);
        if (false === $ret) {
            return false;
        } else {
            return $ret['response_params']['tid'];
        }
    }

    /**
     * 消息推送
     * @param type $data= array('title' => '这是一个群发7891987','description' => '这是一个群发说明7891987','url' => 'http://www.baidu.com');
     *      
	 */
    public function send($data) {
        //发送类型个人和群发检测,标签
        if (!in_array($this->getType(), array('1','2', '3')))return;
        //个人用户id不能为空
        if ($this->getType() == '1' and $this->getUser() == '')return;
        //类型判断
        if (!is_numeric($this->getUser()))return;
        $base_data = array(
            'user_id' => $this->getUser(),
            'push_type' => $this->getType(),
            'url' => $data['url'],
            'is_open' => $data['is_open'],
            'time' => date('Y-m-d h:i:s', time())
        );
        //开始推送安卓消息
        $android_data = array(
            'title' => $data['title'],
            'description' => $data['description'],
        );
        //开始推送苹果消息
        $ios_data = array(
            'alert' => $data['title']
        );
        Push::push_ios($base_data + $ios_data);
    }

    /**
     * 输出数据格式化
     * @param type $str
     */
    public static function right_output($str) {
        echo "\033[1;40;32m" . $str . "\033[0m" . "\n";
    }

    /**
     * 报错数据格式化
     * @param type $str
     */
    public static function error_output($str) {
        echo "\033[1;40;31m" . $str . "\033[0m" . "\n";
    }

    /**
     * 推送android设备消息
     * @param type $data 参数
     * age:单发 
     * $data=array('user_id'=>'','push_type'=>'','title'=>'','description'=>,'url'=>'')
     */
    public static function push_android($data) {
        $optional = array(
            Channel::USER_ID => $data['user_id'],
            Channel::DEVICE_TYPE => 3,
            Channel::MESSAGE_TYPE => 1
        );
        //消息参数 
        $message = array(
            'title' => $data['title'],
            'description' => $data['description'],
            'notification_basic_style' => 7,
            'open_type' => 0,
            'custom_content' => array(
                'url' => $data['url'],
                'is_open' => $data['is_open'], //1打开0不打开
                'time' => $data['time']
            ),
        );
        $channel = new Channel(self::$_app[Android]['apiKey'], self::$_app[Android]['secretKey']);
        $ret = $channel->pushMessage($data['push_type'], json_encode($message), $message_key = 'msg_key', $optional);
        if (false === $ret) {
            return false;
        } else {
            return $ret;
        }
    }

    
}

?>