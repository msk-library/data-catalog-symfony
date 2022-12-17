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

    // initialize OncoTree popovers
    $('.oncotree-list [data-toggle="popover"]').popover({
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


  // Publication Input Javascript
$('#publication_synapseid').on('keypress', function (e) {
  //if user presses spacebar within the synapseid textbox
  if(e.which === 32){

      //Disable textbox to prevent multiple submit
      $(this).attr("disabled", "disabled");

      var synid = $("#publication_synapseid").val();
      var urlfield = `https://synapse.mskcc.org/synapse/works/${synid}`;
      //Set the Synapse url for the publication
      $("#publication_url").val(urlfield);

      //Get Json for the work from Synapse API
      var synapiurl = `https://synapse.mskcc.org/synapse/work/biblio/${synid}.json?callback=?`;
      var pub_doi = "";

      $.getJSON( synapiurl, function( data ) {

        //handle multiple authors
        var anames = [];
        $.each( data[0].authors, function( key, val ) {

          // The nested array of Author objects don't have consistent keys like "name" in the Synapse API, so we have to first get the keys
          // to get the values (i.e. console.log(data[0].authors[0][242169]);)
          var cur = Object.keys(data[0].authors[key]);
          var cur_auth = data[0].authors[key][cur].replace('.', ''); //strip out periods from author names
          // separate out the last name/firstname (split at comma) for formatting display and get only first initial of fname 
          var lname = cur_auth.split(',')[0].replace(',', '');
          var initial = cur_auth.split(',')[1].substring(0,2);
          var final_name = lname + initial;

          //strip out periods from the author data and push to array
          anames.push(final_name);
        });

      //Build the citation
      var citation = "";
      citation += anames.join(", ") + ".";
      if( data[0].title_primary ) {
        citation += " " + data[0].title_primary + ".";
      }
      if( data[0].publication ) {
        citation += " " + data[0].publication + ".";
      }
      if( data[0].publication_date ) {
        citation += " " + data[0].publication_date.substr(0, 4) + ";";
      }
      if( data[0].volume ) {
        citation += " " + data[0].volume;
      }
      if( data[0].issue ) {
        citation += "(" + data[0].issue + ")";
      }
      if( data[0].start_page ) {
        citation += ":" + data[0].start_page;
      }
      if( data[0].end_page ) {
        citation += "-" + data[0].end_page;
      }
      citation += ".";

      pub_doi = data[0].doi;

  $("#publication_citation").val(citation);
  $("#publication_doi").val(pub_doi);


    });

      //Enable the textbox again if needed.
      $(this).removeAttr("disabled");
  }
});