jQuery(document).ready(function($) {

    $(document).on('change', '.input-partnify-vendor', function(e) {
        e.preventDefault();
        inputVendorID = $(this).attr('id');
        inputCampaignID = $(this).closest('.input-wrapper').siblings('.campaign-wrapper').children('select').attr('id');

        inputRadioWrapper = $(this).closest('.input-wrapper').siblings('.campaign-assets-wrapper').children('.assets-radio').attr('id');

        vendorId = $('#' + inputVendorID).val();

        var options = '<option value="">Select Campaign</option>';
        $('#' + inputCampaignID).find('option').remove().end().append(options);
        $('#' + inputRadioWrapper).html('Please wait..');

        var submitButton = $(this).closest('.widget-content').siblings('.widget-control-actions').children('.alignright').children('input').attr('id');
        var spinner = $(this).closest('.widget-content').siblings('.widget-control-actions').children('.alignright').children('span.spinner');
        spinner.addClass('is-active');
        
        $('#' + submitButton).attr({ 'disabled': 'disabled' });
        if (vendorId) {
            var data = { action: "partnify_get_campaign", vendorId: vendorId, inputCampaignID: inputCampaignID };
            $.ajax({
                url: ajaxurl,
                data: data,
                type: "post",
                dataType: "json",
                success: function(data) {
                    if (data.status == 'success') {
                        // console.log(data.campaign_data);
                        for (var i = 0; i < data.campaign_data.length; i++) {
                            options += '<option value="' + data.campaign_data[i].CampaignId + '">' + data.campaign_data[i].CampaignName + '</option>';
                        }
                        // console.log(inputCampaignID);
                        $('#' + data.inputCampaignID)
                            .find('option')
                            .remove()
                            .end()
                            .append(options)
                            .val('');
                    }
                    spinner.removeClass('is-active');

                    $('#' + inputRadioWrapper).html('<span class="assets-not-found">Select campaign first</span>');
                }
            });
        } else {
            spinner.removeClass('is-active');
            $('#' + inputRadioWrapper).html('<span class="assets-not-found">Select campaign first</span>');
        }
    });

    $(document).on('change', '.input-partnify-campaign', function(e) {
        e.preventDefault();
        var spinner = $(this).closest('.widget-content').siblings('.widget-control-actions').children('.alignright').children('span.spinner');
        spinner.addClass('is-active');
        inputCampaignID = $(this).attr('id');

        var inputRadioWrapper = $(this).closest('.input-wrapper').siblings('.campaign-assets-wrapper').children('.assets-radio').attr('id');

        var assetsInputName = $(this).closest('.input-wrapper').siblings('.campaign-assets-wrapper').attr('data-name');
        // console.log(assetsInputName);
        CampaignId = $('#' + inputCampaignID).val();

        $('#' + inputRadioWrapper).html('Please wait..');

        var submitButton = $(this).closest('.widget-content').siblings('.widget-control-actions').children('.alignright').children('input').attr('id');
        $('#' + submitButton).attr({ 'disabled': 'disabled' });
        if (CampaignId) {
            // alert('campaign id');
            var data = { action: "partnify_get_campaign_assets", CampaignId: CampaignId, inputRadioWrapper: inputRadioWrapper };
            $.ajax({
                url: ajaxurl,
                data: data,
                type: "post",
                dataType: "json",
                success: function(data) {
                    // console.log(assetsInputName);

                    if (data.status == 'success') {
                        assets_html = '';
                        for (var i = 0; i < data.campaign_assets_data.length; i++) {
                            assets_html += '<label>'
                            assets_html += '<input type="radio" name="' + assetsInputName + '" value="' + data.campaign_assets_data[i].AssetId + '"  class="widefat input-partnify-campaign-asset"  /><img width="50" src="' + data.campaign_assets_data[i].AssetUrl + '" />';
                            assets_html += '</label>';
                        }
                        // console.log(data.inputRadioWrapper);
                        // console.log(assets_html);
                        $('#' + data.inputRadioWrapper).html(assets_html);
                    }
                    spinner.removeClass('is-active');
                }
            });
        } else {
            // alert('else');
            spinner.removeClass('is-active');
            $('#' + inputRadioWrapper).html('<span class="assets-not-found">Select campaign first</span>');
        }
    });
});