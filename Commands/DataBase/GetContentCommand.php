<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetContentCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_content';

    /** @var array */
    protected $requiredParams = [
        '0' => ['string'], //author
        '1' => ['string'], //permlink
    ];
}