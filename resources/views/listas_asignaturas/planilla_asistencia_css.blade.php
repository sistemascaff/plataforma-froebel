<style>
    /* Estilos del logo institucional en el encabezado */
    .logo-planilla {
        height: 78px;
        width: auto;
        max-width: 110px;
        object-fit: contain;
    }

    /* Estilos para cabeceras verticales y matriz compacta en pantalla */
    .tabla-planilla {
        font-size: 0.82rem;
        border-collapse: collapse;
    }

    .tabla-planilla th,
    .tabla-planilla td {
        padding: 0.28rem 0.35rem !important;
        vertical-align: middle;
    }

    .th-vertical {
        writing-mode: vertical-lr;
        transform: rotate(180deg);
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
        height: 115px;
        font-size: 0.73rem;
        letter-spacing: 0.5px;
        margin: 0 auto;
    }

    .col-sticky-num {
        position: sticky;
        left: 0;
        z-index: 2;
        min-width: 38px;
    }

    .col-sticky-nombre {
        position: sticky;
        left: 38px;
        z-index: 2;
        min-width: 240px;
        white-space: nowrap;
    }

    .celda-asistencia {
        min-width: 28px;
        max-width: 34px;
        text-align: center;
        font-weight: 700;
        cursor: default;
    }

    .col-recuento {
        min-width: 34px;
        text-align: center;
        font-weight: 700;
    }

    /* Fila de estudiante retirado (gris adaptable en web y fijo en impresión) */
    tr.fila-retirado td {
        background-color: rgba(108, 117, 125, 0.28) !important;
        color: #6c757d !important;
    }

    /* ====================================================================
       REGLAS EXCLUSIVAS PARA IMPRESIÓN / CTRL + P / GUARDAR COMO PDF
       Fuerza modo claro independientemente de si el usuario está en dark mode
       ==================================================================== */
    @media print {
        @page {
            size: landscape;
            margin: 8mm;
        }

        :root,
        html,
        body,
        [data-bs-theme="dark"],
        [data-bs-theme="light"] {
            color-scheme: light !important;
            --bs-body-bg: #ffffff !important;
            --bs-body-color: #000000 !important;
            --bs-card-bg: #ffffff !important;
            --bs-card-color: #000000 !important;
            --bs-table-bg: #ffffff !important;
            --bs-table-color: #000000 !important;
            --bs-table-border-color: #000000 !important;
            --bs-secondary-color: #495057 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Asegurar que el navegador respete colores y fondos en todos los elementos */
        *,
        *::before,
        *::after {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* 1. Ocultar absolutamente todo el DOM por defecto */
        body * {
            visibility: hidden !important;
        }

        /* 2. Mostrar únicamente #planilla-asistencia-card y sus hijos */
        #planilla-asistencia-card,
        #planilla-asistencia-card * {
            visibility: visible !important;
        }

        /* 3. Posicionar el card en el extremo superior izquierdo con fondo blanco puro */
        #planilla-asistencia-card {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            background-color: #ffffff !important;
            color: #000000 !important;
        }

        #planilla-asistencia-card>.card-body {
            padding: 0 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
        }

        .d-print-none {
            display: none !important;
        }

        .logo-planilla {
            height: 68px !important;
        }

        /* Encabezado institucional en fondo blanco y texto oscuro */
        .encabezado-institucional {
            background-color: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
        }

        .encabezado-institucional .text-muted {
            color: #495057 !important;
        }

        .table-responsive {
            overflow: visible !important;
            background-color: #ffffff !important;
        }

        .tabla-planilla {
            font-size: 8.5pt !important;
            width: 100% !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
        }

        /* Celdas base en fondo blanco y texto negro */
        .tabla-planilla th,
        .tabla-planilla td {
            background-color: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
            padding: 2px 3px !important;
        }

        .col-sticky-num,
        .col-sticky-nombre {
            position: static !important;
        }

        /* Preservar fondos grises claros de cabecera (.table-secondary) */
        .tabla-planilla tr.table-secondary th,
        .tabla-planilla th.table-secondary,
        .tabla-planilla td.table-secondary {
            background-color: #e2e3e5 !important;
            color: #000000 !important;
        }

        /* Mantener el sombreado gris para los estudiantes retirados */
        .tabla-planilla tr.fila-retirado td {
            background-color: #d9d9d9 !important;
            color: #333333 !important;
        }

        /* Preservar colores semánticos de asistencias y recuentos sobre el fondo claro */
        .tabla-planilla .text-danger {
            color: #dc3545 !important;
        }

        .tabla-planilla .text-warning {
            color: #d97706 !important;
            /* Ámbar oscuro legible sobre papel blanco */
        }

        .tabla-planilla .text-info {
            color: #0284c7 !important;
            /* Celeste oscuro legible sobre papel blanco */
        }

        .tabla-planilla .text-primary {
            color: #0d6efd !important;
        }

        .tabla-planilla .text-success {
            color: #198754 !important;
        }

        .tabla-planilla .text-secondary {
            color: #6c757d !important;
        }
    }
</style>
