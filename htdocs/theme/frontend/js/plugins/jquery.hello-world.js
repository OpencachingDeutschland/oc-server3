;(function($) {
    'use strict';

    $.plugin('ocHelloWorld', {
        /**
         * Default plugin initialisation function.
         * Registers an event listener on the change event.
         * When it's triggered, the parent form will be submitted.
         *
         * @public
         * @method init
         */
        init: function () {
            var me = this;

            // Applies HTML data attributes to the current options
            me.applyDataAttributes();

            console.log('Hello World');
        }
    });
})(jQuery);
