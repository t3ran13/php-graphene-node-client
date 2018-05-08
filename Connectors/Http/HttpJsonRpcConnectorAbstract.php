<?php

namespace GrapheneNodeClient\Connectors\Http;

use GrapheneNodeClient\Connectors\ConnectorInterface;
use JsonRPC\Client;
use JsonRPC\Exception\ConnectionFailureException;


abstract class HttpJsonRpcConnectorAbstract implements ConnectorInterface
{
    /**
     * @var string
     */
    protected $platform;

    /**
     * wss or ws servers, can be list. First node is default, other are reserve.
     * After $maxNumberOfTriesToCallApi tries connects to default it is connected to reserve node.
     *
     * @var string|array
     */
    protected $nodeURL;

    /**
     * current node url, for example 'wss://ws.golos.io'
     *
     * @var string
     */
    private $reserveNodeUrlList;


    /**
     * current node url, for example 'https://api.golos.io'
     *
     * if you set several nodes urls, if with first node will be trouble
     * it will connect after $maxNumberOfTriesToCallApi tries to next node
     *
     * @var string
     */
    private $currentNodeURL;

    /**
     * waiting answer from Node during $wsTimeoutSeconds seconds
     *
     * @var int
     */
    protected $wsTimeoutSeconds = 5;

    /**
     * max number of tries to get answer from the node
     *
     * @var int
     */
    protected $maxNumberOfTriesToCallApi = 3;

    protected static $connection;
    protected static $currentId;

    public function getCurrentUrl()
    {
        if ($this->currentNodeURL === null) {
            if (is_array($this->nodeURL)) {
                $this->reserveNodeUrlList = $this->nodeURL;
                $url = array_shift($this->reserveNodeUrlList);
            } else {
                $url = $this->nodeURL;
            }

            $this->currentNodeURL = $url;
        }

        return $this->currentNodeURL;
    }

    public function isExistReserveNodeUrl()
    {
        return !empty($this->reserveNodeUrlList);
    }

    protected function setReserveNodeUrlToCurrentUrl()
    {
        $this->currentNodeURL = array_shift($this->reserveNodeUrlList);
    }

    public function connectToReserveNode()
    {
        $this->setReserveNodeUrlToCurrentUrl();
        return $this->newConnection($this->getCurrentUrl());
    }

    public function getCurrentId()
    {
        if (self::$currentId === null) {
            self::$currentId = 1;
        }

        return self::$currentId;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function setCurrentId($id)
    {
        self::$currentId = $id;
    }

    public function getNextId()
    {
        $next = $this->getCurrentId() + 1;
        $this->setCurrentId($next);

        return $next;
    }

    /**
     * @param string $apiName
     * @param array  $data
     * @param string $answerFormat
     * @param int    $try_number Try number of getting answer from api
     *
     * @return array|object
     * @throws \Exception
     */
    public function doRequest($apiName, array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY, $try_number = 1)
    {
        $requestId = $this->getNextId();
        $requestData = [
            'jsonrpc' => '2.0',
            'id'     => $requestId,
            'method' => 'call',
            'params' => [
                $apiName,
                $data['method'],
                $data['params']
            ]
        ];
        try {
            $answerRaw = $this->curlRequest($this->getCurrentUrl(), 'post', json_encode($requestData, JSON_UNESCAPED_UNICODE));
            if ($answerRaw['code'] !== 200) {
                throw new \Exception("Curl answer code is '{$answerRaw['code']}' and response '{$answerRaw['response']}'");
            }
            $answer = json_decode($answerRaw['response'], self::ANSWER_FORMAT_ARRAY === $answerFormat);

            if (isset($answer['error'])) {
                throw new \Exception('got error in answer: ' . $answer['error']['code'] . ' ' . $answer['error']['message']);
            }
            //check that answer has the same id or id from previous tries, else it is answer from other request
            if (self::ANSWER_FORMAT_ARRAY === $answerFormat) {
                $answerId = $answer['id'];
            } else { //if (self::ANSWER_FORMAT_OBJECT === $answerFormat) {
                $answerId = $answer->id;
            }
            if ($requestId - $answerId > ($try_number - 1)) {
                throw new \Exception('got answer from old request');
            }


        } catch (\Exception $e) {

            if ($try_number < $this->maxNumberOfTriesToCallApi) {
                //if got WS Exception, try to get answer again
                $answer = $this->doRequest($apiName, $data, $answerFormat, $try_number + 1);
            } elseif ($this->isExistReserveNodeUrl()) {
                //if got WS Exception after few ties, connect to reserve node
                $this->connectToReserveNode();
                $answer = $this->doRequest($apiName, $data, $answerFormat);
            } else {
                //if nothing helps
                throw $e;
            }
        }

        return $answer;
    }

    public function curlRequest($url, $type = 'get', $data = [], $curlOptions = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0");

        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } elseif ($type == 'get' && !empty($data)) {
            $temp = parse_url($url);
            if(!empty($temp['query'])){
                $data = parse_str($temp['query']) + $data;
            }
            $temp['query'] = $data;

            $url = $this->makeUrlFromArray($temp);
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        foreach ($curlOptions as $option => $val) {
            curl_setopt($ch, constant($option), $val);
        }
        if (empty($curlOptions['CURLOPT_CONNECTTIMEOUT'])){
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        }

        $response = curl_exec($ch);

        $data = [];
        $data['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $data['response'] = $response;
        curl_close($ch);

        return $data;
    }


    public function makeUrlFromArray($data)
    {
        $url = '';
        if (!empty($data['scheme'])) {
            $url .= $data['scheme'] . '://';
        }
        if (!empty($data['host'])) {
            $url .= $data['host'];
        }
        if (!empty($data['path'])) {
            $url .= $data['path'];
        }
        if (!empty($data['query'])) {
            if (is_array($data['query'])) {
                $data['query'] = http_build_query($data['query']);
            }
            $url .= '?' . $data['query'];
        }

        return $url;
    }
}