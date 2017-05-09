<?php


namespace GrapheneNodeClient\Commands;


interface CommandQueryDataInterface
{
    /**
     * @return array
     */
    public function getParams();

    /**
     * @param $params
     */
    public function setParams($params);

    /**
     * set value in params by key
     *
     * $setKey example: 'key:1:one_more_key' => $params['key'][1]['one_more_key']
     * $setKey example: 'key:a1' => $params['key']['a1']
     *
     * @param string $setKey
     * @param mixed $setVal
     */
    public function setParamByKey($setKey, $setVal);

    /**
     * @param array $map
     * @return array
     */
    public function prepareData($map);
}
