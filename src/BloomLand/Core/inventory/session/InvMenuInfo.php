<?php


namespace BloomLand\Core\inventory\session;


use BloomLand\Core\inventory\InvMenu;
use BloomLand\Core\inventory\type\graphic\InvMenuGraphic;

final class InvMenuInfo
{

    /**
     * @var InvMenu
     */
    public InvMenu $menu;

    /**
     * @var InvMenuGraphic
     */
    public InvMenuGraphic $graphic;

    /**
     * InvMenuInfo constructor.
     * @param InvMenu $menu
     * @param InvMenuGraphic $graphic
     */
    public function __construct(InvMenu $menu, InvMenuGraphic $graphic)
    {
        $this->menu = $menu;
        $this->graphic = $graphic;
    }
}
