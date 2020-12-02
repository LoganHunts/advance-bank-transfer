/**
 * All of the code for your admin-facing JavaScript source
 * should reside in this file.
 */
jQuery(document).ready( function($){

	jQuery(document).on("change",".adv_receipt_attachment", function() {
	
		var file = jQuery( ".adv_receipt_attachment" ).prop( 'files' );
		var upload = new FormData(); 
	
		upload.append( "receipt", file[0] ); 
		upload.append( "base_dir", adv_bank_transfer.base_dir ); 

		$.ajax({ 
			url: adv_bank_transfer.ajaxurl, 
			type: 'POST', 
			processData: false, 
			contentType: false, 
			dataType: 'json', 
			data: upload 
		});
		
	});

// End of scripts.
});
























	// Perform upload.
		// jQuery( '#progress-wrp' ).removeClass( 'is_hidden' );

		// const result = await jQuery.ajax({
		// 	type: 'POST',
		// 	url: adv_bank_transfer.ajaxurl,
		// 	timeout: 60000,
		// 	datatype: 'json',
		// 	data: { 
		// 		nonce : adv_bank_transfer.auth_nonce,
		// 		action: 'perform_upload',
		// 		fileName: file.name, // uploaded file Name.
		// 		fileType: file.type, // uploaded file Type.
		// 		fileSize: file.size, // uploaded file Size.
		// 	}
		// });
		// 
		// function processing( event ) {
		// 	var percent = 0;
		// 	var position = event.loaded || event.position;
		// 	var total = event.total;
		// 	var progress_bar_id = "#progress-wrp";
		// 	if (event.lengthComputable) {
		// 		percent = Math.ceil(position / total * 100);
		// 	}
	
		// 	// Update progressbars classes so it fits your code.
		// 	$(progress_bar_id + " .progress-bar").css("width", +percent + "%");
		// 	$(progress_bar_id + " .status").text(percent + "%");
		// }