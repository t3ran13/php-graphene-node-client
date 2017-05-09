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
        $this->setArrayElementByKey($this->params, $setKey, $setVal);
    }

    /**
     * @param CommandInterface $command
     * @return array
     */
    public function getCommandData(CommandInterface $command)
    {
        $queryData = [];
        $map = $command->getDataMap();
        foreach ($map as $route => $rules) {
            $this->setQueryDataForRoute($queryData, $route, $rules);
        }

        return $queryData;
    }


    /**
     * @param array $data
     * @param string $route
     * @param array $rules
     * @throws CommandQueryDataException
     */
    protected function setQueryDataForRoute(&$data, $route, $rules = [])
    {
        $errors = [];
        $routeParts = explode(':', $route);
        $values = self::getParamsListByKey($this->params, $routeParts);

        foreach ($values as $route => $value) {
            foreach ($rules as $rule) {
                if (!$this->validate($value, $rule)) {
                    $errors[] = 'Validation rule \'{$route}\' was failed for \'{$route}\';';
                }
            }
            if ($value !== null) {
                $this->setArrayElementByKey($data, $route, $value);
            }
        }

        if (!empty($errors)) {
            throw new CommandQueryDataException(implode(PHP_EOL, $errors));
        }
    }


    protected function validate($value, $rule)
    {
        if ($rule === 'required') {
            return $value === null ? false : true;
        } elseif ($rule === 'array') {
            return $value !== null && is_array($value);
        } elseif ($rule === 'string') {
            return $value !== null && is_string($value);
        } elseif ($rule === 'integer') {
            return $value !== null && is_integer($value);
        } elseif ($rule === 'nullOrArray') {
            return $value === null || is_array($value);
        } elseif ($rule === 'nullOrString') {
            return $value === null || is_string($value);
        } elseif ($rule === 'nullOrInteger') {
            return $value === null || is_integer($value);
        }
    }


    protected static function getParamsListByKey($params, $routeParts = [])
    {
        $values = [];
        if (empty($routeParts)) {
            $values[] = $params;
        } else {
            $currentKeyPart = array_shift($routeParts);
            if (
                is_numeric($currentKeyPart)
                && (string)((integer)$currentKeyPart) === $currentKeyPart
            ) {
                $currentKeyPart = (integer)$currentKeyPart;
            }
            if (isset($params[$currentKeyPart])) {
                foreach (self::getParamsListByKey($params[$currentKeyPart], $routeParts) as $valueKey => $value) {
                    $values[$currentKeyPart . ':' . $valueKey] = $value;
                }
            } elseif (is_array($params) && $currentKeyPart === '*') {
                foreach ($params as $paramKey => $param) {
                    foreach (self::getParamsListByKey($param, $routeParts) as $valueKey => $value) {
                        $values[$paramKey . ':' . $valueKey] = $value;
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
    protected function setArrayElementByKey(&$array, $setKey, $setVal)
    {
        $link = $array;
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