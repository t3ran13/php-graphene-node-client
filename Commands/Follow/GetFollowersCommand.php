<?php


namespace GrapheneNodeClient\Commands\Follow;


class GetFollowersCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_followers';

    /** @var array */
    protected $queryDataMap = [
        '0' => ['string'], //author
        '1' => ['nullOrString'], //startFollower
        '2' => ['string'], //followType //blog, ignore
        '3' => ['integer'], //limit
    ];
}