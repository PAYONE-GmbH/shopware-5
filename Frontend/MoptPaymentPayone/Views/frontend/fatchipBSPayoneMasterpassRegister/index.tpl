{extends file="parent:frontend/register/index.tpl"}

{* enable step box SW 5.0 *}
{block name='frontend_index_navigation_categories_top'}
{/block}

{* disable login box SW 5.0 *}
{block name='frontend_register_index_login'}
{/block}

{* disable sidebar SW 5.0 *}
{* Sidebar left *}
{block name='frontend_index_content_left'}
{/block}

{* disable advantage box SW 5.0 *}
{block name='frontend_register_index_advantages'}
{/block}

{* change register Steps to 1 Ihre Adresse, 2 Versandart, 3 Prüfen und Bestellen *}

{* First Step - Address *}
{block name='frontend_register_steps_basket'}
{/block}

{* Second Step - Payment *}
{block name='frontend_register_steps_register'}
{/block}

{* Third Step - Confirmation *}
{block name='frontend_register_steps_confirm'}
{/block}

{* Replace Register content with Amazon Widget SW 5.0 *}
{block name='frontend_register_index_registration'}
    <div id="fatchipBSPayoneMasterpassInformation" hidden
         data-fatchipBSPayoneMasterpassRegisterUrl='{url controller="FatchipBSPayoneMasterpassRegister" action="saveRegister" forceSecure}?sTarget=FatchipBSPayoneMasterpassCheckout&sTargetAction=shippingPayment'
<!--  {*       data-firstname='{$fatchipAddrFirstName}'
         data-lastname='{$fatchipAddrLastName}'
         data-email='{$fatchipCTResponse->getEmail()}'
         data-phone='0' // never set, as precaution use 0 to defeat sw field validation
         data-street='{$fatchipCTResponse->getAddrStreet()}'
         data-zip='{$fatchipCTResponse->getAddrZip()}'
         data-city='{$fatchipCTResponse->getAddrCity()}'
         data-countryCodeBillingID='{$fatchipAddrCountryCodeID}'
    *}
         -->
    data-firstname='Stefan'
    data-lastname='Müller'
    data-email='stefan.mueller@fatchip.de'
    data-phone='0' // never set, as precaution use 0 to defeat sw field validation
    data-street='Speyerer Str.3'
    data-zip='10779'
    data-city='Berlin'
    data-countryCodeBillingID='2'


    ></div>
{/block}

