<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayonePayDirekt;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

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
    public function addButton(\Shopware\CustomModels\MoptPayonePaydirekt\MoptPayonePayDirekt $button)
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

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return MoptPayonePayDirekt
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocaleId()
    {
        return $this->localeId;
    }

    /**
     * @param mixed $localeId
     * @return MoptPayonePayDirekt
     */
    public function setLocaleId($localeId)
    {
        $this->localeId = $localeId;
        return $this;
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
        return $this;
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