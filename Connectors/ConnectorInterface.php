<?php

namespace GrapheneNodeClient\Connectors;


interface ConnectorInterface
{
    const PLATFORM_GOLOS   = 'golos';
    const PLATFORM_STEEMIT = 'steemit';

    const ANSWER_FORMAT_ARRAY  = 'array';
    const ANSWER_FORMAT_OBJECT = 'object';

    /**
     * @param int $timeoutSeconds
     */
    public function setConnectionTimeoutSeconds($timeoutSeconds);

    /**
     * Number of tries to reconnect (get correct answer) to api
     *
     * @param int $triesN
     */
    public function setMaxNumberOfTriesToReconnect($triesN);

    /**
     * @return string
     */
    public function getPlatform();

    /**
     * @param string $apiName calling api name - follow_api, database_api and ect.
     * @param array  $data    options and data for request
     * @param string $answerFormat
     *
     * @return array|object return answer data
     */
    public function doRequest($apiName, array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY);
}