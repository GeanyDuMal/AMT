/**
 * To apply the plugin DataTables
 * pagination
 * search bar
 * sorting per column
 */
$(document).ready( function () {
    $('#ordered-table').DataTable(
        {
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
            responsive: true
        }
    );
} );