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



}