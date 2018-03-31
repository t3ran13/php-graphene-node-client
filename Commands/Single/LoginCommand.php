<?php


namespace GrapheneNodeClient\Commands\Single;

/**
 * ONLY for STEEM
 * This must be called prior to requesting other APIs. Other APIs may not be accessible until the client has
 * sucessfully authenticated.
 */
class LoginCommand extends CommandAbstract
{
    protected $method       = 'login';
}