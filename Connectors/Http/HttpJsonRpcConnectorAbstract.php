<?php

namespace GrapheneNodeClient\Connectors\Http;

use GrapheneNodeClient\Connectors\ConnectorInterface;


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
    protected static $nodeURL;


    /**
     * current node url, for example 'https://api.golos.io'
     *
     * if you set several nodes urls, if with first node will be trouble
     * it will connect after $maxNumberOfTriesToCallApi tries to next node
     *
     * @var string
     */
    protected static $currentNodeURL;

    /**
     * waiting answer from Node during $wsTimeoutSeconds seconds
     *
     * @var int
     */
    protected $wsTimeoutSeconds = 3;

    /**
     * max number of tries to get answer from the node
     *
     * @var int
     */
    protected $maxNumberOfTriesToCallApi = 3;

    /**
     * counter of tries to change node to get answer. max = total nodes
     *
     * @var int
     */
    protected $resetTryNodes = 1;

    protected static $currentId;

    /**
     * HttpJsonRpcConnectorAbstract constructor.
     *
     * @param int $orderNodesByTimeoutMs skip this checks when it is 0.
     *                                   do not set it is too low, if node do not answer it go out from list
     */
    public function __construct($orderNodesByTimeoutMs = 0) {

        if ($orderNodesByTimeoutMs > 0 && is_array(static::$nodeURL) && count(static::$nodeURL) > 1) {
            $this->orderNodesByTimeoutMs($orderNodesByTimeoutMs);
        }
    }

    /**
     * @param int $timeoutSeconds
     */
    public function setConnectionTimeoutSeconds($timeoutSeconds)
    {
        $this->wsTimeoutSeconds = $timeoutSeconds;
    }

    /**
     * Number of tries to reconnect (get correct answer) to api
     *
     * @param int $triesN
     */
    public function setMaxNumberOfTriesToReconnect($triesN)
    {
        $this->maxNumberOfTriesToCallApi = $triesN;
    }


    /**
     * @param integer $orderNodesByTimeoutMs Only if you set few nodes. do not set it is too low, if node do not answer it go out from list
     *
     * @return void
     */
    public function orderNodesByTimeoutMs($orderNodesByTimeoutMs)
    {
        $requestId = $this->getNextId();
        $limits = [4, 7];
        $requestData = [
            'jsonrpc' => '2.0',
            'id'      => $requestId,
            'method'  => 'call',
            'params'  => [
                'database_api',
                'get_discussions_by_created',
                [['limit' => 7]]
            ]
        ];
        $timeouts = [];
        foreach (static::$nodeURL as $currentNodeURL) {
            try {
                $curlOptions = [];
                $curlOptions['CURLOPT_CONNECTTIMEOUT_MS'] = $orderNodesByTimeoutMs;
                $startMTime = microtime(true);
                foreach ($limits as $limit) {
                    $requestData['params'][2] = [['limit' => $limit]];
                    $answerRaw = $this->curlRequest(
                        $currentNodeURL,
                        'post',
                        json_encode($requestData, JSON_UNESCAPED_UNICODE),
                        $curlOptions
                    );

                    if ($answerRaw['code'] !== 200) {
                        throw new \Exception("Curl answer code is '{$answerRaw['code']}' and response '{$answerRaw['response']}'");
                    }
                    $answer = json_decode($answerRaw['response'], self::ANSWER_FORMAT_ARRAY);
                    if (isset($answer['error'])) {
                        throw new \Exception('got error in answer: ' . $answer['error']['code'] . ' ' . $answer['error']['message']);
                    }
                }
                $timeout = $requestTimeout = microtime(true) - $startMTime;
                $timeouts[$currentNodeURL] = round($timeout, 4);

            } catch (\Exception $e) {
            }
        }
        asort($timeouts);
        static::$nodeURL = array_keys($timeouts);
    }

    public function getCurrentUrl()
    {
        if (
            !isset(static::$currentNodeURL[$this->getPlatform()])
            || static::$currentNodeURL[$this->getPlatform()] === null
            || !in_array(static::$currentNodeURL[$this->getPlatform()], static::$nodeURL)
        ) {
            if (is_array(static::$nodeURL)) {
                $url = array_values(static::$nodeURL)[0];
            } else {
                $url = static::$nodeURL;
            }

            static::$currentNodeURL[$this->getPlatform()] = $url;
        }

        return static::$currentNodeURL[$this->getPlatform()];
    }

    protected function setReserveNodeUrlToCurrentUrl()
    {
        $totalNodes = count(static::$nodeURL);
        foreach (static::$nodeURL as $key => $node) {
            if ($node === $this->getCurrentUrl()) {
                if ($key + 1 < $totalNodes) {
                    static::$currentNodeURL[$this->getPlatform()] = static::$nodeURL[$key + 1];
                } else {
                    static::$currentNodeURL[$this->getPlatform()] = static::$nodeURL[0];
                }
                break;
            }
        }
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
     * @param bool   $resetTryNodes update total requested nodes tries counter
     *
     * @return array|object
     * @throws \Exception
     */
    public function doRequest($apiName, array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY, $try_number = 1, $resetTryNodes = true)
    {
        if ($resetTryNodes) {
            $this->resetTryNodes = count(static::$nodeURL);
        }
        $requestId = $this->getNextId();
        $requestData = [
            'jsonrpc' => '2.0',
            'id'      => $requestId,
            'method'  => 'call',
            'params'  => [
                $apiName,
                $data['method'],
                $data['params']
            ]
        ];
        try {
            $curlOptions = [];
            $curlOptions['CURLOPT_CONNECTTIMEOUT'] = $this->wsTimeoutSeconds;
            $answerRaw = $this->curlRequest(
                $this->getCurrentUrl(),
                'post',
                json_encode($requestData, JSON_UNESCAPED_UNICODE),
                $curlOptions
            );
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
                $answer = $this->doRequest($apiName, $data, $answerFormat, $try_number + 1, false);
            } elseif ($this->resetTryNodes > 1) {
                $this->resetTryNodes = $this->resetTryNodes - 1;
                //if got WS Exception after few ties, connect to reserve node
                $this->setReserveNodeUrlToCurrentUrl();
                $answer = $this->doRequest($apiName, $data, $answerFormat, 1, false);
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
            if (!empty($temp['query'])) {
                $data = parse_str($temp['query']) + $data;
            }
            $temp['query'] = $data;

            $url = $this->makeUrlFromArray($temp);
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        foreach ($curlOptions as $option => $val) {
            curl_setopt($ch, constant($option), $val);
        }
        if (empty($curlOptions['CURLOPT_CONNECTTIMEOUT']) && empty($curlOptions['CURLOPT_CONNECTTIMEOUT_MS'])) {
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