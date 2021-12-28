/**
 * To apply the plugin DataTables (he makes the table so cool :3 )
 * pagination
 * search bar
 * sorting per column
 */
$(document).ready( function () {
    $('#order_table').DataTable(
        {
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            }
        }
    );
} );