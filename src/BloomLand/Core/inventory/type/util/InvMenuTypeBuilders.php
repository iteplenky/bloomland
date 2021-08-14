<?php


namespace BloomLand\Core\inventory\type\util;


use BloomLand\Core\inventory\type\util\builder\BlockActorFixedInvMenuTypeBuilder;
use BloomLand\Core\inventory\type\util\builder\BlockFixedInvMenuTypeBuilder;
use BloomLand\Core\inventory\type\util\builder\DoublePairableBlockActorFixedInvMenuTypeBuilder;

final class InvMenuTypeBuilders
{

    /**
     * @return BlockActorFixedInvMenuTypeBuilder
     */
    public static function BLOCK_ACTOR_FIXED() : BlockActorFixedInvMenuTypeBuilder
    {
		return new BlockActorFixedInvMenuTypeBuilder();
	}

    /**
     * @return BlockFixedInvMenuTypeBuilder
     */
    public static function BLOCK_FIXED() : BlockFixedInvMenuTypeBuilder
    {
		return new BlockFixedInvMenuTypeBuilder();
	}

    /**
     * @return DoublePairableBlockActorFixedInvMenuTypeBuilder
     */
    public static function DOUBLE_PAIRABLE_BLOCK_ACTOR_FIXED() : DoublePairableBlockActorFixedInvMenuTypeBuilder
    {
		return new DoublePairableBlockActorFixedInvMenuTypeBuilder();
	}
}