<?php

/**
 * $Id: $
 */
class Mopt_PayoneMain
{

    const SCORING_GREEN         = 500;
    const SCORING_YELLOW        = 300;
    const SCORING_RED           = 100;
    const TRAFFIC_LIGHT__GREEN  = 1;
    const TRAFFIC_LIGHT__YELLOW = 2;
    const TRAFFIC_LIGHT__RED    = 3;

  /**
   * Mopt_PayoneMain instance
   * @var Mopt_PayoneMain
   */
    static protected $instance = null;

  /**
   * Payone Config
   * @var array
   */
    protected $payoneConfig = [];

  /**
   * Payone ParamBuilder
   * @var Mopt_PayoneParamBuilder
   */
    protected $paramBuilder = null;

  /**
   * Payone FormHandler
   * @var Mopt_PayoneFormHandler
   */
    protected $formHandler = null;

  /**
   * Payone Helper
   * @var Mopt_PayoneHelper
   */
    protected $helper = null;

  /**
   * Payone Payment Helper
   * @var Mopt_PayonePaymentHelper
   */
    protected $paymentHelper = null;

    /**
     * @var bool $basketUpdated
     */
    protected $basketUpdated = false;

    /**
     * Payone User Helper
     * @var Mopt_PayoneUserHelper
     */
    protected $userHelper = null;

  /**
   * singleton accessor
   *
   * @return Mopt_PayoneMain
   */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Mopt_PayoneMain();
        }
        return self::$instance;
    }

  /**
   * returns config according to submitted payment id
   * returns global config if no payment id is submitted
   *
   * @param int $paymentId
   * @param bool $forceReload
   * @param bool $asArray
   * @return array
   */
    public function getPayoneConfig($paymentId = 0, $forceReload = false, $asArray = true)
    {
        if (is_null($paymentId)) {
            $paymentId = 0;
        }
    
        if (!empty($this->payoneConfig[$paymentId]) && !$forceReload) {
            return $this->payoneConfig[$paymentId];
        }

        /** @var \Shopware\CustomModels\MoptPayoneConfig\Repository $repository */
        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig');
        $data       = $repository->getConfigByPaymentId($paymentId, $asArray);

        if ($data === null) {
            $data = array();
            $data['paymentId'] = $paymentId;
        }

        return $this->payoneConfig[$paymentId] = $data;
    }

  /**
   * param builder getter
   *
   * @return Mopt_PayoneParamBuilder
   */
    public function getParamBuilder()
    {
        if (is_null($this->paramBuilder)) {
            $this->paramBuilder = new Mopt_PayoneParamBuilder(
                $this->payoneConfig,
                $this->getHelper(),
                $this->getPaymentHelper()
            );
        }
        return $this->paramBuilder;
    }

  /**
   * getter method for feedback handler
   *
   * @return Mopt_PayoneFormHandler
   */
    public function getFormHandler()
    {
        if (is_null($this->formHandler)) {
            $this->formHandler = new Mopt_PayoneFormHandler();
        }
        return $this->formHandler;
    }

  /**
   * getter method for helper
   *
   * @return Mopt_PayoneHelper
   */
    public function getHelper()
    {
        if (is_null($this->helper)) {
            $this->helper = new Mopt_PayoneHelper();
        }
        return $this->helper;
    }

  /**
   * getter method for payment helper
   *
   * @return Mopt_PayonePaymentHelper
   */
    public function getPaymentHelper()
    {
        if (is_null($this->paymentHelper)) {
            $this->paymentHelper = new Mopt_PayonePaymentHelper();
        }

        return $this->paymentHelper;
    }

    /**
     * getter method for user helper
     *
     * @return Mopt_PayoneUserHelper
     */
    public function getUserHelper()
    {
        if (is_null($this->userHelper)) {
            $this->userHelper = new Mopt_PayoneUserHelper();
        }

        return $this->userHelper;
    }

    /**
     * Returns all basket data (and triggers an update if required)
     *
     * @return array
     */
    public function sGetBasket()
    {
        if ($this->basketUpdated === true) {
            // GH issue #145: sGetBasketData() is only available on SW >= 5.2.0
            if (\Shopware::VERSION === '___VERSION___' ||
                version_compare(\Shopware::VERSION, '5.2.0', '>=')
            ) {
                return Shopware()->Modules()->Basket()->sGetBasketData();
            } else {
                return Shopware()->Modules()->Basket()->sGetBasket();
            }
        } else {
            return Shopware()->Modules()->Basket()->sGetBasket();
        }
    }

    /**
     * @return bool
     */
    public function isBasketUpdated()
    {
        return $this->basketUpdated;
    }

    /**
     * @param bool $basketUpdated
     */
    public function setBasketUpdated($basketUpdated)
    {
        $this->basketUpdated = $basketUpdated;
    }

    public function reserveOrdernumber()
    {
        $session = Shopware()->Session();
        if (empty($session['moptReservedOrdernum'])){
            $order = new sOrder();
            $session['moptReservedOrdernum'] = $order->sGetOrderNumber();
        }
        return $session['moptReservedOrdernum'];
    }

}
