<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneAmazonPay;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_mopt_payone_amazon_pay")
 */
class MoptPayoneAmazonPay extends ModelEntity
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
     * @var
     * @ORM\Column(name="client_id", type="string", nullable=false)
     */
    protected $clientId;


    /**
     * @var
     * @ORM\Column(name="seller_id", type="string", nullable=false)
     */
    protected $sellerId;

    /**
     * @var
     * @ORM\Column(name="button_type", type="string", nullable=false)
     */
    protected $buttonType;

    /**
     * @var
     * @ORM\Column(name="button_color", type="string", nullable=false)
     */
    protected $buttonColor;

    /**
     * @var
     * @ORM\Column(name="button_language", type="string", nullable=false)
     */
    protected $buttonLanguage;

    /**
     * @var
     * @ORM\Column(name="amazon_mode", type="string", nullable=false)
     */
    protected $amazonMode;

    /**
     * @var
     * @ORM\Column(name="pack_station_mode", type="string", nullable=false)
     */
    protected $packStationMode;

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
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return mixed
     */
    public function getSellerId()
    {
        return $this->sellerId;
    }

    /**
     * @param mixed $sellerId
     */
    public function setSellerId($sellerId)
    {
        $this->sellerId = $sellerId;
    }

    /**
     * @return mixed
     */
    public function getButtonType()
    {
        return $this->buttonType;
    }

    /**
     * @param mixed $buttonType
     */
    public function setButtonType($buttonType)
    {
        $this->buttonType = $buttonType;
    }

    /**
     * @return mixed
     */
    public function getButtonColor()
    {
        return $this->buttonColor;
    }

    /**
     * @param mixed $buttonColor
     */
    public function setButtonColor($buttonColor)
    {
        $this->buttonColor = $buttonColor;
    }

    /**
     * @return mixed
     */
    public function getButtonLanguage()
    {
        return $this->buttonLanguage;
    }

    /**
     * @param mixed $buttonLanguage
     */
    public function setButtonLanguage($buttonLanguage)
    {
        $this->buttonLanguage = $buttonLanguage;
    }

    /**
     * @return mixed
     */
    public function getAmazonMode()
    {
        return $this->amazonMode;
    }

    /**
     * @param mixed $amazonMode
     */
    public function setAmazonMode($amazonMode)
    {
        $this->amazonMode = $amazonMode;
    }

    /**
     * @return mixed
     */
    public function getPackStationMode()
    {
        return $this->packStationMode;
    }

    /**
     * @param $packStationMode
     */
    public function setPackStationMode($packStationMode)
    {
        $this->packStationMode = $packStationMode;
    }



}
