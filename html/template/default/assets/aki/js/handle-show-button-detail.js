$(function () {
    $('.aki-reason_box .icon-show-more-detail-up').hide();
    $('.aki-readmore-new').on('click', function () {
        const parentBox = $(this).closest('.aki-reason_box');
        const hideText = parentBox.find('.hide-text');
        if (hideText.css('display') === 'none') {
            parentBox.find('.icon-show-more-detail-down').hide();
            parentBox.find('.icon-show-more-detail-up').show();
        } else {
            parentBox.find('.icon-show-more-detail-down').show();
            parentBox.find('.icon-show-more-detail-up').hide();
        }
    })
});
