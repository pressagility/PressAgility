jQuery(document).ready(function ($) {
  
  //set url
  let currentUrl = window.location.href;
  
  //add data-action
  $( '.pressagility-selective-page-btn' ).each(function () {
    var id = this.id.replace('wp-admin-bar-pressagility-selective-page-cache-', '');
    $(this).attr('data-action', id);
  });
  
  // Initial check
  sendAPIRequest( 'check', currentUrl );
  
  
  
  
  function showButtons(ids) {
    $( '.pressagility-selective-page-btn' ).hide();
    ids.forEach(function (id) {
      $('#wp-admin-bar-pressagility-selective-page-cache-' + id).show();
    });
  }
  
  
  $( '.pressagility-selective-page-btn' ).on('click', function (e) {
    e.preventDefault();
    var action = $(this).data('action');
		
		// Show confirmation dialog only for "flush"
		if( action === 'flush' ){
			if( !confirm('Are you sure you want to delete the entire page cache?') ){
				return; // Exit if user cancels
			}
		}
	
    sendAPIRequest( action, currentUrl, true );
  });
  
  
  function sendAPIRequest( action, currentUrl, sendAlert=false ){
    $.ajax({
      url: WPPA_PRESSAGILITY_DATA.api_url,
      method: 'POST',
      data: {
        action: action,
        url: currentUrl
      },
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', WPPA_PRESSAGILITY_DATA.nonce);
      },
      success: function (res) {
        
        if( sendAlert ){
          alert( 'Selective Page Cache "' + action + '" action completed. Cache is updating in the background and will reflect across all servers soon.' );
        }else{
          if( res.body.status ){
            $( '#pressagility-selective-page-cache-check' ).text('✅ Cached!');
            showButtons(['refresh', 'remove', 'flush']);
          }else{
            $( '#pressagility-selective-page-cache-check' ).text('❌ Not Cached!');
            showButtons(['add', 'flush']);
          }
        }
        
      },
      error: function (xhr) {
        $( '#pressagility-selective-page-cache-check' ).text( 'Error checking cache' );
      }
    });
  }
  
});