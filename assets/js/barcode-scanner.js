( function( $ ) {

  'use strict';

  var resultContainer = document.getElementById('qr-reader-results');
  var lastResult, countResults = 0;

  function onScanSuccess(decodedText, decodedResult) {
    if (decodedText !== lastResult) {
      ++countResults;
      lastResult = decodedText;
      // Handle on success condition with the decoded message.
      console.log(`Scan result ${decodedText}`, decodedResult);


      let base_url =  'http://one.wordpress.test/test-5/?wt-user-id=' + decodedText;

      window.location.replace( base_url );

    }
  }

  var html5QrcodeScanner = new Html5QrcodeScanner(
    "wt-barcode-reader", { fps: 10, qrbox: 250 });
  html5QrcodeScanner.render(onScanSuccess);

  $( '.wt-show-barcode-scanner' ).click( function( event ) {
    $( '#wt-barcode-reader' ).toggleClass( 'ws-ls-hide' );
  });

} )( jQuery );
