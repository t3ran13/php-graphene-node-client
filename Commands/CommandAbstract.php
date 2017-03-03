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
     * @return array|object
     */
    public function execute($params = [], $answerFormat = ConnectorInterface::ANSWER_FORMAT_ARRAY)
    {
        $this->setParams($params);

        $this->checkRequiredParams($this->requiredParams, $this->params);


        return $this->doRequest($answerFormat);
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
}