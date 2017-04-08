<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

abstract class CommandAbstract implements CommandInterface
{
    /** @var array */
    private $params = [];
    /** @var string */
    protected $method = '';
    /** @var array */
    protected $requiredParams = [];
    /** @var ConnectorInterface */
    protected $connector;

    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param array $params
     * @param string $answerFormat
     * @param string $getElementWithKey If you want to get only certain element from answer.
     *                                  Example: 'key:123:qwe' => $array['key']['123']['qwe'] or $object->key->123->qwe
     * @return array|object
     */
    public function execute($params = [], $getElementWithKey = null, $answerFormat = ConnectorInterface::ANSWER_FORMAT_ARRAY)
    {
        $this->setParams($params);

        $requiredParams = isset($this->requiredParams[$this->connector->getPlatform()])
            ? $this->requiredParams[$this->connector->getPlatform()]
            : $this->requiredParams;
        $this->checkRequiredParams($requiredParams, $this->params);

        $answer = $this->doRequest($answerFormat);

        $defaultValue = $answerFormat === ConnectorInterface::ANSWER_FORMAT_ARRAY ? [] : ((object)[]);

        return $this->getElementByKey($answer, $getElementWithKey, $defaultValue);
    }

    /**
     * @param string $name
     * @param mixed $val
     */
    protected function setParam($name, $val)
    {
        $this->params[$name] = $val;
    }

    /**
     * @param array $params
     */
    protected function setParams($params = [])
    {
        foreach ($params as $param => $val) {
            $this->setParam($param, $val);
        }
    }

    /**
     * @param $haveTo
     * @param $data
     * @return bool
     */
    public function isSetRequiredParams($haveTo, $data)
    {
        foreach ($haveTo as $param) {
            if (is_array($param)) {
                foreach ($data as $d) {
                    if (!self::isSetRequiredParams($param, $d)) {
                        return false;
                    }
                }
            } elseif (!is_array($param) && !isset($data[$param])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param [] $haveTo
     * @param [] $data
     *
     * @throws \Exception
     */
    protected function checkRequiredParams($haveTo, $data)
    {
        foreach ($haveTo as $param) {
            if (is_array($param)) {
                foreach ($data as $d) {
                    if (!self::isSetRequiredParams($param, $d)) {
                        throw new \Exception('METHOD ' . $this->method . ' should to be have "' . implode('","', $param) . '" params, there are ' . print_r($d, true));
                    }
                }
            } elseif (!is_array($param) && !isset($data[$param])) {
                throw new \Exception('METHOD ' . $this->method . ' should to be have "' . implode('","', $haveTo) . '" params, there are ' . print_r($data, true));
            }
        }
    }

    /**
     * @param string $answerFormat
     * @return array|object
     */
    protected function doRequest($answerFormat = ConnectorInterface::ANSWER_FORMAT_ARRAY)
    {
        $data = [
            'method' => $this->method,
            'params' => $this->params
        ];

        return $this->connector->doRequest($data, $answerFormat);
    }


    /**
     * get all values or vulue by key
     *
     * Example: 'key:123:qwe' => $array['key']['123']['qwe'] or $object->key->123->qwe
     *
     * @param null|string $getKey
     * @param null|mixed $default
     * @param array|object $array
     *
     * @return mixed
     */
    protected function getElementByKey($array, $getKey = null, $default = null)
    {
        $data = $array;
        if ($getKey) {
            $keyParts = explode(':', $getKey);
            foreach ($keyParts as $key) {
                if (is_array($data) && isset($data[$key])) {
                    $data = $data[$key];
                } elseif (is_object($data) && isset($data->$key)) {
                    $data = $data->$key;
                } else {
                    $data = null;
                    break;
                }
            }
        }

        if ($data === null) {
            $data = $default;
        }

        return $data;
    }
}