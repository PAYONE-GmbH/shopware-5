<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayonePaypal;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Models\Shop\Shop;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_mopt_payone_paypal")
 */
class MoptPayonePaypal extends ModelEntity
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $shopId
     * @ORM\Column(name="shop_id", type="integer", unique=true)
     */
    protected $shopId;

    /**
     * @var Shop $shop
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Shop")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     */
    private $shop;

    /**
     * @ORM\Column(name="image", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $image;

    /**
     * @var
     * @ORM\Column(name="pack_station_mode", type="string", nullable=true)
     */
    protected $packStationMode;

    protected $buttons;

    public function __construct()
    {
        $this->buttons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * add button to collection
     *
     * @param \Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal $button
     */
    public function addButton(\Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal $button)
    {
        $this->buttons[] = $button;
    }

    /**
     * Set button collection
     *
     * @param $buttons
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;
    }

    /**
     * Get button collection
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getPackStationMode()
    {
        return $this->packStationMode;
    }

    /**
     * @param mixed $packStationMode
     */
    public function setPackStationMode($packStationMode)
    {
        $this->packStationMode = $packStationMode;
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function setShop(\Shopware\Models\Shop\Shop $shop)
    {
        $this->shop = $shop;
    }
}
