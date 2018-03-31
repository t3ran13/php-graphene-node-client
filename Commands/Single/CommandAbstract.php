<?php


namespace GrapheneNodeClient\Commands\Single;


use GrapheneNodeClient\Commands\CommandQueryDataInterface;
use GrapheneNodeClient\Commands\Commands;
use GrapheneNodeClient\Connectors\ConnectorInterface;

abstract class CommandAbstract
{
    /** @var string */
    protected $method = '';
    /** @var ConnectorInterface */
    protected $connector;


    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param CommandQueryDataInterface $commandQueryData
     * @param string $answerFormat
     * @param string $getElementWithKey If you want to get only certain element from answer.
     *                                  Example: 'key:123:qwe' => $array['key']['123']['qwe'] or $object->key->123->qwe
     * @return array|object
     */
    public function execute(CommandQueryDataInterface $commandQueryData, $getElementWithKey = null, $answerFormat = ConnectorInterface::ANSWER_FORMAT_ARRAY)
    {
        $commands = new Commands($this->connector);
        return $commands->{$this->method}()->execute($commandQueryData, $getElementWithKey, $answerFormat);
    }
}