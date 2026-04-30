
/**
* Record outbound link clicks for Analytics
*/
var trackOutboundLink = function (url, label) {
  gtag('event', 'click', {
    'event_category': 'outbound',
    'event_label': label,
    'transport_type': 'beacon'
  });
}


/**
* Initialize Author popovers for Bootstrap 5
*/
$(function () {
  // Function to initialize popovers with bottom placement
  function initAuthorPopovers(selector) {
    $(selector).each(function () {
      var popover = new bootstrap.Popover(this, {
        html: true,
        animation: false,
        trigger: 'manual',
        placement: 'bottom',
        container: 'body' // Prevent container clipping
      });

      $(this).on('mouseenter', function () {
        var _this = this;
        popover.show();

        // Make popover hoverable
        setTimeout(function () {
          $('.popover').on('mouseleave', function () {
            popover.hide();
          });
        }, 10);
      }).on('mouseleave', function () {
        var _this = this;
        setTimeout(function () {
          if (!$('.popover:hover').length) {
            popover.hide();
          }
        }, 200);
      });
    });
  }

  // Initialize for both Bootstrap 4 and 5 attributes
  initAuthorPopovers('.dataset-authors-section [data-toggle="popover"]');
  initAuthorPopovers('.dataset-authors-section [data-bs-toggle="popover"]');


  // Initialize Publisher popovers for Bootstrap 5
  function initPublisherPopovers(selector) {
    $(selector).each(function () {
      var popover = new bootstrap.Popover(this, {
        html: true,
        animation: false,
        trigger: 'manual',
        placement: 'bottom', // Changed from 'top' to 'bottom'
        container: 'body'
      });

      $(this).on('mouseenter', function () {
        popover.show();

        setTimeout(function () {
          $('.popover').on('mouseleave', function () {
            popover.hide();
          });
        }, 10);
      }).on('mouseleave', function () {
        setTimeout(function () {
          if (!$('.popover:hover').length) {
            popover.hide();
          }
        }, 200);
      });
    });
  }

  initPublisherPopovers('.publishers-list [data-toggle="popover"]');
  initPublisherPopovers('.publishers-list [data-bs-toggle="popover"]');


  // Initialize local expert popovers for Bootstrap 5
  function initLocalExpertPopovers(selector) {
    $(selector).each(function () {
      var popover = new bootstrap.Popover(this, {
        html: true,
        animation: false,
        trigger: 'manual',
        placement: 'bottom', // Changed from 'right' to 'bottom'
        container: 'body'
      });

      $(this).on('mouseenter', function () {
        popover.show();

        setTimeout(function () {
          $('.popover').on('mouseleave', function () {
            popover.hide();
          });
        }, 10);
      }).on('mouseleave', function () {
        setTimeout(function () {
          if (!$('.popover:hover').length) {
            popover.hide();
          }
        }, 200);
      });
    });
  }

  initLocalExpertPopovers('.local-expert-popover-link');
})
