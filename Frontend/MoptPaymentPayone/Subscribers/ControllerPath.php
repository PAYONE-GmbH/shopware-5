<?php

/* 
 * manage custom controllers
 */

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

/**
 * provide paths to custom controllers
 */
class ControllerPath implements SubscriberInterface
{
    
    /**
     * path to plugin files
     * 
     * @var string
     */
    private $path;
    
    /**
     * inject path to plugin files
     * 
     * @param type $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }
    
    /**
     * return array with all subsribed events
     * 
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
    
            //Frontend
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_MoptPaymentPayone'
            => 'onGetControllerPathFrontendMoptPaymentPayone',
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_MoptShopNotification'
            => 'moptRegisterController_Frontend_MoptShopNotification',
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_MoptPaymentEcs'
            => 'moptRegisterController_Frontend_MoptPaymentEcs',
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_MoptAjaxPayone'
            => 'moptRegisterController_Frontend_MoptAjaxPayone',
            //Backend
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptConfigPayone' => 'onGetConfigControllerBackend',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptApilogPayone' => 'onGetApilogControllerBackend',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayoneTransactionLog'
            => 'onGetTransactionLogControllerBackend',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptSupportPayone' => 'onGetSupportControllerBackend',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayoneOrder'
            => 'moptRegisterController_Backend_MoptPayoneOrder',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayonePayment'
            => 'moptRegisterController_Backend_MoptPayonePayment',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptExportPayone' => 'onGetBackendExportController',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayonePaypal' => 'onGetBackendControllerPaypal',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayoneCreditcardConfig' 
            => 'onGetBackendControllerCreditcardConfig',
        );
    }
    
    /**
    * Returns the path to a frontend controller for an event.
    *
    * @param Enlight_Event_EventArgs $args
    * @return string
    */
    public function onGetControllerPathFrontendMoptPaymentPayone()
    {
      return $this->path . '/Controllers/Frontend/MoptPaymentPayone.php';
    }
    
    /**
    * controller callback, return path to controller file
    * 
    * @return string
    */
    public function moptRegisterController_Frontend_MoptPaymentEcs()
    {
      return $this->path . 'Controllers/Frontend/MoptPaymentEcs.php';
    }
    
    /**
    * controller callback, return path to controller file
    * 
    * @return string
    */
    public function moptRegisterController_Frontend_MoptAjaxPayone()
    {
      return $this->path . 'Controllers/Frontend/MoptAjaxPayone.php';
    }
    
    /**
    * controller callback, return path to controller file
    * 
    * @return string
    */
    public function moptRegisterController_Frontend_MoptShopNotification()
    {
      return $this->path . 'Controllers/Frontend/MoptShopNotification.php';
    }
    
    /**
    * Returns the path to a backend controller for an event.
    *
    * @return string
    */
    public function onGetConfigControllerBackend()
    {
      return $this->path . 'Controllers/Backend/MoptConfigPayone.php';
    }
    
      /**
   * Returns the path to a backend controller for an event.
   *
   * @return string
   */
  public function onGetApilogControllerBackend()
  {
    return $this->path . 'Controllers/Backend/MoptApilogPayone.php';
  }

  public function onGetTransactionLogControllerBackend()
  {
    return $this->path . 'Controllers/Backend/MoptPayoneTransactionLog.php';
  }

  /**
   * Returns the path to a backend controller for an event.
   *
   * @return string
   */
  public function onGetSupportControllerBackend()
  {
    return $this->path . 'Controllers/Backend/MoptSupportPayone.php';
  }

  /**
   * Returns the path to a backend controller for an event.
   *
   * @return string
   */
  public function onGetBackendExportController()
  {
    return $this->path . 'Controllers/Backend/MoptExportPayone.php';
  }
  
    /**
   * controller callback, return path to controller file
   * 
   * @return string
   */
  public function moptRegisterController_Backend_MoptPayoneOrder()
  {
    return $this->path . 'Controllers/Backend/MoptPayoneOrder.php';
  }

  /**
   * controller callback, return path to controller file
   * 
   * @return string
   */
  public function moptRegisterController_Backend_MoptPayonePayment()
  {
    return $this->path . 'Controllers/Backend/MoptPayonePayment.php';
  }
  
  /**
   * Returns the path to a backend controller for an event.
   *
   * @return string
   */
  public function onGetBackendControllerPaypal()
  {
    return $this->path . 'Controllers/Backend/MoptPayonePaypal.php';
  }
  
  /**
   * Returns the path to a backend controller for an event.
   *
   * @return string
   */
  public function onGetBackendControllerCreditcardConfig()
  {
    return $this->path . 'Controllers/Backend/MoptPayoneCreditcardConfig.php';
  }

}
