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

        /**
         * Initialize the component
         * - Sets up an observer for minicart changes
         * - Listens for customer data reload events
         */
        initialize: function () {
            this._super();
            this.initMiniCartObserver();
            $(document).on('customer-data-reload', this.updateMiniCart.bind(this));
        },

        /**
         * Checks if the button should be displayed
         * - Calls isModuleEnabled and isLoggedInCustomer
         * - Sets isDisplayButton observable based on the results
         */
        checkDisplayButton: function () {
            $.when(this.isModuleEnabled(), this.isLoggedInCustomer()).then((isModuleEnabled, isLoggedIn) => {
                this.isDisplayButton(isModuleEnabled && isLoggedIn);
            });
        },

        /**
         * Checks if the customer is logged in
         * - Returns true if the customer has a first name (indicating they are logged in)
         */
        isLoggedInCustomer: function () {
            const customer = customerData.get('customer')();
            return !!customer.firstname;
        },

        /**
         * Checks if the module is enabled
         * - Makes an AJAX call to the server to check module status
         * - Returns a promise that resolves to true if the module is enabled, false otherwise
         */
        isModuleEnabled: function () {
            const serviceUrl = url.build('saveforlater/index/isModuleEnabled');
            return storage.get(serviceUrl).then((response) => {
                if (response.success) {
                    return response.isModuleEnabled === '1';
                } else {
                    messageList.addErrorMessage({ message: 'Error checking module status: ' + response.message });
                    return false;
                }
            }).fail((error) => {
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
        },

        /**
         * Initializes the observer for minicart changes
         * - Subscribes to changes in the cart data
         * - Calls checkDisplayButton whenever the cart data changes
         */
        initMiniCartObserver: function () {
            const cartData = customerData.get('cart');
            cartData.subscribe(this.checkDisplayButton.bind(this));

            // Initial call to handle page refresh
            this.checkDisplayButton();
        },

        /**
         * Updates the minicart data
         * - Invalidates and reloads the cart section of the customer data
         * - Only triggers if the customer section is included in the reload
         */
        updateMiniCart: function (event, sections) {
            if (sections.includes('customer')) {
                const sectionsToUpdate = ['cart'];
                customerData.invalidate(sectionsToUpdate);
                customerData.reload(sectionsToUpdate, true);
            }
        },
    });
});
