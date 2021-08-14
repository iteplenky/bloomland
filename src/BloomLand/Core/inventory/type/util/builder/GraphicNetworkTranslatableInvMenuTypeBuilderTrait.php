<?php


namespace BloomLand\Core\inventory\type\util\builder;


use BloomLand\Core\inventory\type\graphic\network\InvMenuGraphicNetworkTranslator;
use BloomLand\Core\inventory\type\graphic\network\MultiInvMenuGraphicNetworkTranslator;
use BloomLand\Core\inventory\type\graphic\network\WindowTypeInvMenuGraphicNetworkTranslator;
use JetBrains\PhpStorm\Pure;

trait GraphicNetworkTranslatableInvMenuTypeBuilderTrait
{

    /**
     * @var array
     */
    private array $graphic_network_translators = [];

    /**
     * @param InvMenuGraphicNetworkTranslator $translator
     * @return $this
     */
    public function addGraphicNetworkTranslator(InvMenuGraphicNetworkTranslator $translator) : self
    {
        $this->graphic_network_translators[] = $translator;
        return $this;
    }

    /**
     * @param int $window_type
     * @return $this
     */
    public function setNetworkWindowType(int $window_type) : self
    {
        $this->addGraphicNetworkTranslator(new WindowTypeInvMenuGraphicNetworkTranslator($window_type));
        return $this;
    }

    /**
     * @return InvMenuGraphicNetworkTranslator|null
     */
    #[Pure]
    protected function getGraphicNetworkTranslator() : ?InvMenuGraphicNetworkTranslator
    {
        if (count($this->graphic_network_translators) === 0) {
            return null;
        }

        if (count($this->graphic_network_translators) === 1) {
            return $this->graphic_network_translators[array_key_first($this->graphic_network_translators)];
        }

        return new MultiInvMenuGraphicNetworkTranslator($this->graphic_network_translators);
    }
}
