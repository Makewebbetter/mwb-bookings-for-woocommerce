jQuery(document).ready(function($){
    jQuery('.wps-mbfw-user-booking-meta-data-listing').slideUp();
    jQuery('.wps-mbfw-ser-booking-toggler').on('click',function(){
        jQuery(this).toggleClass('booking-toggler-reverse');
        jQuery(this).siblings('.wps-mbfw-user-booking-meta-data-listing').slideToggle('slow');
    })
});