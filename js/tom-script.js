/**
 * Created by PhpStorm.
 * User: bilalunalnet
 * Date: 05.03.2017
 * Time: 10:01
 */

jQuery(document).ready(function () {
   jQuery('.post-like a').click(function () {
       jQuery("#load").show();
       var button = jQuery(this);
       var post_id = button.data('post_id');
       var event = button.data('event');
       if (event == 'like') {
           button.text('dislike');
           button.data('event','unlike');
       }else {
           button.text('like');
           button.data('event','like');
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
               jQuery("#load").hide();
               jQuery('.count').text(response);
           }
       });
   });
});
