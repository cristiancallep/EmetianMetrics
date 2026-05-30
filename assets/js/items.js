$(document).ready(function () {
    const itemsTable = $('#itemsTable').DataTable({
        columns: [
            { data: 'id' },
            { data: 'title' },
            { data: 'crypto_symbol' },
            { data: 'description' },
            { data: 'owner_name' },
            { data: 'created_at' },
            {
                data: null,
                orderable: false,
                render: function (data) {
                    const isOwner = data.user_id === data.current_user_id;
                    const editButton = `<button class="btn-action edit-item" data-id="${data.id}" data-title="${encodeHtml(data.title)}" data-crypto-symbol="${encodeHtml(data.crypto_symbol)}" data-description="${encodeHtml(data.description)}">Editar</button>`;
                    const deleteButton = `<button class="btn-action delete-item" data-id="${data.id}" ${isOwner ? '' : 'disabled'}>Eliminar</button>`;
                    return `${editButton} ${deleteButton}`;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    function encodeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function loadItems() {
        fetch('../backend/api/items.php')
            .then((response) => response.json())
            .then((data) => {
                if (data.data) {
                    const items = data.data.map((item) => ({
                        ...item,
                        current_user_id: data.current_user_id
                    }));
                    itemsTable.clear().rows.add(items).draw();
                }
            })
            .catch((error) => {
                console.error('Error al cargar favoritos cripto:', error);
            });
    }

    function resetForm() {
        $('#item_id').val('');
        $('#title').val('');
        $('#crypto_symbol').val('');
        $('#description').val('');
        $('#image').val('');
    }

    $('#itemForm').on('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        fetch('../backend/api/item_save.php', {
            method: 'POST',
            body: formData
        })
            .then(async (response) => {
                const text = await response.text();
                let data = null;
                try {
                    data = text ? JSON.parse(text) : null;
                } catch (err) {
                    console.error('Respuesta no JSON de item_save:', response.status, text);
                    throw new Error('Respuesta inválida del servidor: ' + response.status);
                }
                if (!response.ok) {
                    console.error('API error item_save', response.status, data);
                    throw new Error(data?.message || ('Error del servidor: ' + response.status));
                }
                return data;
            })
            .then((data) => {
                if (data && data.success) {
                    alert(data.message);
                    loadItems();
                    resetForm();
                } else {
                    alert((data && data.message) || 'Error al guardar el favorito cripto');
                }
            })
            .catch((error) => {
                console.error('Error al enviar el formulario:', error);
                alert(error.message || 'Error de red al guardar el favorito cripto');
            });
    });

    $('#resetItem').on('click', resetForm);

    $('#itemsTable tbody').on('click', '.edit-item', function () {
        const rowData = itemsTable.row($(this).parents('tr')).data();
        $('#item_id').val(rowData.id);
        $('#title').val(rowData.title);
        $('#crypto_symbol').val(rowData.crypto_symbol);
        $('#description').val(rowData.description);
        $('html, body').animate({ scrollTop: $('#itemForm').offset().top - 20 }, 300);
    });

    $('#itemsTable tbody').on('click', '.delete-item', function () {
        const itemId = $(this).data('id');
        if (!confirm('¿Eliminar este favorito cripto?')) {
            return;
        }
        fetch('../backend/api/item_delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(itemId)}`
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert(data.message);
                    loadItems();
                } else {
                    alert(data.message || 'Error al eliminar el favorito cripto');
                }
            })
            .catch((error) => {
                console.error('Error al eliminar favorito cripto:', error);
                alert('Error de red al eliminar el favorito cripto');
            });
    });

    loadItems();
});
