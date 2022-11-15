'use strict';

const wt_barcode_reader           = new Html5Qrcode( 'wt-barcode-reader', false);
const wt_barcode_devices_list     = document.getElementById('wt-barcode-reader-devices-list');

// Callback for a successful scan
let wt_barcode_callback_success = ( decodedText, decodedResult ) => {

  wt_barcode_beep();

  wt_barcode_reader.stop()

  const div = document.getElementById('wt-barcode-reader');

  div.innerHTML = `<div class="ykuk-margin-bottom-remove"><div ykuk-spinner="ratio: 3"></div></div>`;

  wt_barcode_redirect( decodedText );
};

/**
 * Start barcode reader
 */
function wt_barcode_camera_initialise( device_id = null ) {

  // Check localstorage?
  if ( null === device_id ) {
    device_id = localStorage.getItem('ws-ls-barcode-device' ) || null;
  }

  let wt_barcode_library_config = { fps: 10, qrbox: { width: 250, height: 250 }};

  device_id = ( null === device_id ) ? { facingMode: "environment" } : device_id;

  wt_barcode_reader.start(device_id, wt_barcode_library_config, wt_barcode_callback_success);
}

/**
 * Show barcode reader for hand held
 */
function wt_barcode_lazer_show() {

  const div = document.getElementById('ykuk-barcode-lazer-container');

  if ( div.classList.contains( 'ws-ls-hide' ) ) {
    div.classList.remove('ws-ls-hide');
  } else {
    div.classList.add('ws-ls-hide');
    return;
  }
  
  let barcode_reader = document.getElementById('ykuk-barcode-lazer-value');
  barcode_reader.focus();

  // If we have a value inserted into the text field, disable and redirect
  barcode_reader.addEventListener('change', (event) => {

    barcode_reader.readOnly = true;

    wt_barcode_beep();

    wt_barcode_redirect( barcode_reader.value );
  });
}

/**
 * Redirect to user record
 * @param user_id
 */
function wt_barcode_redirect( user_id ) {
  let base_url = wt_barcode_scanner_config['current-url'] + '?' +
    wt_barcode_scanner_config[ 'querystring-key-user-id' ] + '=' + user_id;

  window.location.replace( base_url );
}

/**
 * Populate drop down list of available cameras
 */
function wt_barcode_reader_show() {

  Html5Qrcode.getCameras().then(devices => {
    if ( devices && devices.length ) {

      for(let i in devices) {
        wt_barcode_devices_list.add(new Option(devices[i].label, devices[i].id));
      }

      // If we have a stored selected value then set.
      let selected_device = localStorage.getItem('ws-ls-barcode-device' ) || null;

      if( null !== selected_device ) {
        wt_barcode_devices_list.value = selected_device;
      }

      const div = document.getElementById('ykuk-barcode-reader-container');
      div.classList.remove('ws-ls-hide');

      wt_barcode_camera_initialise();
    }
  }).catch(err => {
    alert( wt_barcode_scanner_config[ 'text-error-loading-cameras' ] );
  });

  return null;
}

// Has the user selected another Camera? Yes, show scanner by default!
if( 'undefined' !== typeof( wt_barcode_querystring_value('camera-id') ) ) {
  wt_barcode_reader_show();
}

/**
 * Upon a selecting a device, set device ID and reload page
 */
wt_barcode_devices_list.addEventListener('change', (event) => {

  localStorage.setItem('ws-ls-barcode-device', wt_barcode_devices_list.value );

  let base_url = wt_barcode_scanner_config['current-url'] + '?camera-id=' + wt_barcode_devices_list.value;

  window.location.replace( base_url );

});

/*
  Make a beep sound!
 */
function wt_barcode_beep() {
  let beep = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");
  beep.play();
}

/**
 * Fetch a querystring value for the given key
 * @param key
 * @returns {string}
 */
function wt_barcode_querystring_value( key ) {

  let page_url    = window.location.search.substring( 1 );
  let qs_values   = page_url.split('&' );

  for ( let i = 0; i < qs_values.length; i++ ) {

    let qs_name = qs_values[ i ].split( '=' );

    if ( key === qs_name[0]) {
      return qs_values[ i ];
    }
  }
}
