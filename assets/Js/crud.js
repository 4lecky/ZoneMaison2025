$(document).ready(function () {
  $('#usuarios').DataTable({
    // Opciones de DOM para personalizar la interfaz
    dom: "lfrtip", // Cambié la opción "Bfrtilp" a "Bfrtip" para asegurar compatibilidad con tabla y paginación.

    // Definir las opciones de longitud
    lengthMenu: [10, 20, 50, 100],

    // Habilitar la opción responsive
    responsive: true,

    // Configurar idioma
    language: {
      lengthMenu: "Mostrar _MENU_ registros por página",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando página _PAGE_ de _PAGES_",
      infoEmpty: "No hay registros disponibles",
      infoFiltered: "(filtrado de _MAX_ registros en total)",
      search: "Buscar:",
      paginate: {
        previous: "Anterior",
        next: "Siguiente"
      }
    }
  });
});