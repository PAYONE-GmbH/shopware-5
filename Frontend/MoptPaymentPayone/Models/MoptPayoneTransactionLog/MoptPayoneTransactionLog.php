<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneTransactionLog;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="s_plugin_mopt_payone_transaction_log")
 * @ORM\Entity(repositoryClass="Repository")
 */
class MoptPayoneTransactionLog extends ModelEntity
{

  /**
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
    private $id;

  /**
   * @ORM\Column(name="transaction_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
   */
    private $transactionId;

  /**
   * @ORM\Column(name="order_nr", type="string", length=100, unique=false)
   */
    private $orderNr;

  /**
   * @ORM\Column(name="status", type="string", length=100, precision=0, scale=0, nullable=false, unique=false)
   */
    private $status;

  /**
   * @ORM\Column(name="transaction_date", type="datetime", precision=0, scale=0, nullable=false, unique=false)
   */
    private $transactionDate;

  /**
   * @ORM\Column(name="sequence_nr", type="integer", precision=0, scale=0, nullable=false, unique=false)
   */
    private $sequenceNr;

  /**
   * @ORM\Column(name="payment_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
   */
    private $paymentId;

  /**
   * @ORM\Column(name="live_mode", type="boolean", precision=0, scale=0, nullable=false, unique=false)
   */
    private $liveMode;

  /**
   * @ORM\Column(name="portal_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
   */
    private $portalId;

  /**
   * @ORM\Column(name="claim", type="decimal", nullable=false, unique=false)
   */
    private $claim;

  /**
   * @ORM\Column(name="balance", type="decimal", nullable=false, unique=false)
   */
    private $balance;

  /**
   * @ORM\Column(name="creation_date", type="datetime", precision=0, scale=0, nullable=false, unique=false)
   */
    private $creationDate;

  /**
   * @ORM\Column(name="update_date", type="datetime", precision=0, scale=0, nullable=false, unique=false)
   */
    private $updateDate;

  /**
   * @ORM\Column(name="details", type="array", precision=0, scale=0, nullable=false, unique=false)
   */
    private $details;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection
   */
    private $transactionLogs;

    public function __construct()
    {
        $this->transactionLogs = new \Doctrine\Common\Collections\ArrayCollection();
    }

  /**
   * Add transactionLog
   *
   * @param \Shopware\Models\Article\Article $articles
   */
    public function addTransaction(\Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog $transaction)
    {
        $this->transactionLogs[] = $transaction;
    }

  /**
   * Set transactionLogs
   *
   * @param $articles
   *
   * @return \Doctrine\Common\Collections\Collection
   */
    public function setTransactions($transactions)
    {
        $this->transactionLogs = $transactions;
        return $this;
    }

  /**
   * Get transactionLogs
   *
   * @return \Doctrine\Common\Collections\Collection
   */
    public function getTransactions()
    {
        return $this->transactionLogs;
    }
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    public function getOrderNr()
    {
        return $this->orderNr;
    }

    public function setOrderNr($orderNr)
    {
        $this->orderNr = $orderNr;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;
    }

    public function getSequenceNr()
    {
        return $this->sequenceNr;
    }

    public function setSequenceNr($sequenceNr)
    {
        $this->sequenceNr = $sequenceNr;
    }

    public function getPaymentId()
    {
        return $this->paymentId;
    }

    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function getLiveMode()
    {
        return $this->liveMode;
    }

    public function setLiveMode($liveMode)
    {
        $this->liveMode = $liveMode;
    }

    public function getPortalId()
    {
        return $this->portalId;
    }

    public function setPortalId($portalId)
    {
        $this->portalId = $portalId;
    }

    public function getClaim()
    {
        return $this->claim;
    }

    public function setClaim($claim)
    {
        $this->claim = $claim;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function setDetails($details)
    {
        $this->details = $details;
    }

    public function getTransactionLogs()
    {
        return $this->transactionLogs;
    }

    public function setTransactionLogs($transactionLogs)
    {
        $this->transactionLogs = $transactionLogs;
    }
}
