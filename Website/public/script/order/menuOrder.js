/**
 * To apply the plugin DataTables
 * pagination
 * search bar
 * sorting per column
 */
$(document).ready( function () {
    $('#order_table').DataTable(
        {
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
            responsive: true
        }
    );
} );

/**
 * Reglage de la taille du tableau qui n'est pas correcte en mode Smartphone
 */
