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
         data-firstname='{$fatchipBSPayone.firstname}'
         data-lastname='{$fatchipBSPayone.lastname}'
         data-email='{$fatchipBSPayone.email}'
         data-phone='{$fatchipBSPayone.telephonenumber}' // never set, as precaution use 0 to defeat sw field validation
         data-street='{$fatchipBSPayone.street}'
         data-zip='{$fatchipBSPayone.zip}'
         data-city='{$fatchipBSPayone.city}'
         data-firstname2='{$fatchipBSPayone.shipping_firstname}'
         data-lastname2='{$fatchipBSPayone.shipping_lastname}'
         data-street2='{$fatchipBSPayone.shipping_street}'
         data-zip2='{$fatchipBSPayone.shipping_zip}'
         data-city2='{$fatchipBSPayone.shipping_city}'
         data-phone2='{$fatchipBSPayone.shipping_telephonenumber}'
         data-salutation='{$fatchipBSPayone.salutation}'
         data-salutation2='{$fatchipBSPayone.shipping_salutation}'
         data-countryCodeBillingID='{$fatchipBSPayone.countryCodeBillingID}'
         data-countryCodeShippingID='{$fatchipBSPayone.countryCodeShippingID}'
    ></div>
{/block}

