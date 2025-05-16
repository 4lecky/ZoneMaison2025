document.getElementById('menu_toggle').addEventListener('click', function() {

  //id del nav
  var nav = document.getElementById('main_nav');

  //id de los iconos
  var iconOpen = document.getElementById('icon_open');
  var iconClose = document.getElementById('icon_close');

  nav.classList.toggle('nav_expanded');
  nav.classList.toggle('nav_collapsed');


//Condicional para mostrar iconos segun el estado del menu
    if (nav.classList.contains('nav_expanded')) {
        iconOpen.style.display = '';
        iconClose.style.display = 'none';
    } else {
        iconOpen.style.display = 'none';
        iconClose.style.display = '';
    }


});