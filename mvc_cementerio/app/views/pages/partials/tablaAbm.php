<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><?= $datos['title'] ?></h2>
            <?php if (!empty($datos['urlCrear']) && !empty($datos['puedeCrear'])): ?>
                <a href="<?= $datos['urlCrear'] ?>" class="btn btn-light">
                    <i class="bi bi-plus-circle"></i> Nuevo
                </a>
            <?php endif; ?>
        </div>


        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="busqueda" class="form-control" placeholder="Buscar...">
                    </div>
                </div>
            </div>

            <div class="table-responsive rounded-3 overflow-hidden">
                <table class="table table-hover align-middle mb-0" id="tablaABM">
                    <thead class="table-light">
                        <tr>
                            <?php foreach ($datos['columnas'] as $col): ?>
                                <th class="fw-semibold"><?= $col ?></th>
                            <?php endforeach ?>
                            <?php if (!empty($datos['acciones'])): ?>
                                <th class="fw-semibold text-end">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos['data'] as $fila): ?>
                            <tr class="border-top">
                                <?php foreach ($datos['columnas_claves'] as $key): ?>
                                    <?php
                                        if (isset($fila[$key])) {
                                            echo '<td>' . ucfirst(htmlspecialchars($fila[$key])) . '</td>';
                                        } else {
                                            echo '<td></td>'; 
                                        }
                                    ?>
                                <?php endforeach ?>
                                <?php if (!empty($datos['acciones'])): ?>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?= $datos['acciones']($fila) ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('busqueda');
    const tabla = document.getElementById('tablaABM').getElementsByTagName('tbody')[0];

    input.addEventListener('keyup', function () {
        const filtro = input.value.toLowerCase();
        const filas = tabla.getElementsByTagName('tr');

        Array.from(filas).forEach(fila => {
            const celdas = fila.getElementsByTagName('td');
            let coincide = false;

            for (let celda of celdas) {
                if (!(celda.firstElementChild && celda.firstElementChild.tagName === "A") && 
                    celda.textContent.toLowerCase().includes(filtro)) {
                    coincide = true;
                    break;
                }
            }

            fila.style.display = coincide ? '' : 'none';
        });
    });
});
</script>