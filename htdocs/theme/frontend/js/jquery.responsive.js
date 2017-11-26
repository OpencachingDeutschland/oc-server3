(function($, window) {
    var baseFontSize = 16;

    window.StateManager.init([
        {
            state: 'xs',
            enter: 0,
            exit: 575 / baseFontSize   // 575px
        },
        {
            state: 'sm',
            enter: 576 / baseFontSize,
            exit: 767 / baseFontSize   // 767px
        },
        {
            state: 'md',
            enter: 768 / baseFontSize,      // 768px
            exit: 991 / baseFontSize   // 991px
        },
        {
            state: 'lg',
            enter: 992 / baseFontSize,      // 992px
            exit: 1199 / baseFontSize   // 1199px
        },
        {
            state: 'xl',
            enter: 1200 / baseFontSize,   // 1200px
            exit: 5160 / baseFontSize     // 5160px
        }
    ]);

    window.StateManager.addPlugin('body', 'ocHelloWorld');

    window.StateManager.registerListener([{
        state: '*',
        enter: function(event) {
            console.debug(event.exiting + ' => ' + event.entering);
        }
    }]);

})(jQuery, window);
