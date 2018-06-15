$.plugin("fatchipBSPayoneMasterpass", {
    defaults: {
        fatchipBSPayoneMasterpassRegisterUrl: false,

        customerType: "private",
        salutation: false,
        firstname: false,
        lastname: false,
        email: false,
        phone: false,
        //birthdayDay: false,
        //birthdayMonth: false,
        //birthdayYear: false,
        street: false,
        additionalAddressLine1: false,
        zip: false,
        city: false,
        countryCodeBillingID: false,
        differentShipping: "1",
        salutation2: false,
        firstname2: false,
        lastname2: false,
        phone2: false,
        company2: "",
        department2: "",
        street2: false,
        additionalAddressLine1shipping: false,
        zip2: false,
        city2: false,
        countryCodeShippingID: false
    },

    init: function () {
        "use strict";
        var me = this;
        me.applyDataAttributes();
        // TESTING
        console.log(me.opts);
        var frm = $("<form>", {
            "action": me.opts.fatchipBSPayoneMasterpassRegisterUrl,
            "method": "post"
        });

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
            "<input type=\"hidden\" name=\"register[billing][additionalAddressLine1]\" value=\"" + me.opts.additionalAddressLine1 + "\"/>" +
            "<input type=\"hidden\" name=\"register[billing][city]\" value=\"" + me.opts.city + "\"/>" +
            "<input type=\"hidden\" name=\"register[billing][zipcode]\" value=\"" + me.opts.zip + "\"/>" +
            "<input type=\"hidden\" name=\"register[billing][country]\" value=\"" + me.opts.countryCodeBillingID + "\"/>" +
            "<input type=\"hidden\" name=\"register[billing][shippingAddress]\" value=\"" + me.opts.differentShipping + "\"/>" +
            "<input type=\"hidden\" name=\"register[billing][customer_type]\" value=\"" + me.opts.customerType + "\"/>" +
            // SW > 5.2
            "<input type=\"hidden\" name=\"register[billing][accountmode]\" value=\"1\"/>" +
            "<input type=\"hidden\" name=\"register[billing][phone]\" value=\"" + me.opts.phone + "\"/>" +

            // SW > 5.2 check this, shouldnt be neccessary ->Register::getPostData
            "<input type=\"hidden\" name=\"register[billing][additional][customer_type]\" value=\"" + me.opts.customerType + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][salutation]\" value=\"" + me.opts.salutation2 + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][firstname]\" value=\"" + me.opts.firstname2 + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][lastname]\" value=\"" + me.opts.lastname2 + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][company]\" value=\"" + me.opts.company2 + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][department]\" value=\"" + me.opts.department2 + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][street]\" value=\"" + me.opts.street2 + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][additionalAddressLine1]\" value=\"" + me.opts.additionalAddressLine1shipping + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][city]\" value=\"" + me.opts.city2 + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][zipcode]\" value=\"" + me.opts.zip2 + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][country]\" value=\"" + me.opts.countryCodeShippingID + "\"/>" +
            "<input type=\"hidden\" name=\"register[shipping][phone]\" value=\"" + me.opts.phone2 + "\"/>"
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
