(function () {
  "use strict";
  window.addEventListener(
    "load",
    function () {
      const carrusel = $(".owl-carousel");

      console.log(carrusel);
      if (carrusel.length !== 0) {
        $(".owl-carousel").owlCarousel({
          autoplay: true,
          dots: false,
          loop: true,
          margin: 20,
          autoHeight: true,
          responsiveClass: true,
          responsive: {
            0: {
              items: 1,
              nav: true,
            },
            600: {
              items: 3,
              nav: false,
            },
            1000: {
              items: 5,
              nav: true,
              loop: false,
            },
            1000: {
              items: 6,
              nav: true,
              loop: false,
            },
          },
        });
      }
    },
    false
  );
})();
