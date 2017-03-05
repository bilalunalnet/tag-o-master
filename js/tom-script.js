/**
 * Created by bilalunalnet on 2.03.2017.
 */

jQuery(document).ready(function () {
   jQuery('.post-like a').click(function () {
       var button = jQuery(this);
       var post_id = button.data('post_id');
       var event = button.data('event');
       if (event == 'like') {
           button.text('dislike');
           button.data('event','unlike');
           button.attr('class', 'unlike');
       }else {
           button.text('like');
           button.data('event','like');
           button.attr('class', 'like');
       }
       jQuery.ajax({
           type : 'post',
           url : tom_ajax.ajax_url,
           data : {
               action : 'like',
               post_id : post_id,
               event : event,
               nonce : tom_ajax.nonce
           },
           success : function (response) {
               jQuery('.count').text(response);
           }
       });
   });
});
