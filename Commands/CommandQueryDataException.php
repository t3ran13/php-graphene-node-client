<?php


namespace GrapheneNodeClient\Commands;


use \Exception;

class CommandQueryDataException extends Exception
{
    /** @var array */
    private $params = [];

//    /**
//     * @param CommandInterface $command
//     * @return array
//     */
//    public function getCommandData(CommandInterface $command)
//    {
//        $queryData = [];
//        $params = $this->params;
//        $map = $command->getDataMap();
//        foreach ($map as $route) {
//            $routeParts = explode(':', $route);
//            foreach ($routeParts as $key) {
//                if ($key === '*') {
//                    $data = $data;
//                } elseif (isset($data[$key])) {
//                    $data[] = $data[$key];
//                } else {
//                    $data = null;
//                    break;
//                }
//            }
//        }
//
//        return $this->params;
//    }


    public function getCommandData($data, $route, $rules)
    {
        $data = [];
        $routeParts = explode(':', $route);
        $values = self::getParamsListByKey($data, $routeParts);

        foreach ($values as $route => $value) {
            foreach ($rules as $rule) {
                $this->validate($value, $rule);
            }
            if ($value !== null) {
            }
        }

        return $this->params;
    }


    public function validate($value, $rule)
    {
        if ($rule === 'required') {
            if ($value === null) {

            }
        }
    }


    public function getParamsListByKey($params, $routeParts = [])
    {

    }
}