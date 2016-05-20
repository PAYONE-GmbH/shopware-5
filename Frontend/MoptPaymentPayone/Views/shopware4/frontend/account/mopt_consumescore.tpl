{extends file="frontend/account/payment.tpl"}

{block name="frontend_index_content" append}
    {if $moptConsumerScoreCheckNeedsUserAgreement}
        <script type="text/javascript">
            $(document).ready(function () {
                $.post('{url controller=moptAjaxPayone action=ajaxGetConsumerScoreUserAgreement forceSecure}', function (data) {
                    $.modal(data, '', {
                        'position': 'fixed',
                        'width': 500,
                        'height': 500
                    }).find('.close').remove();
                });
            });
        </script>
    {/if}

    <script type="text/javascript">
        if (typeof(MoptSubmitPaymentForm) !== "function") {
            function MoptSubmitPaymentForm()
            {
                $('form[name=frmRegister]').submit();
            };
        }
    </script>
{/block}