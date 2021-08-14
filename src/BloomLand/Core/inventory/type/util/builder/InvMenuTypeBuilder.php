<?php


namespace BloomLand\Core\inventory\type\util\builder;


use BloomLand\Core\inventory\type\InvMenuType;

interface InvMenuTypeBuilder
{

    /**
     * @return InvMenuType
     */
    public function build() : InvMenuType;
}