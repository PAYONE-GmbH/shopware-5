$.plugin("fatchipBSPayoneMasterpass", {
    defaults: {
        fatchipBSPayoneMasterpassRegisterUrl: false,

        customerType: "private",
        salutation: "mr", // there is no way to know the gender
        firstname: false,
        lastname: false,
        email: false,
        phone: "0",
        //birthdayDay: false,
        //birthdayMonth: false,
        //birthdayYear: false,
        street: false,
        zip: false,
        city: false,
        countryCodeBillingID: false
    },

    init: function () {
        "use strict";
        var me = this;
        me.applyDataAttributes();
        var frm = $("<form>", {
            "action": me.opts.fatchipBSPayoneMasterpassRegisterUrl,
            "method": "post"
        });

        // SW 5.0 - 5.1
        frm.append(
            "<input type=\"hidden\" name=\"register[personal][customer_type]\" value=\"" + me.opts.customerType + "\"/>" +
            "<input type=\"hidden\" name=\"register[personal][salutation]\" value=\"" + me.opts.salutation + "\"/>" +
            "<input type=\"hidden\" name=\"register[personal][firstname]\" value=\"" + me.opts.firstname + "\"/>" +
            "<input type=\"hidden\" name=\"register[personal][lastname]\" value=\"" + me.opts.lastname + "\"/>" +
            // SW > 5.2
            "<input type=\"hidden\" name=\"register[personal][accountmode]\" value=\"1\"/>" +

            "<input type=\"hidden\" name=\"register[personal][skipLogin]\" value=\"1\"/>" +
            "<input type=\"hidden\" name=\"register[personal][email]\" value=\"" + me.opts.email + "\"/>" +
            "<input type=\"hidden\" name=\"register[personal][emailConfirmation]\" value=\"" + me.opts.email + "\"/>" +
            "<input type=\"hidden\" name=\"register[personal][phone]\" value=\"" + me.opts.phone + "\"/>" +

            "<input type=\"hidden\" name=\"register[billing][street]\" value=\"" + me.opts.street + "\"/>" +
            "<input type=\"hidden\" name=\"register[billing][city]\" value=\"" + me.opts.city + "\"/>" +
            "<input type=\"hidden\" name=\"register[billing][zipcode]\" value=\"" + me.opts.zip + "\"/>" +
            "<input type=\"hidden\" name=\"register[billing][country]\" value=\"" + me.opts.countryCodeBillingID + "\"/>" +
            //"<input type=\"hidden\" name=\"register[billing][shippingAddress]\" value=\"" + me.opts.differentShipping + "\"/>" +
            "<input type=\"hidden\" name=\"register[billing][customer_type]\" value=\"" + me.opts.customerType + "\"/>" +
            // SW > 5.2
            "<input type=\"hidden\" name=\"register[billing][accountmode]\" value=\"1\"/>" +
            "<input type=\"hidden\" name=\"register[billing][phone]\" value=\"" + me.opts.phone + "\"/>" +

            // SW > 5.2 check this, shouldnt be neccessary ->Register::getPostData
            "<input type=\"hidden\" name=\"register[billing][additional][customer_type]\" value=\"" + me.opts.customerType + "\"/>"
        );

        $(document.body).append(frm);
        // needed for SW > 5.2
        if (CSRF !== undefined && CSRF.updateForms !== undefined) {
            CSRF.updateForms();
        }
        frm.submit();
    },

    destroy: function () {
        "use strict";
        console.log("destroy Plugin triggered");
        var me = this;
        me._destroy();
    }
});

$("#fatchipBSPayoneMasterpassInformation").fatchipBSPayoneMasterpass();
