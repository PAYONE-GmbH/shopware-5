<?php

use Shopware\Components\CSRFWhitelistAware;

/**
 * updated and finish transactions
 */
class Shopware_Controllers_Frontend_MoptAddressPayone extends Shopware_Controllers_Frontend_Address implements CSRFWhitelistAware
{

    /**
     * Pre dispatch method
     */
    public function preDispatch()
    {
        $this->admin = Shopware()->Modules()->Admin();

        $this->addressRepository = $this->get('models')->getRepository(Shopware\Models\Customer\Address::class);
        $this->addressService = $this->get('shopware_account.address_service');

        $this->View()->assign('sUserLoggedIn', $this->admin->sCheckUser());

        if (!$this->View()->getAssign('sUserLoggedIn')) {
            return $this->forward('index', 'register', 'frontend', $this->getForwardParameters());
        }

        $this->View()->assign([
            'sUserData' => $this->admin->sGetUserData(),
            'sAction' => $this->Request()->getActionName()
        ]);
    }

    /**
     * whitelists indexAction for SW 5.2 compatibility
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'edit',
        ];
    }
}
