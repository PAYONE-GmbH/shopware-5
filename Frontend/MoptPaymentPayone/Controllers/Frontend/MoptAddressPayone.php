<?php

use Shopware\Components\CSRFWhitelistAware;
use Shopware\Bundle\AccountBundle\Form\Account\AddressFormType;
use Shopware\Bundle\AccountBundle\Service\AddressServiceInterface;
use Shopware\Models\Customer\Address;
use Shopware\Models\Customer\AddressRepository;
use Shopware\Models\Customer\Customer;
use Symfony\Component\Form\FormInterface;

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

        $this->View()->assign('sUserData', $this->admin->sGetUserData());
        $this->View()->assign('sAction', $this->Request()->getActionName());
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



    /**
     * edit action for changing addresses for guest users
     *
     * @return mixed
     */
    public function editAction()
    {
      parent::editAction();
    }


}
