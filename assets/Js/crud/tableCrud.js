$(document).ready(function () {
  $('#usuarios').DataTable({
    // Opciones de DOM para personalizar la interfaz
    // Opción "Bfrtilp" original
    dom: '<"d-flex justify-content-between align-items-center"Blf>rt<"d-flex justify-content-between align-items-center"p>',

    lengthMenu: [10, 20, 50, 100],
    // Habilitar la opción responsive
    responsive: true,
    // Configurar idioma
    language: {
      lengthMenu: "Mostrar _MENU_ registros por página",
      zeroRecords: "No se encontraron resultados",
      info: " ",
      infoEmpty: "No hay registros disponibles",
      infoFiltered: "(filtrado de _MAX_ registros en total)",
      search: "Buscar:",
      paginate: {
        previous: "Anterior",
        next: "Siguiente"
      }
    },

    buttons: [
      {
        extend: "excelHtml5",
        text: "<i class='ri-file-excel-2-fill' style= 'font-size: 20px;'></i>",
        titleAttr: "Exportar a Excel",
        className: "btn-export-crud btn-success",
        title: 'Reporte de Usuarios'
      },
      {
        extend: "pdfHtml5",
        text: "<i class='ri-file-pdf-2-fill' style= 'font-size: 20px;'></i>",
        titleAttr: "Exportar a PDF",
        className: "btn-export-crud btn-danger",
        pageSize: 'A3', // Smás ancho
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7] // indica los índices de las columnas visibles que SÍ se exportan
        },
        title: 'Reporte de Usuarios',
        customize: function (doc) {
          var colCount = doc.content[1].table.body[0].length;
          doc.content[1].table.widths = ['10%', '20%', '15%', '25%', '5%', '10%', '8%', '8%', '*'];

          doc.styles.tableHeader = {
            fillColor: '#789c82',
            color: 'white',
            alignment: 'center',
            bold: true,
            fontSize: 11
          };

          doc.styles.title = {
            alignment: 'center',
            fontSize: 16,
            bold: true,
            margin: [0, 0, 0, 20]
          };

          doc.styles.defaultStyle = {
            fontSize: 5// Aplica a todo el contenido por defecto
          };

          doc.content[1].table.body.forEach(function (row) {
            row.forEach(function (cell) {
              if (typeof cell === 'object') {
                cell.alignment = 'center';
                cell.margin = [0, 1, 0, 1];
                cell.noWrap = true; // Evita que se parta en varias líneas
              }
            });
          });

          doc.content[1].layout = {
            hLineWidth: function () { return 0.5; },
            vLineWidth: function () { return 0.5; },
            paddingLeft: function () { return 5; },
            paddingRight: function () { return 5; }
          };
        }
      },
      {
        extend: "print",
        text: "<i class='ri-printer-cloud-fill' style='color:rgb(255, 255, 255); font-size: 20px;'></i>",
        titleAttr: "Imprimir",
        className: "btn-export-crud btn btn-warning",
        title: 'Reporte de Usuarios',
        customize: function (win) {
          $(win.document.body).css('font-size', '10pt')
            .prepend('<h3 class="text-center">Listado de Usuarios</h3>');

          $(win.document.body).find('table')
            .addClass('compact')
            .css('font-size', 'inherit')
            .css('border-collapse', 'collapse')
            .css('width', '100%');
        }
      },
    ],

    "ordering": false

  });
});