<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneApiLog;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_mopt_payone_api_log")
 */
class MoptPayoneApiLog extends ModelEntity
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
   * @ORM\Column(name="request", type="string", length=100, precision=0, scale=0, nullable=false, unique=false)
   */
    private $request;

  /**
   * @ORM\Column(name="response", type="string", length=100, precision=0, scale=0, nullable=false, unique=false)
   */
    private $response;

  /**
   * @ORM\Column(name="live_mode", type="boolean", precision=0, scale=0, nullable=false, unique=false)
   */
    private $liveMode;

  /**
   * @ORM\Column(name="merchant_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
   */
    private $merchantId;

  /**
   * @ORM\Column(name="portal_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
   */
    private $portalId;

  /**
   * @ORM\Column(name="creation_date", type="datetime", precision=0, scale=0, nullable=false, unique=false)
   */
    private $creationDate;

  /**
   * @ORM\Column(name="request_details", type="array", precision=0, scale=0, nullable=false, unique=false)
   */
    private $requestDetails;

  /**
   * @ORM\Column(name="response_details", type="array", precision=0, scale=0, nullable=false, unique=false)
   */
    private $responseDetails;

    /**
     * @var string $transactionId
     * @ORM\Column(name="transaction_id", length=255, type="string", nullable=true)
     */
    private $transactionId;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection
   */
    private $apiLogs;

    public function __construct()
    {
        $this->apiLogs = new \Doctrine\Common\Collections\ArrayCollection();
    }

  /**
   * add apiLog to collection
   *
   * @param \Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog $apiLog
   */
    public function addApiLog(\Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog $apiLog)
    {
        $this->apiLogs[] = $apiLog;
    }

  /**
   * Set apiLogs collection
   *
   * @param $apiLogs
   *
   * @return \Doctrine\Common\Collections\Collection
   */
    public function setApiLogs($apiLogs)
    {
        $this->apiLogs = $apiLogs;
        return $this;
    }

  /**
   * Get apiLogs collection
   *
   * @return \Doctrine\Common\Collections\Collection
   */
    public function getApiLogs()
    {
        return $this->apiLogs;
    }
  
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getLiveMode()
    {
        return $this->liveMode;
    }

    public function setLiveMode($liveMode)
    {
        $this->liveMode = $liveMode;
    }

    public function getMerchantId()
    {
        return $this->merchantId;
    }

    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    public function getPortalId()
    {
        return $this->portalId;
    }

    public function setPortalId($portalId)
    {
        $this->portalId = $portalId;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    public function getRequestDetails()
    {
        return $this->requestDetails;
    }

    public function setRequestDetails($requestDetails)
    {
        $this->requestDetails = $requestDetails;
    }

    public function getResponseDetails()
    {
        return $this->responseDetails;
    }

    public function setResponseDetails($responseDetails)
    {
        $this->responseDetails = $responseDetails;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }
}
