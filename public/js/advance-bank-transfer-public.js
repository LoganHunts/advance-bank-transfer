/**
 * All of the code for your admin-facing JavaScript source
 * should reside in this file.
 */
jQuery(document).ready( function($){

	jQuery(document).on( "change",".adv_receipt_attachment", function() {
	
		var file = jQuery( ".adv_receipt_attachment" ).prop( 'files' );

		jQuery( '.adv_receipt_attached' ).val( '' );
		jQuery( '#progress-wrp' ).removeClass( "progress-complete" );
		jQuery( '#progress-wrp' ).removeClass( "progress-failed" );
		jQuery( '#progress-wrp .status' ).text( "Processing" );
		jQuery( '#progress-wrp' ).show();

		var upload = new FormData(); 
	
		upload.append( "receipt", file[0] ); 
		upload.append( "auth_nonce", adv_bank_transfer.auth_nonce ); 
		upload.append( "action", "perform_upload" );

		jQuery.ajax({ 
			url: adv_bank_transfer.ajaxurl, 
			type: 'POST', 
			processData: false, 
			contentType: false, 
			dataType: 'json', 
			data: upload ,
			success: function( response ) {
				if( 'success' == response[ 'result' ] ) {
					jQuery( '.adv_receipt_remove_attachment' ).show();
					jQuery( '.adv_receipt_attached' ).val( response.url );
					jQuery( '#progress-wrp' ).addClass( "progress-complete" );
					jQuery( '.adv_receipt_field ' ).removeClass( "is_hidden" );
					jQuery( '#progress-wrp .status' ).text( "Completed" );

					// Add removal scripts.
					jQuery(document).on( "click",".adv_receipt_remove_attachment", function() {

						var removal = new FormData(); 
	
						removal.append( "path", response.path ); 
						removal.append( "auth_nonce", adv_bank_transfer.auth_nonce ); 
						removal.append( "action", "remove_current_upload" );

						jQuery.ajax({ 
							url: adv_bank_transfer.ajaxurl, 
							type: 'POST', 
							processData: false, 
							contentType: false, 
							dataType: 'json', 
							data: removal,
							success: function( response ) {
								if( 'success' == response[ 'result' ] ) {
									jQuery( '.adv_receipt_remove_attachment' ).hide();
									jQuery( '#progress-wrp' ).removeClass( "progress-complete" );
									jQuery( '#progress-wrp' ).addClass( "progress-failed" );
									jQuery( '#progress-wrp .status' ).text( "Removed" );
								}
								else if( 'failure' == response[ 'result' ] ) {
									jQuery( '#progress-wrp' ).removeClass( "progress-complete" );
									jQuery( '#progress-wrp' ).addClass( "progress-failed" );
									jQuery( '#progress-wrp .status' ).text( "Something Went Wrong. Please refresh!" );
								}
							}
						});
					});
				}

				else if( 'failure' == response[ 'result' ] ) {
					jQuery( '#progress-wrp' ).addClass( 'progress-failed' );
					jQuery( '#progress-wrp .status' ).text( response[ 'errors' ][0] );
				}
            }
		});
	});

// End of scripts.
});