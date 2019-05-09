 jQuery(document).ready(function($) {

  /*------------ Swipper --------------*/
 var swiper = new Swiper('.swiper-container', {
      slidesPerView: 6,
      spaceBetween: 30,
      speed:600,
      loop:true,
      /*slidesPerGroup: 3,*/
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      lazy: {
      loadPrevNext: true,
      },
      autoplay: {
        delay: 1000,
      },
      breakpoints: {
      // when window width is <= 420px
      420: {
        slidesPerView: 1,
        spaceBetween: 10
      },
      // when window width is <= 540px
      540: {
        slidesPerView: 2,
        spaceBetween: 20
      },
      // when window width is <= 720px
      720: {
        slidesPerView: 3,
        spaceBetween: 30
      },
      // when window width is <= 1024px
      1024: {
        slidesPerView: 4,
        spaceBetween: 40
      },
      // when window width is <= 1280px
      1280: {
        slidesPerView: 5,
        spaceBetween: 50
      }
    }
    });
 console.log("Funciona el swipper");


 /*--------- uploadFile image -------------*/
 var file_frame;

  jQuery.fn.upload_listing_image = function( button ) {
    var button_id = button.attr('id');
    var field_id = button_id.replace( '_button', '' );

    // If the media frame already exists, reopen it.
    if ( file_frame ) {
      file_frame.open();
      return;
    }

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: jQuery( this ).data( 'uploader_title' ),
      button: {
        text: jQuery( this ).data( 'uploader_button_text' ),
      },
      multiple: false
    });

    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      var attachment = file_frame.state().get('selection').first().toJSON();
      jQuery("#"+field_id).val(attachment.id);
      jQuery("#listingimagediv img").attr('src',attachment.url);
      //jQuery("#listingimagediv img").attr('srcset',attachment.url);
      jQuery( '#listingimagediv img' ).show();
      jQuery( '#' + button_id ).attr( 'id', 'remove_listing_image_button' );
      jQuery( '#remove_listing_image_button' ).text( 'Remove container image' );
      //console.log("imagen selecionada");
    });

    // Finally, open the modal
    file_frame.open();
  }

  jQuery('#listingimagediv').on( 'click', '#upload_listing_image_button', function( event ) {
    event.preventDefault();
    jQuery.fn.upload_listing_image( jQuery(this) );
  });

  jQuery('#listingimagediv').on( 'click', '#remove_listing_image_button', function( event ) {
    event.preventDefault();
    jQuery( '#upload_listing_image' ).val( '' );
    jQuery( '#listingimagediv img' ).attr( 'src', '' );
    jQuery( '#listingimagediv img' ).attr('srcset', '');
    jQuery( '#listingimagediv img' ).hide();
    jQuery( this ).attr('id', 'upload_listing_image_button');
    jQuery( '#upload_listing_image_button' ).text( 'Set containers image' );
    //console.log("imagen eliminada");
  });
});
