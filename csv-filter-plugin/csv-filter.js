jQuery(document).ready(function($) {
    var table = $('#csv-table').DataTable();

    $('#add-filter').on('click', function() {
        var filterColumn1 = $('#filter1').val();
        var filterValue1 = $('#filter1 option:selected').text();
        var filterColumn2 = $('#filter2').val();
        var filterValue2 = $('#filter2 option:selected').text();

        var filters = [];
        if (filterColumn1) filters.push({column: filterColumn1, value: filterValue1});
        if (filterColumn2) filters.push({column: filterColumn2, value: filterValue2});

        $.ajax({
            url: csvFilter.ajax_url,
            method: 'POST',
            data: {
                action: 'filter_csv',
                filters: filters
            },
            success: function(response) {
                if (response.success) {
                    table.clear();
                    response.data.forEach(function(row) {
                        table.row.add(row);
                    });
                    table.draw();
                }
            }
        });
    });
});
