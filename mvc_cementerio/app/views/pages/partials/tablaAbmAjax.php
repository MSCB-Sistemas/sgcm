<?php if (!empty($datos['errores'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($datos['errores'] as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<div class="container-fluid mt-1 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><?= $datos['title'] ?></h2>
        <?php if (!empty($datos['urlCrear']) && ($datos['puedeCrear'] ?? false)): ?>
            <a href="<?= $datos['urlCrear'] ?>" class="btn btn-success">Nuevo</a>
        <?php endif; ?>
    </div>

    <div class="table-responsive-lg shadow-rounded">
        <table class="table table-hover align-middle mb-0" id="tablaABM" style="min-width: 400px;">
            <thead class="table-light">
                <tr>
                    <?php foreach ($datos['columnas'] as $col): ?>
                        <th><?= $col ?></th>
                    <?php endforeach ?>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = $('#tablaABM');
    
    const config = {
        dom: 'Bfrtip',
        buttons: [
            { 
                extend: 'copy', 
                text: 'Copiar', 
                className: 'btn btn-secondary btn-sm', 
                exportOptions: { columns: ':not(:last-child)' } 
            },
            { 
                extend: 'csv', 
                text: 'CSV', 
                className: 'btn btn-primary btn-sm', 
                bom: true, 
                charset: 'UTF-8', 
                exportOptions: { columns: ':not(:last-child)' } 
            },
            { 
                extend: 'excel', 
                text: 'Excel', 
                className: 'btn btn-success btn-sm', 
                exportOptions: { columns: ':not(:last-child)' } 
            },
            { 
                extend: 'pdf', 
                text: 'PDF', 
                className: 'btn btn-danger btn-sm', 
                exportOptions: { columns: ':not(:last-child)' } 
            },
            { 
                extend: 'print', 
                text: 'Imprimir', 
                className: 'btn btn-info btn-sm', 
                exportOptions: { columns: ':not(:last-child)' } 
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 8,  
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        serverSide: true,
        processing: true,
        ajax: {
            url: '<?= $datos['ajaxUrl'] ?>',
            type: 'POST',
        },
        columns: <?= json_encode($datos['columnsConfig'] ?? []) ?>,
        columnDefs: [
            {
                targets: -1,
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return data;
                }
            }
        ]
    };

    $('#tablaABM').DataTable(config);
});

function editarItem(id) {
    window.location.href = `<?= $datos['baseUrl'] ?? '' ?>edit/${id}`;
}

function eliminarItem(id) {
    if (confirm('¿Está seguro de que desea eliminar este registro?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= $datos['baseUrl'] ?? '' ?>delete/${id}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?= $datos['csrfToken'] ?? '' ?>';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>