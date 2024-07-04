<?php
/*
Plugin Name: CSV Filter Plugin
Description: A plugin to filter CSV data.
Version: 1.0
Author: Your Name
*/

// Enqueue DataTables and custom script
function enqueue_csv_filter_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('datatables', 'https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js', array('jquery'), null, true);
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css');
    wp_enqueue_script('csv-filter-script', plugin_dir_url(__FILE__) . 'csv-filter.js', array('jquery', 'datatables'), null, true);
    wp_localize_script('csv-filter-script', 'csvFilter', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_csv_filter_scripts');

// Shortcode to display the filter and table
function csv_filter_shortcode() {
    ob_start();
    ?>
    <div id="csv-filter">
        <select id="filter1">
            <option value="">Select Filter 1</option>
        </select>
        <select id="filter2">
            <option value="">Select Filter 2</option>
        </select>
        <button id="add-filter">Add Filter</button>
        <table id="csv-table" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Column 1</th>
                    <th>Column 2</th>
                    <th>Column 3</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('csv_filter', 'csv_filter_shortcode');

// Handle AJAX request to filter CSV
function filter_csv() {
    $csv_file = plugin_dir_path(__FILE__) . 'data.csv'; // Replace with your CSV file path
    $csv = array_map('str_getcsv', file($csv_file));
    $headers = array_shift($csv);

    $filters = $_POST['filters'];

    $filtered_csv = array_filter($csv, function($row) use ($filters, $headers) {
        foreach ($filters as $filter) {
            if (!empty($filter['value']) && strpos($row[array_search($filter['column'], $headers)], $filter['value']) === false) {
                return false;
            }
        }
        return true;
    });

    wp_send_json_success($filtered_csv);
}
add_action('wp_ajax_filter_csv', 'filter_csv');
add_action('wp_ajax_nopriv_filter_csv', 'filter_csv');
