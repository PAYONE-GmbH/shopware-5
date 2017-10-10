<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayonePaypal;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

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
     * @var
     * @ORM\Column(name="locale_id", type="integer", unique=true)
     */
    protected $localeId;

    /**
     * @var Locale $locale
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Locale")
     * @ORM\JoinColumn(name="locale_id", referencedColumnName="id")
     */
    private $locale;

    /**
     * @ORM\Column(name="image", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $image;

    /**
     * @ORM\Column(name="is_default", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $isDefault;

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
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;
        return $this;
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

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getIsDefault()
    {
        return $this->isDefault;
    }

    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }
}
