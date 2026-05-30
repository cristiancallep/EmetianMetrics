$(document).ready(function () {
    const table = $('#usersTable').DataTable({
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'username' },
            { data: 'created_at' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    fetch('../backend/api/users.php')
        .then((response) => response.json())
        .then((data) => {
            if (data.data) {
                table.clear().rows.add(data.data).draw();
            }
        })
        .catch((error) => {
            console.error('Error al cargar usuarios:', error);
        });
});
