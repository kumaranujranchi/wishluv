$("#latest-offer").owlCarousel({
  loop: true,
  margin: 10,
  nav: true,
  dots: false,
  margin: 10,
  responsive: {
    0: {
      items: 1,
    },
    575: {
      items: 1,
    },
    768: {
      items: 2,
    },
    992: {
      items: 2,
    },
    1200: {
      items: 2,
    },
    1201: {
      items: 2,
    },
  },
});

$("#testimonial").owlCarousel({
  loop: true,
  margin: 10,
  nav: false,
  dots: true,
  margin: 10,
  responsive: {
    0: {
      items: 1,
    },
    1024: {
      items: 2,
    },
  },
});

$("#ongoing").owlCarousel({
  loop: true,
  margin: 10,
  nav: true,
  items: 1,
  dots: false,
});
// let mybutton = document.getElementById("myBtn");
// window.onscroll = function() {scrollFunction()};

// function scrollFunction() {
//   if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
//     mybutton.style.display = "block";
//   } else {
//     mybutton.style.display = "none";
//   }
// }
// function topFunction() {
//   document.body.scrollTop = 0;
//   document.documentElement.scrollTop = 0;
// }
jQuery(document).ready(function () {
  var modernAccordion = $(".mdn-accordion");
  if (modernAccordion.length > 0) {
    modernAccordion.each(function () {
      var each_accordion = $(this);
      $(".accordion-toggle:checked")
        .siblings("ul")
        .attr("style", "display:none;")
        .stop(true, true)
        .slideDown(300);
      each_accordion.on("change", ".accordion-toggle", function () {
        var toggleAccordion = $(this);
        if (toggleAccordion.is(":radio")) {
          toggleAccordion
            .closest(".mdn-accordion")
            .find('input[name="' + $(this).attr("name") + '"]')
            .siblings("ul")
            .attr("style", "display:block;")
            .stop(true, true)
            .slideUp(300);
          toggleAccordion
            .siblings("ul")
            .attr("style", "display:none;")
            .stop(true, true)
            .slideDown(300);
        } else {
          toggleAccordion.prop("checked")
            ? toggleAccordion
                .siblings("ul")
                .attr("style", "display:none;")
                .stop(true, true)
                .slideDown(300)
            : toggleAccordion
                .siblings("ul")
                .attr("style", "display:block;")
                .stop(true, true)
                .slideUp(300);
        }
      });
    });
  }
  $(document).on("click", ".mdn-accordion .accordion-title", function (e) {
    var $mdnRippleElement = $('<span class="mdn-accordion-ripple" />'),
      $mdnButtonElement = $(this),
      mdnBtnOffset = $mdnButtonElement.offset(),
      mdnXPos = e.pageX - mdnBtnOffset.left,
      mdnYPos = e.pageY - mdnBtnOffset.top,
      mdnSize = parseInt(
        Math.min($mdnButtonElement.height(), $mdnButtonElement.width()) * 0.5
      ),
      mdnAnimateSize = parseInt(
        Math.max($mdnButtonElement.width(), $mdnButtonElement.height()) *
          Math.PI
      );
    $mdnRippleElement
      .css({
        top: mdnYPos,
        left: mdnXPos,
        width: mdnSize,
        height: mdnSize,
        backgroundColor: $mdnButtonElement.data("accordion-ripple-color"),
      })
      .appendTo($mdnButtonElement)
      .animate(
        {
          width: mdnAnimateSize,
          height: mdnAnimateSize,
          opacity: 0,
        },
        800,
        function () {
          $(this).remove();
        }
      );
  });
});
