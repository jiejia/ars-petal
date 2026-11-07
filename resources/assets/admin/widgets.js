/**
 * Widgets screen and Customizer: the "Select image" button of the Petal banners widget. The widget forms are added to the
 * page while it is open (block widget editor, Customizer), so the click is handled on the document.
 */
(function ($) {
  'use strict';

  $(document).on('click', '[data-ars-petal-pick]', function (event) {
    event.preventDefault();

    const slide = $(this).closest('.ars-petal-slide');
    const image = slide.find('[data-ars-petal-image]');
    const alt = slide.find('[data-ars-petal-alt]');
    const strings = window.petalWidgets || {};
    const frame = wp.media({ title: strings.title, button: { text: strings.button }, library: { type: 'image' }, multiple: false });

    frame.on('select', function () {
      const attachment = frame.state().get('selection').first().toJSON();

      // A change event, so the Customizer and the block widget editor notice the new value and save it.
      image.val(attachment.url).trigger('input').trigger('change');

      if (!alt.val() && attachment.alt) {
        alt.val(attachment.alt).trigger('input').trigger('change');
      }
    });

    frame.open();
  });
})(jQuery);
