//add tooltip
jQuery(function () {
    jQuery(document).tooltip({
        content: function () {
            return jQuery(this).prop('title');
        },
        show: null,
        close: function (event, ui) {
            ui.tooltip.hover(

            function () {
                jQuery(this).stop(true).fadeTo(400, 1);
            },

            function () {
                jQuery(this).fadeOut("400", function () {
                    jQuery(this).remove();
                })
            });
        }
    });
});
