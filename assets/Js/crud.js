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
        text: "<i class='fa-solid fa-file-excel fa-xl' style='color:rgb(255, 255, 255);'></i>",
        titleAttr: "Exportar a Excel",
        className: "btn-export-crud btn-success"
      },
      {
        extend: "pdfHtml5",
        text: "<i class='fa-solid fa-file-pdf fa-xl' style='color:rgb(255, 255, 255);'></i>",
        titleAttr: "Exportar a PDF",
        className: "btn-export-crud btn-danger"
      },
      {
        extend: "print",
        text: "<i class='fa-solid fa-print fa-xl' style='color:rgb(255, 255, 255);'></i>",
        titleAttr: "Exportar a PDF",
        className: "btn-export-crud btn btn-warning"
      },
    ],


  });
});