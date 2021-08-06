<?php


namespace BloomLand\Core\provider;


use BloomLand\Core\Core;

interface ProviderInterface
{

    /**
     * ProviderInterface constructor.
     * @param Core $core
     */
    public function __construct(Core $core);

    /**
     * @param int $id
     * @return bool
     */
    public function exists(int $id) : bool;

    /**
     * @param int $id
     * @return bool
     */
    public function new(int $id) : bool;

    /**
     * @param string $username
     * @return int
     */
    public function getCoins(string $username) : int;

    /**
     * @param string $username
     * @param int $count
     */
    public function setCoins(string $username, int $count) : void;

    /**
     * @return string
     */
    public function getName() : string;
}
