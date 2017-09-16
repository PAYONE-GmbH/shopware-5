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

        $this->View()->assign([
            'activeBillingAddressId' => $activeBillingAddressId,
            'activeShippingAddressId' => $activeShippingAddressId,
            'sUserData' => $userData,
            'sUserLoggedIn' => $this->admin->sCheckUser(),
            'sAction' => $this->Request()->getActionName()
        ]);
    }

    /**
     * whitelists indexAction for SW 5.2 compatibility
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'savePayment',
            'payment'
        ];
    }
}
