// initialize Core Facility popovers
// These custom detailed options make the content area hoverable vs the bootstrap defaults where
// the popover disappears when you navigate to the content area.
$('.core_facilities-list [data-toggle="popover"]').popover({
  'html':true,
  'animation':false,
  'trigger':'manual',
  'placement':'bottom',
}).on("mouseenter", function () {
  var _this = this;
  $(this).popover("show");
  $(".popover").on("mouseleave", function () {
    $(_this).popover('hide');
  });
}).on("mouseleave", function () {
  var _this = this;
  setTimeout(function () {
      if (!$(".popover:hover").length) {
          $(_this).popover("hide");
      }
  }, 200);
});