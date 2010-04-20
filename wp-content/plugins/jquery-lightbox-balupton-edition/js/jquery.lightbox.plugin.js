jQuery(function($) {

  //K2 fix
  
  if (typeof K2 !== "undefined") {
    function Lightbox() {
      this.updateImageList = function() {
        $.Lightbox.relify();
      }
    }
    
    var myLightbox = new Lightbox();
  }
  
  //Native Wordpress Gallery fix
  
  $(".gallery").each(function(index, obj) {
    $(obj).find("a").lightbox();
  } );
  
} );