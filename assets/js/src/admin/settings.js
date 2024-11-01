/**
 * settings.js
 *
 * JS handlers for the admin settings view
 */
(function($) {

    'use strict';

    $(document).ready(function() {

        // Ensure we are on the settings view or bail
        var wrap$ = $('body.wp-admin .wrap.shipbob');

        if (!wrap$.length) {
            return;
        }

        // Handle enable/disable continue on terms accept
        wrap$.on('change', 'input[type=checkbox][name$=terms_accepted]', function() {
            wrap$.find('input[type=submit]').prop('disabled', !$(this).is(':checked'));
        });

        // Handle enable/disable of form fields for radio options
        wrap$.on('change', '.row.radio input[type=radio]', function() {
            var group$ = $(this).closest('.row.radio-group');
            group$.find('input[type=text]').prop('disabled', true);
            $(this).closest('.row.radio').find('input[type=text]').prop('disabled', false);
        });

        // Handle nav tabs navigation
        wrap$.on('click', '.nav-tabs .nav-link[data-toggle]', function() {

            var tabs$ = $(this).closest('.nav-tabs');

            tabs$.find('.nav-link').removeClass('active');
            $(this).addClass('active');

            var panes$ = tabs$.next('.tab-content').children('.tab-pane');

            panes$.removeClass('show active').filter('.tab-pane.' + $(this).data('toggle')).addClass('show active');

        });

    });

})(jQuery);