/* === main.js === */

$(document).ready(function () {
  // Owl Carousel para aliados
  $('.owl-carousel').owlCarousel({
    loop: true,
    margin: 10,
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,
    responsive: {
      0: {
        items: 2
      },
      600: {
        items: 4
      },
      1000: {
        items: 6
      }
    }
  });

  // Activar tooltips si es necesario
  $("[data-toggle='tooltip']").tooltip();

  // Smooth scroll para anclas si se usan
  $('a[href^="#"]').on('click', function (event) {
    var target = $(this.getAttribute('href'));
    if (target.length) {
      event.preventDefault();
      $('html, body').stop().animate({
        scrollTop: target.offset().top - 60
      }, 800);
    }
  });

  // Slider/carousel si usas ID personalizado
  $('.carousel').carousel({
    interval: 6000,
    pause: 'hover'
  });
});
