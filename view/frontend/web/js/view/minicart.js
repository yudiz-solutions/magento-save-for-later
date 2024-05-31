define([
    'jquery',
    'Magento_Checkout/js/view/minicart',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/model/messageList',
    'Magento_Customer/js/customer-data',
    'swal',
    'mage/storage',
    'mage/url',
    'ko'
], function ($, Component, alert, confirm, messageList, customerData, Swal, storage, url, ko) {
    'use strict';

    return Component.extend({
        isDisplayButton: ko.observable(false),

        initialize: function () {
            this._super();
            this.checkDisplayButton();
        },

        checkDisplayButton: function () {
            var self = this;
            $.when(self.isModuleEnabled(), self.isLoggedInCustomer()).then(function (isModuleEnabled, isLoggedIn) {
                self.isDisplayButton(isModuleEnabled && isLoggedIn);
            });
        },

        isLoggedInCustomer: function () {
            var customer = customerData.get('customer')();
            if (customer.firstname !== undefined && customer.firstname !== null && customer.firstname !== '') {
                return true;
            } else {
                return false;
            }
        },

        isModuleEnabled: function () {
            var serviceUrl = url.build('saveforlater/index/isModuleEnabled');
            return storage.get(serviceUrl).then(function (response) {
                if (response.success) {
                    if (response.isModuleEnabled === '1') {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    messageList.addErrorMessage({ message: 'Error checking module status: ' + response.message });
                    return false;
                }
            }, function (error) {
                messageList.addErrorMessage({ message: 'Error checking module status: ' + JSON.stringify(error) });
                return false;
            });
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
                showLoader: true,
                beforeSend: function () {
                    $(elem).attr('disabled', 'disabled');
                },
                complete: function () {
                    $(elem).attr('disabled', null);
                }
            }).done(function (response) {
                if (!response.errors) {
                    $('body').trigger('processStop');
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: "Products are moved to Save For Later.",
                        showConfirmButton: false,
                        timer: 3000,
                    });
                } else {
                    var msg = response.message;
                    if (msg) {
                        Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: msg,
                            showConfirmButton: false,
                            timer: 3000,
                        });
                    }
                }
            }).fail(function (error) {
                messageList.addErrorMessage({ message: JSON.stringify(error) });
            });
        }
    });
});
