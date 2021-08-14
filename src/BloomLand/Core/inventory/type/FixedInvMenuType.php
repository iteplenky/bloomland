<?php


namespace BloomLand\Core\inventory\type;


interface FixedInvMenuType extends InvMenuType
{

    /**
     * @return int
     */
    public function getSize() : int;
}