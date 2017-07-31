<?php

use Shopware\Components\CSRFWhitelistAware;


/**
 * updated and finish transactions
 */
class Shopware_Controllers_Frontend_MoptAccountPayone extends Shopware_Controllers_Frontend_Account implements CSRFWhitelistAware
{

    /**
     * Pre dispatch method
     */
    public function preDispatch()
    {
        $this->View()->setScope(Enlight_Template_Manager::SCOPE_PARENT);
        $userData = $this->admin->sGetUserData();

        $activeBillingAddressId = $userData['additional']['user']['default_billing_address_id'];
        $activeShippingAddressId = $userData['additional']['user']['default_shipping_address_id'];

        $this->View()->assign('activeBillingAddressId', $activeBillingAddressId);
        $this->View()->assign('activeShippingAddressId', $activeShippingAddressId);
        $this->View()->assign('sUserData', $userData);
        $this->View()->assign('userInfo', $this->get('shopware_account.store_front_greeting_service')->fetch());
        $this->View()->assign('sUserLoggedIn', $this->admin->sCheckUser());
        $this->View()->assign('sAction', $this->Request()->getActionName());
    }

    /**
     * whitelists indexAction for SW 5.2 compatibility
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'savePayment'
        ];
    }

    /**
     * Save shipping action
     *
     * Save shipping address data
     */
    public function savePaymentAction()
    {
      parent::savePaymentAction();
    }

}
