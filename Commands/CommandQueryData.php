<?php


namespace GrapheneNodeClient\Commands;


class CommandQueryData implements CommandQueryDataInterface
{
    /** @var array */
    protected $params = [];

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * set value in params by key
     *
     * $setKey example: 'key:1:one_more_key' => $params['key'][1]['one_more_key']
     * $setKey example: 'key:a1' => $params['key']['a1']
     *
     * @param string $setKey
     * @param mixed $setVal
     */
    public function setParamByKey($setKey, $setVal)
    {
        $this->params = $this->setArrayElementByKey($this->params, $setKey, $setVal);
    }

    /**
     * @param array $map
     * @return array
     */
    public function prepareData($map)
    {
        $queryData = [];
        foreach ($map as $route => $rules) {
            $queryData = $this->prepareQueryDataForRoute($queryData, $route, $rules);
        }

        return $queryData;
    }


    /**
     * @param array $data
     * @param string $fullRoute
     * @param array $rules
     *
     * @return mixed
     *
     * @throws CommandQueryDataException
     */
    protected function prepareQueryDataForRoute($data, $fullRoute, $rules = [])
    {
        $errors = [];
        $routeParts = explode(':', $fullRoute);
        $values = $this->getParamsListByKey($this->params, $routeParts);

        foreach ($values as $route => $value) {
            foreach ($rules as $rule) {
                if (!$this->validate($value, $rule)) {
                    $errors[] = 'Validation rule \'' . $rule . '\' was failed for route \'' . $route . '\' with value ' . $value . ';';
                }
            }
            if ($value !== null) {
                $data = $this->setArrayElementByKey($data, $route, $value);
            }
        }

        if (!empty($errors)) {
            throw new CommandQueryDataException(implode(PHP_EOL, $errors));
        }

        return $data;
    }


    /**
     * @param mixed $value
     * @param string $rule
     * @return bool
     */
    protected function validate($value, $rule)
    {
        if ($rule === 'required') {
            return $value === null ? false : true;
        } elseif ($rule === 'bool') {
            return  $value !== null && is_bool($value);
        } elseif ($rule === 'array') {
            return $value !== null && is_array($value);
        } elseif ($rule === 'string') {
            return $value !== null && is_string($value);
        } elseif ($rule === 'integer') {
            return $value !== null && is_int($value);
        } elseif ($rule === 'nullOrBool') {
            return $value === null || is_bool($value);
        } elseif ($rule === 'nullOrArray') {
            return $value === null || is_array($value);
        } elseif ($rule === 'nullOrString') {
            return $value === null || is_string($value);
        } elseif ($rule === 'nullOrInteger') {
            return $value === null || is_int($value);
        }
    }


    /**
     * @param array $params
     * @param array $routeParts
     * @return array
     */
    protected function getParamsListByKey($params, $routeParts = [])
    {
        $values = [];
        if (empty($routeParts)) {
            $values = $params;
        } else {
            $currentKeyPart = array_shift($routeParts);
            if (
                is_numeric($currentKeyPart)
                && (string)((integer)$currentKeyPart) === $currentKeyPart
            ) {
                $currentKeyPart = (integer)$currentKeyPart;
            }
            if (isset($params[$currentKeyPart])) {
                $tmp = $this->getParamsListByKey($params[$currentKeyPart], $routeParts);
                if (is_array($tmp) && !empty($routeParts)) {
                    foreach ($tmp as $valueKey => $value) {
                        $values[$currentKeyPart . ':' . $valueKey] = $value;
                    }
                } else {
                    $values[$currentKeyPart] = $tmp;
                }
            } elseif (is_array($params) && $currentKeyPart === '*') {
                foreach ($params as $paramKey => $param) {
                    $tmp = $this->getParamsListByKey($param, $routeParts);
                    if (is_array($tmp)) {
                        foreach ($this->getParamsListByKey($param, $routeParts) as $valueKey => $value) {
                            $values[$paramKey . ':' . $valueKey] = $value;
                        }
                    } else {
                        $values[$paramKey] = $tmp;
                    }
                }
            } else {
                $values[implode(':', array_merge([$currentKeyPart], $routeParts))] = null;
            }
        }

        return $values;
    }


    /**
     * set value in array by key
     *
     * $setKey example: 'key:123:array' => $_SESSION['key'][123]['array']
     *
     * @param array $array
     * @param string $setKey
     * @param mixed  $setVal
     *
     * @return array
     */
    protected function setArrayElementByKey($array, $setKey, $setVal)
    {
        $link = &$array;
        $keyParts = explode(':', $setKey);
        foreach ($keyParts as $key) {
            if (
                is_numeric($key)
                && (string)((integer)$key) === $key
            ) {
                $key = (integer)$key;
            }
            if (!isset($link[$key])) {
                $link[$key] = [];
            }
            $link = &$link[$key];
        }
        $link = $setVal;

        return $array;
    }
}