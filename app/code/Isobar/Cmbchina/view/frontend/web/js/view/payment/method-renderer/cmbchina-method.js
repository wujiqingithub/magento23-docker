/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Isobar_Cmbchina/payment/cmbchina'
        },

        /** Returns is method available */
        isAvailable: function () {
            return true;
        }
    });
});
