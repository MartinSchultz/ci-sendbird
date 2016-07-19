<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sendbird
{
    private $CI;


    public function __construct()
    {
        $this->CI = &get_instance();
    }

    private function sendRequest($method, $params = array(), $overRideMethod = false)
    {
        if (!$overRideMethod) {
            $params['api_token'] = $this->CI->config->item('sendbirdApiToken');
            $params['auth'] = $this->CI->config->item('sendbirdApiToken');
        }
        $context_params = array(
            'http' => array(
                'method' => !$overRideMethod ? 'POST' : $overRideMethod,
                'header' => "Content-Type: application/json\r\n",
                'ignore_errors' => true,
                'content' => json_encode($params)
            )
        );

        $context = stream_context_create($context_params);
        $fp = fopen('https://api.sendbird.com/' . $method . ($overRideMethod ? '?api_token=' . $this->CI->config->item('sendbirdApiToken') : ''), 'rb', false, $context);
        $res = false;
        if ($fp) {
            $res = stream_get_contents($fp);
        }
        if ($res === false) {
            return array(
                'error' => true,
                'message' => 'Connection failed.',
            );
        }
        return json_decode($res, true);
    }

    public function viewGroupChannel($channelUrl)
    {
        return $this->sendRequest('messaging/view', array('channel_url' => $channelUrl));
    }

    public function createGroupChannel( $name, $isGroup, $coverUrl = '', $data = '')
    {
        return $this->sendRequest('messaging/create', array('name' => $name, 'cover_url' => $coverUrl, 'data' => $data, 'is_group' => $isGroup));
    }

    public function inviteGroupChannel($channelUrl, $userIds)
    {
        return $this->sendRequest('messaging/invite', array('channel_url' => $channelUrl, 'user_ids' => $userIds));
    }

    public function leaveGroupChannel($channelUrl, $userIds)
    {
        return $this->sendRequest('messaging/leave', array('channel_url' => $channelUrl, 'user_ids' => $userIds));
    }

    public function createChannel($channelUrl, $name, $isGroup, $coverUrl = '', $data = '')
    {
        return $this->sendRequest('channel/create', array('channel_url' => $channelUrl, 'name' => $name, 'cover_url' => $coverUrl, 'data' => $data, 'is_group' => $isGroup));
    }

    public function sendChannel($id, $channelUrl, $message = '', $data = '')
    {
        return $this->sendRequest('channel/send', array('id' => $id, 'channel_url' => $channelUrl, 'message' => $message, 'data' => $data));
    }

    public function viewChannel($channelUrl)
    {
        return $this->sendRequest('channel/view', array('channel_url' => $channelUrl));
    }

    public function listChannels()
    {
        return $this->sendRequest('channel/list');
    }

    public function createUser($id, $name = '', $image = '', $issueAccessToken = false)
    {
        return $this->sendRequest('user/create', array('id' => $id, 'nickname' => $name, 'image_url' => $image, 'issue_access_token' => $issueAccessToken));
    }

    public function authUser($id, $issueAccessToken = false)
    {
        return $this->sendRequest('user/auth', array('id' => $id, 'issue_access_token' => $issueAccessToken));
    }

    public function broadcastMessage($channelUrls, $message, $persistent = true, $data = '')
    {
        return $this->sendRequest('admin/broadcast_message', array('id' => $channelUrls, 'message' => $message, 'persistent' => $persistent, 'data' => $data));
    }

    public function createBot($botUserId, $botNickName, $botCallBackUrl, $isPrivacyMode)
    {
        return $this->sendRequest('v2/bots', array('bot_userid' => $botUserId, 'bot_nickname' => $botNickName, 'bot_callback_url' => $botCallBackUrl, 'is_privacy_mode' => $isPrivacyMode));
    }

    public function sendChannelBot($botUserId, $channelUrl, $message, $data = '')
    {
        return $this->sendRequest('v2/bots/' . $botUserId . '/send', array('channel_url' => $channelUrl, 'message' => $message, 'data' => $data));
    }

    public function listBots()
    {
        return $this->sendRequest('v2/bots', array(), 'GET');
    }

    public function deleteBot($botUserId)
    {
        return $this->sendRequest('v2/bots/' . $botUserId, array(), 'DELETE');
    }

    public function updateBot($botUserId, $botNickName, $botCallBackUrl, $isPrivacyMode)
    {
        return $this->sendRequest('v2/bots/' . $botUserId, array('bot_nickname' => $botNickName, 'bot_callback_url' => $botCallBackUrl, 'is_privacy_mode' => $isPrivacyMode));
    }

}