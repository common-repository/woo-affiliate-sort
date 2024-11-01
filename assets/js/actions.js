jQuery(document).ready(function($){
	$('a.product_type_external').on('click',function(e){
		var product_classes = $(this).closest('li').attr('class').split(/(\s+)/);
		var product_id      = '';
		$.each( product_classes, function( index, className ) {
			if ( className.indexOf( 'wooaffpro-' ) === 0 ) {
				product_id = className.split('-')[1];
			}
		} )
		if ( product_id.length ) {
			$.ajax({

				url:WOOAFFPRO_ajax_data.ajaxUrl,
				method:'post',
				data:{
					product_id: product_id,
					action:'increase_external_populatiry',
					nonce:WOOAFFPRO_ajax_data.nonce
				}
			})
		}
	});
});