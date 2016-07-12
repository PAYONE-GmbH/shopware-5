<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This class configures:
 * installment, uninstallment, updates, hooks, events, payment methods
 *
 *
 * PHP version 5
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 3)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone Payment Plugin for Shopware 5
 * @subpackage      Backward compatibility
 * @copyright       Copyright (c) 2016 <kontakt@fatchip.de> - www.fatchip.com
 * @author          Stefan Müller <stefan.mueller@fatchip.de>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.fatchip.com
 */

namespace Shopware\Components;

/*
 *  \Shopware\Components\CSRFWhitelistAware was implemented in SW 5.2
 *  this empty interface definition is used for SW versions < SW 5.2
 */

if (!interface_exists('\Shopware\Components\CSRFWhitelistAware')) {
    interface CSRFWhitelistAware {}
}
