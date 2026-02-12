/**
 * RG ROI Calculator Admin JS
 */
(function($) {
    'use strict';

    // Tab switching
    $('.rg-roi-admin .nav-tab').on('click', function(e) {
        e.preventDefault();
        var tab = $(this).data('tab');

        $('.rg-roi-admin .nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.rg-roi-admin .rg-tab-content').removeClass('active');
        $('#tab-' + tab).addClass('active');
    });

    // Calculate leasing rate (same formula as PHP)
    function calculateLeasingRate(price, months) {
        if (price <= 0 || months <= 0) return 0;

        var residualPercent = rgRoiAdmin.leasingResidual / 100;
        var annualInterest = rgRoiAdmin.leasingInterest / 100;
        var monthlyInterest = annualInterest / 12;

        var residualValue = price * residualPercent;
        var depreciationTotal = price - residualValue;
        var monthlyDepreciation = depreciationTotal / months;

        var averageBalance = (price + residualValue) / 2;
        var monthlyInterestAmount = averageBalance * monthlyInterest;

        return Math.round((monthlyDepreciation + monthlyInterestAmount) * 100) / 100;
    }

    // Format number with German locale
    function formatNumber(num, decimals) {
        decimals = decimals || 0;
        return num.toLocaleString('de-DE', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    // Update leasing preview
    function updateLeasingPreview() {
        var price = parseFloat($('#robot_price').val()) || 0;

        if (price > 0) {
            $('#leasing-preview').show();
            $('#preview-price').text(formatNumber(price));
            $('#preview-lease36').text(formatNumber(calculateLeasingRate(price, 36), 2));
            $('#preview-lease48').text(formatNumber(calculateLeasingRate(price, 48), 2));
            $('#preview-lease60').text(formatNumber(calculateLeasingRate(price, 60), 2));
        } else {
            $('#leasing-preview').hide();
        }
    }

    // Price input change
    $('#robot_price').on('input change', updateLeasingPreview);

    // Clear form
    function clearForm() {
        $('#robot_id').val('');
        $('#robot_name').val('');
        $('#robot_price').val('');
        $('#robot_performance').val('');
        $('#robot_service').val('');
        $('#robot_power').val('');
        $('#leasing-preview').hide();
        $('#rg-save-robot').text('Roboter speichern');
        $('#rg-cancel-edit').hide();
        $('#rg-robots-table tr').removeClass('editing');
    }

    // Cancel edit
    $('#rg-cancel-edit').on('click', clearForm);

    // Edit robot
    $(document).on('click', '.rg-edit-robot', function() {
        var $row = $(this).closest('tr');
        var id = $row.data('id');

        // Extract data from table cells (parse German number format)
        var name = $row.find('td:eq(0)').text().trim();
        var priceText = $row.find('td:eq(1)').text().replace(/\./g, '').replace(/[^\d]/g, '');
        var perfText = $row.find('td:eq(2)').text().replace(/\./g, '').replace(/[^\d]/g, '');
        var serviceText = $row.find('td:eq(3)').text().replace(/\./g, '').replace(/[^\d]/g, '');
        var powerText = $row.find('td:eq(4)').text().replace(/\./g, '').replace(/[^\d]/g, '');

        $('#robot_id').val(id);
        $('#robot_name').val(name);
        $('#robot_price').val(priceText);
        $('#robot_performance').val(perfText);
        $('#robot_service').val(serviceText);
        $('#robot_power').val(powerText);

        updateLeasingPreview();

        $('#rg-save-robot').text('Roboter aktualisieren');
        $('#rg-cancel-edit').show();

        $('#rg-robots-table tr').removeClass('editing');
        $row.addClass('editing');

        // Scroll to form
        $('html, body').animate({
            scrollTop: $('#rg-robot-form').offset().top - 50
        }, 300);
    });

    // Delete robot
    $(document).on('click', '.rg-delete-robot', function() {
        if (!confirm('Roboter wirklich löschen?')) return;

        var $row = $(this).closest('tr');
        var id = $row.data('id');

        $.post(rgRoiAdmin.ajaxUrl, {
            action: 'rg_delete_robot',
            nonce: rgRoiAdmin.nonce,
            robot_id: id
        }, function(response) {
            if (response.success) {
                $row.fadeOut(300, function() {
                    $(this).remove();
                    if ($('#rg-robots-list tr').length === 0) {
                        $('#rg-robots-list').html('<tr class="no-robots"><td colspan="8">Noch keine Roboter hinzugefügt.</td></tr>');
                    }
                });
                clearForm();
            } else {
                alert('Fehler: ' + (response.data?.message || 'Unbekannter Fehler'));
            }
        }).fail(function() {
            alert('Verbindungsfehler');
        });
    });

    // Save robot
    $('#rg-robot-form').on('submit', function(e) {
        e.preventDefault();

        var $btn = $('#rg-save-robot');
        var originalText = $btn.text();
        $btn.text('Speichern...').prop('disabled', true);

        $.post(rgRoiAdmin.ajaxUrl, {
            action: 'rg_save_robot',
            nonce: rgRoiAdmin.nonce,
            robot_id: $('#robot_id').val(),
            robot_name: $('#robot_name').val(),
            robot_price: $('#robot_price').val(),
            robot_performance: $('#robot_performance').val(),
            robot_service: $('#robot_service').val(),
            robot_power: $('#robot_power').val()
        }, function(response) {
            $btn.text(originalText).prop('disabled', false);

            if (response.success) {
                // Reload page to show updated list
                location.reload();
            } else {
                alert('Fehler: ' + (response.data?.message || 'Unbekannter Fehler'));
            }
        }).fail(function() {
            $btn.text(originalText).prop('disabled', false);
            alert('Verbindungsfehler');
        });
    });

})(jQuery);
