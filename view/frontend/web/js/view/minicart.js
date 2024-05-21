define([
    'jquery',
    'Magento_Checkout/js/view/minicart',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/model/messageList',
    'Magento_Customer/js/customer-data'
], function ($, Component, alert, confirm, messageList, customerData) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
            this.isLoggedInCustomer();
        },

        isLoggedInCustomer: function () {
            console.log("initi");
            var customer = customerData.get('customer')();
            console.log("Customer Data:", customer);
            console.log("Customer firstname:", customer.firstname);

            if (customer.firstname !== undefined && customer.firstname !== null && customer.firstname !== '') {
                return true;
            } else {
                return false;
            }
        },

        confirmMessage: $.mage.__('Are you sure you would like to remove all items from the shopping cart?'),
        emptyCartUrl: window.checkout.emptyMiniCart,
        emptyCartAction: function (element) {
            var self = this,
                href = self.emptyCartUrl;

            $(element).on('click', function () {
                self._removeAllItems(href, this);
            });
        },
        _removeAllItems: function (href, elem) {
            var self = this;
            $.ajax({
                url: href,
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $(elem).attr('disabled', 'disabled');
                },
                complete: function () {
                    $(elem).attr('disabled', null);
                }
            }).done(function (response) {
                if (!response.errors) {
                    // Display success message in a centered popup
                    alert({
                        title: $.mage.__('Save For Later'),
                        content: $.mage.__('Products are moved to Save For Later.'),
                        modalClass: 'centered-popup',
                        actions: {
                            always: function () {
                                console.log('Success popup closed.');
                            }
                        }
                    });
                } else {
                    var msg = response.message;
                    if (msg) {
                        alert({
                            content: msg,
                            modalClass: 'centered-popup'
                        });
                    }
                }
            }).fail(function (error) {
                console.log(JSON.stringify(error));
            });
        }
    });
});
