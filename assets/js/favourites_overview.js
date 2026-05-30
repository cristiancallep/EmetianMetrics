$(document).ready(function () {
    const favouritesTable = $('#overviewFavouritesTable').DataTable({
        columns: [
            { data: 'id' },
            { data: 'user_name' },
            { data: 'username' },
            { data: 'item_title' },
            { data: 'crypto_symbol' },
            { data: 'created_at' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[4, 'desc']]
    });

    function applySearch(value) {
        favouritesTable.search(value).draw();
    }

    $('#overviewFilter').on('input', function () {
        applySearch(this.value);
    });

    $('#clearOverviewFilter').on('click', function () {
        $('#overviewFilter').val('');
        applySearch('');
    });

    fetch('../backend/api/favourites_overview.php')
        .then((response) => response.json())
        .then((data) => {
            if (data.data) {
                favouritesTable.clear().rows.add(data.data).draw();
            }
        })
        .catch((error) => {
            console.error('Error al cargar los favoritos combinados:', error);
        });
});
