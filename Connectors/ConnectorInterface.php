<?php

namespace GrapheneNodeClient\Connectors;


interface ConnectorInterface
{
    const PLATFORM_GOLOS = 'golos';
    const PLATFORM_STEEMIT = 'steemit';

    const ANSWER_FORMAT_ARRAY = 'array';
    const ANSWER_FORMAT_OBJECT = 'object';

    /**
     * @return string
     */
    public function getPlatform();

    /**
     * @param array $data options and data for request
     * @param string $answerFormat
     * @return array return array with answer data
     */
    public function doRequest(array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY);
}