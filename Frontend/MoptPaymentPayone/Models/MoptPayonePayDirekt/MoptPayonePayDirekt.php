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
     * @ORM\Column(name="locale_id", type="integer", unique=false)
     */
    protected $localeId;

    /**
     * @var Locale $locale
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Locale")
     * @ORM\JoinColumn(name="locale_id", referencedColumnName="id")
     */
    private $locale;

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
}