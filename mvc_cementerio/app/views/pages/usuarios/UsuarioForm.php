<?php
$usuario = '';
if (isset($values['usuario'])) {
    $usuario = $values['usuario'];
}
$nombre= '';
if (isset($datos['values']['nombre'])) {
    $nombre = htmlspecialchars($datos['values']['nombre']);
}
$apellido= '';
if (isset($datos['values']['apellido'])) {
    $apellido = htmlspecialchars($datos['values']['apellido']);
}
$telefono= '';
if (isset($datos['values']['telefono'])) {
    $telefono = htmlspecialchars($datos['values']['telefono']);
}
$email= '';
if (isset($datos['values']['email'])) {
    $email = htmlspecialchars($datos['values']['email']);
}
$fecha_retiro= '';
if (isset($datos['values']['fecha_retiro'])) {
    $fecha_retiro = htmlspecialchars($datos['values']['fecha_retiro']);
}
$fecha_retiro= '';
if (isset($datos['values']['fecha_retiro'])) {
    $fecha_retiro = htmlspecialchars($datos['values']['fecha_retiro']);
}
$fecha_retiro= '';
if (isset($datos['values']['fecha_retiro'])) {
    $fecha_retiro = htmlspecialchars($datos['values']['fecha_retiro']);
}
?>



<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><?= $datos['title'] ?></h2>
        </div>
        
        <div class="card-body">
            <?php if (!empty($datos['errores'])): ?>
                <div class="alert alert-danger">
                    <h5 class="alert-heading">Errores en el formulario</h5>
                    <ul class="mb-0">
                        <?php foreach ($datos['errores'] as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="<?= $datos['action'] ?>" method="POST" class="needs-validation" novalidate>
            <div class="row g-3">
                <!-- Columna izquierda -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="usuario" class="form-label fw-bold">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" 
                            value="<?= htmlspecialchars($datos['values']['usuario'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor ingrese un nombre de usuario
                        </div>
                    </div>

                    <?php if (!$datos['update']): ?>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">
                                Por favor ingrese una contraseña
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                            value="<?= htmlspecialchars($datos['values']['nombre'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor ingrese el nombre
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="apellido" class="form-label fw-bold">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" 
                            value="<?= htmlspecialchars($datos['values']['apellido'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor ingrese el apellido
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label fw-bold">Número de teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" 
                            value="<?= htmlspecialchars($datos['values']['telefono'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor ingrese su número de teléfono
                        </div>
                    </div>
                </div>

                <!-- Columna derecha -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Correo electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" 
                            value="<?= htmlspecialchars($datos['values']['email'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor ingrese su dirección de correo electrónico
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cargo" class="form-label fw-bold">Cargo</label>
                        <input type="text" class="form-control" id="cargo" name="cargo" 
                            value="<?= htmlspecialchars($datos['values']['cargo'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="sector" class="form-label fw-bold">Sector</label>
                        <input type="text" class="form-control" id="sector" name="sector" 
                            value="<?= htmlspecialchars($datos['values']['sector'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tipo_usuario" class="form-label fw-bold">Tipo de usuario</label>
                        <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['tipos'] as $n): ?>
                                <option value="<?= $n['id_tipo_usuario'] ?>"
                                    <?= ($datos['values']['id_tipo_usuario'] ?? '') == $n['id_tipo_usuario'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($n['descripcion']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback">
                            Por favor seleccione un tipo de usuario
                        </div>
                    </div>
                    
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar
                </button>
                <a href="<?= URL ?>usuario" class="btn btn-outline-secondary me-md-2">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>             
            </div> 
                </div>
            </div>
        </form>
        </div>
    </div>
</div>