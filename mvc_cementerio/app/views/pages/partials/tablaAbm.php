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
        <?php if (!empty($datos['urlCrear'])): ?>
            <a href="<?= $datos['urlCrear'] ?>" class="btn btn-success"> Nuevo </a>
        <?php endif; ?>
    </div>

    <div class="table-responsive-lg shadow-rounded">
        <table class="table table-hover align-middle mb-0" id="tablaABM" style="min-width: 800px;">
            <thead class="table-light">
                <tr>
                    <?php foreach ($datos['columnas'] as $col): ?>
                        <th><?= $col ?></th>
                    <?php endforeach ?>
                    <?php if (!empty($datos['acciones'])): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['data'] as $fila): ?>
                    <tr>
                        <?php foreach ($datos['columnas_claves'] as $key): ?>
                            <td class="text-truncate" style="max-width: 200px;"><?= ucfirst(htmlspecialchars($fila[$key])) ?></td>
                        <?php endforeach ?>
                        <?php if (!empty($datos['acciones'])): ?>
                            <td><?= $datos['acciones']($fila) ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#tablaABM').DataTable({
        dom: 'Bfrtip', // B: botones, f: filtro, r: información, t: tabla, i: info, p: paginación
        buttons: [
            { extend: 'copy', text: 'Copiar', className: 'btn btn-secondary btn-sm', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'csv', text: 'CSV', className: 'btn btn-primary btn-sm', bom: true, charset: 'UTF-8', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'excel', text: 'Excel', className: 'btn btn-success btn-sm', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'pdf', text: 'PDF', className: 'btn btn-danger btn-sm', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'print', text: 'Imprimir', className: 'btn btn-info btn-sm', exportOptions: { columns: ':not(:last-child)' } }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 8,       // Número de filas por página
        lengthMenu: [5, 10, 25, 50, 100], // Opciones para cambiar cantidad
        order: []             // Sin orden inicial (para que el usuario elija)
    });
});
</script>