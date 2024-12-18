<script>
    var payonePayPalAttempts = 0;

    function loadPayPalScript() {
        if (window.paypalJsLoaded === undefined || window.paypalJsLoaded !== true) {
            var elemScript = document.createElement('script');
            elemScript.type = "text/javascript";
            {if $payonePaypalv2Config['liveMode'] === true}
                elemScript.src ='https://www.paypal.com/sdk/js?client-id=AVNBj3ypjSFZ8jE7shhaY2mVydsWsSrjmHk0qJxmgJoWgHESqyoG35jLOhH3GzgEPHmw7dMFnspH6vim&merchant-id={$payonePaypalv2Config['paypalV2MerchantId']}&locale={$Locale}&currency={$payonePaypalv2Currency}&intent=authorize&commit=false&vault=false&disable-funding=card,sepa,bancontact{if $payonePaypalv2Config['paypalV2ShowButton'] === true}&enable-funding=paylater{/if}'
            {/if}
           {if $payonePaypalv2Config['liveMode'] == false}
                elemScript.src ='https://www.paypal.com/sdk/js?client-id=AUn5n-4qxBUkdzQBv6f8yd8F4AWdEvV6nLzbAifDILhKGCjOS62qQLiKbUbpIKH_O2Z3OL8CvX7ucZfh&merchant-id=3QK84QGGJE5HW&locale={$Locale}&currency={$payonePaypalv2Currency}&intent=authorize&commit=false&vault=false&disable-funding=card,sepa,bancontact{if $payonePaypalv2Config['paypalV2ShowButton'] === true}&enable-funding=paylater{/if}'
           {/if}
            document.body.appendChild(elemScript);

            window.paypalJsLoaded = true;
        }
    }

    function triggerPayPalButtonRender(button) {
        if (payonePayPalAttempts > 10) {
            return; // abort
        }

        if (typeof paypal != 'object') {
            loadPayPalScript();
            setTimeout(function() {
                window.requestAnimationFrame(function() {
                    triggerPayPalButtonRender(button)
                });
            }, 250);
        } else {
            initPayPalButton(button);
        }
        payonePayPalAttempts++;
    }

    function initPayPalButton(button) {
        if (document.getElementById(button) && document.getElementById(button).childNodes.length > 0) { // button already created, no need to init another button
            return;
        }
        let url = window.location.href.substring(window.location.href.lastIndexOf('/') + 1)
        let layout = 'vertical'
        if (url === 'cart') {
            layout = 'horizontal';
        }
        console.log('Layout:' + layout);
        paypal.Buttons({

            style: {
                layout: layout, // vertical or horizontal
                color: '{$payonePaypalv2Config['paypalV2ButtonColor']}', // gold, blue, black, silver, white
                shape: '{$payonePaypalv2Config['paypalV2ButtonShape']}', // rect, pill
                label: 'paypal', // paypal, pay, subscribe, checkout, buynow
                height: 40 // 25 - 55, a value around 40 is recommended
            },

            createOrder: function (data, actions) {
                return startPayPalExpress().then(function (res) {
                    var resJson = JSON.parse(res);
                    if (resJson.success === true) {
                        return resJson.order_id;
                    }
                    return false;
                }).fail(function (res) {
                    alert("An error occured.");
                    return false;
                });
            },

            onApprove: function (data, actions) {
                // redirect to your serverside success handling script/page
                // redirect to your serverside success handling script/page
                window.location = '{url controller="MoptPaymentEcsv2" action="paypalv2express" forceSecure}';
            },

            onCancel: function (data, actions) {
                window.location = '{url controller="MoptPaymentEcsv2" action="paypalv2expressabort" forceSecure}';
                // add your actions on cancellation
            },

            onError: function () {
                window.location = '{url controller="MoptPaymentEcsv2" action="paypalv2expresserror" forceSecure}';
                // add your actions if error occurs
            },

        }).render('#' + button);
    }
    function startPayPalExpress() {
        console.log('PPE Start');
        return $.ajax({
            url: '{url controller="MoptAjaxPayone" action="startPaypalExpress" forceSecure}',
            method: 'POST',
            type: 'POST',
            data: {
                shipping: '{$sShippingcosts}',
            }
        });
    }

    triggerPayPalButtonRender('paypal-button-container')
</script>