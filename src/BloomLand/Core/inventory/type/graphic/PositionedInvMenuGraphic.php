<?php


namespace BloomLand\Core\inventory\type\graphic;


use pocketmine\math\Vector3;

interface PositionedInvMenuGraphic extends InvMenuGraphic
{

    /**
     * @return Vector3
     */
    public function getPosition() : Vector3;
}