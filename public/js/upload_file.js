function loadFileToTextArea(text_area_dest, input_file){
     readBlob(null,null,text_area_dest,input_file);
}


function readBlob(opt_startByte, opt_stopByte, text_area_dest, input_file) {
	if (window.File && window.FileReader && window.FileList && window.Blob) {
		var files;
		var file_data;

		files = document.getElementById(input_file).files;
    console.log(files.length)
		if (!files.length) return;
		var file = files[0];
		var start = parseInt(opt_startByte) || 0;
		var stop = parseInt(opt_stopByte) || file.size - 1;
		var reader = new FileReader();
		reader.onloadend = function(evt) {
      console.log(evt.target.readyState)
		  if (evt.target.readyState == FileReader.DONE) {
  			if( file.size > 1200000 ){ alert("File size exceeded! Please submit a file less than or equal to 100Mb"); }
  			else{
            document.getElementById(text_area_dest).value = evt.target.result;
  			}
		  }
		};
		var blob = file.slice(start, stop + 1);
		reader.readAsBinaryString(blob);
	}
	else {
			document.getElementById(text_area_dest).value = "Unfortunately your browser does not support file upload.";
	}
 }

( function($) {
  $('#button_input_tree_newick').on('click', function() {
      $('#file_input_tree_newick').trigger('click');
  });

  $('#button_input_put_mdcc').on('click', function() {
      $('#file_input_put_mdcc').trigger('click');
  });

} ) ( jQuery );
