<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayonePayDirekt;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Models\Shop\Shop;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_mopt_payone_pay_direkt")
 */
class MoptPayonePayDirekt extends ModelEntity
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
     * @var
     * @ORM\Column(name="dispatch_id", type="integer", unique=false)
     */
    protected $dispatchId;

    /**
     * @var Dispatch $dispatch
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Dispatch\Dispatch")
     * @ORM\JoinColumn(name="dispatch_id", referencedColumnName="id")
     */
    private $dispatch;

    /**
     * @ORM\Column(name="image", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $image;

    /**
     * @var
     * @ORM\Column(name="pack_station_mode", type="string", nullable=true)
     */
    protected $packStationMode;

    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDispatch()
    {
        return $this->dispatch;
    }

    /**
     * @param mixed $dispatch
     */
    public function setDispatch($dispatch)
    {
        $this->dispatch = $dispatch;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return MoptPayonePayDirekt
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getDispatchId(){
        return $this->dispatchId;
    }

    /**
     * @param $dispatchId
     */
    public function setDispatchId($dispatchId)
    {
        $this->dispatchId = $dispatchId;
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