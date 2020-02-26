<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneTransactionForwardQueue;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="s_plugin_mopt_payone_transaction_forward_queue")
 * @ORM\Entity(repositoryClass="Repository")
 */
class MoptPayoneTransactionForwardQueue extends ModelEntity
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
     * @ORM\Column(name="request", type="text", nullable=false, unique=false)
     */
    private $request;

    /**
     * @ORM\Column(name="response", type="text", nullable=false, unique=false)
     */
    private $response;

    /**
     * @ORM\Column(name="endpoint", type="text", nullable=false, unique=false)
     */
    private $endpoint;

    /**
     * @ORM\Column(name="numtries", type="integer", nullable=false)
     */
    private $numtries;

    /**
     * @ORM\Column(name="json_post", type="text", nullable=false, unique=false)
     */
    private $jsonPost;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param mixed $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param mixed $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return mixed
     */
    public function getNumtries()
    {
        return $this->numtries;
    }

    /**
     * @param mixed $numtries
     */
    public function setNumtries($numtries)
    {
        $this->numtries = $numtries;
    }

    /**
     * @return mixed
     */
    public function getJsonPost()
    {
        return $this->jsonPost;
    }

    /**
     * @param mixed $jsonPost
     */
    public function setJsonPost($jsonPost)
    {
        $this->jsonPost = $jsonPost;
    }


}
