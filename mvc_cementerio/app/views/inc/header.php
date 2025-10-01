<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="<?= URL . '/public/img/logo_municipio_bariloche.png' ?>" sizes="32x32">
  <link rel="shortcut icon" href="<?= URL . '/public/img/logo_municipio_bariloche.png' ?>" type="image/x-icon">

  <!-- Título dinámico desde PHP -->
  <title><?php if(!empty($datos['title'])){echo $datos['title'];} else {echo 'Sin título';} ?></title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <!-- Select con busqueda -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- DataTables CSS y JS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  
  <meta name="theme-color" content="#fd7e14">

  <?php if (isset($datos['error'])) { $error = $datos['error']; } ?>

  <style>
    :root {
      --bs-primary: #fd7e14;
      --bs-primary-rgb: 253, 126, 20;
      --bs-primary-bg-subtle: #fff3e8;
      --bs-border-color: #dee2e6;
    }
    
    .bg-primary {
      background-color: var(--bs-primary) !important;
    }
    
    .btn-primary {
      --bs-btn-bg: var(--bs-primary);
      --bs-btn-border-color: var(--bs-primary);
      --bs-btn-hover-bg: #e67312;
      --bs-btn-hover-border-color: #dc6e11;
      --bs-btn-active-bg: #d46910;
      --bs-btn-active-border-color: #c9640f;
      --bs-btn-disabled-bg: var(--bs-primary);
      --bs-btn-disabled-border-color: var(--bs-primary);
    }
    
    .btn-outline-primary {
      --bs-btn-color: var(--bs-primary);
      --bs-btn-border-color: var(--bs-primary);
      --bs-btn-hover-bg: var(--bs-primary);
      --bs-btn-hover-border-color: var(--bs-primary);
      --bs-btn-active-bg: var(--bs-primary);
      --bs-btn-active-border-color: var(--bs-primary);
      --bs-btn-disabled-color: var(--bs-primary);
      --bs-btn-disabled-border-color: var(--bs-primary);
    }
    
    .alert-primary {
      --bs-alert-color: #6c3400;
      --bs-alert-bg: var(--bs-primary-bg-subtle);
      --bs-alert-border-color: #fdd0a8;
    }
    
    .text-primary {
      color: var(--bs-primary) !important;
    }
    
    .border-primary {
      border-color: var(--bs-primary) !important;
    }
    
    .nav-pills {
      --bs-nav-pills-link-active-bg: var(--bs-primary);
    }
    
    .form-check-input:checked {
      background-color: var(--bs-primary);
      border-color: var(--bs-primary);
    }
    
    .dropdown-item.active, .dropdown-item:active {
      background-color: var(--bs-primary);
    }
    
    .bd-placeholder-img {
        font-size:1.125rem;
        text-anchor:middle;
        -webkit-user-select:none;
        -moz-user-select:none;
        user-select:none
    }
    
    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size:3.5rem
        }
    }
    
    .b-example-divider {
        width:100%;
        height:3rem;
        background-color:rgba(0, 0, 0, 0.1);
        border:solid rgba(0,0,0,.15);
        border-width:1px 0;
        box-shadow:inset 0 .5em 1.5em rgba(0, 0, 0, 0.1),inset 0 .125em .5em rgba(0, 0, 0, 0.15)
    }
    
    .b-example-vr {
        flex-shrink:0;
        width:1.5rem;
        height:100vh
    }
    
    .bi {
        vertical-align:-.125em;
        fill:currentColor
    }
    
    .nav-scroller {
        position:relative;
        z-index:2;
        height:2.75rem;
        overflow-y:hidden
    }
    
    .nav-scroller .nav {
        display:flex;
        flex-wrap:nowrap;
        padding-bottom:1rem;
        margin-top:-1px;
        overflow-x:auto;
        text-align:center;
        white-space:nowrap;
        -webkit-overflow-scrolling:touch
    }
    
    .btn-bd-primary {
        --bd-violet-bg: var(--bs-primary);
        --bd-violet-rgb: var(--bs-primary-rgb);
        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #e67312;
        --bs-btn-hover-border-color: #dc6e11;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #d46910;
        --bs-btn-active-border-color: #c9640f;
    }
    
    .bd-mode-toggle {
        z-index:1500
    }
    
    .bd-mode-toggle .bi {
        width:1em;
        height:1em
    }
    
    .bd-mode-toggle .dropdown-menu .active .bi {
        display:block!important
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
        background-color: var(--bs-primary);
        color: white;
        font-weight: 500;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        border-bottom: 2px solid var(--bs-border-color);
        background-color: #ffffffff;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(253, 126, 20, 0.05);
    }
  </style>

  <!-- Definiciones de símbolos SVG para íconos -->
  <svg xmlns="http://www.w3.org/2000/svg" style="display:none;">
    <!-- Home -->
    <symbol id="home" viewBox="0 0 16 16">
      <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z"/>
    </symbol>

    <!-- Table -->
    <symbol id="table" viewBox="0 0 16 16">
      <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"/>
    </symbol>
  </svg>
</head>