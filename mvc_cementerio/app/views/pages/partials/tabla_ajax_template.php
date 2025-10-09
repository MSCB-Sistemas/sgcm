<?php
$tabId = $config['tabId'];
$ajaxUrl = $config['ajaxUrl'];
$columnHeaders = $config['columnHeaders'];
$configKey = $config['configKey'];
?>

<div class="tab-pane fade" id="<?= $tabId ?>" role="tabpanel">
    <div class="filtro-box mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="fecha_inicio_<?= $tabId ?>" class="form-label">Desde</label>
                <input type="date" class="form-control" id="fecha_inicio_<?= $tabId ?>">
            </div>
            <div class="col-md-3">
                <label for="fecha_fin_<?= $tabId ?>" class="form-label">Hasta</label>
                <input type="date" class="form-control" id="fecha_fin_<?= $tabId ?>">
            </div>
        </div>
    </div>

    <table class="table table-bordered table-striped datatable-ajax" 
           id="tabla-<?= $tabId ?>" 
           style="width:100%"
           data-ajax-url="<?= $ajaxUrl ?>"
           data-config-key="<?= $configKey ?>"
           data-filter-ids="#fecha_inicio_<?= $tabId ?>, #fecha_fin_<?= $tabId ?>">
        
        <thead>
            <tr>
                <?php foreach ($columnHeaders as $header) : ?>
                    <th><?= $header ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            </tbody>
    </table>
</div>