<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;

class MailCatcher extends BaseFixture
{
    const MAIL_CATCHER_BASE_URL = 'http://m2.docker:1080';

    /**
     * @return array
     */
    public function getLastEmailData()
    {
        $lastEmailData = [];

        $id = $this->_getLastEmailId();
        if (!is_null($id)) {
            $lastEmailData = $this->_getEmailData($id);
        }

        return $lastEmailData;
    }

    /**
     * @param  int $id
     *
     * @return array
     */
    protected function _getEmailData($id)
    {
        $baseUrl = $this->_getBaseUrl();
        return $this->_curlGetData("$baseUrl/messages/$id.json");
    }

    /**
     * @return int|null
     */
    protected function _getLastEmailId()
    {
        $messages = $this->_getMessages();

        if (is_array($messages)) {
            $last = end($messages);
            return $last['id'];
        }

        return null;
    }

    /**
     * @return array
     */
    protected function _getMessages()
    {
        $baseUrl = $this->_getBaseUrl();
        return $this->_curlGetData("$baseUrl/messages");
    }

    /**
     * @param  string $url
     *
     * @return array
     */
    protected function _curlGetData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data, true);
    }

    /**
     * @return string
     */
    protected function _getBaseUrl()
    {
        return self::MAIL_CATCHER_BASE_URL;
    }
}
