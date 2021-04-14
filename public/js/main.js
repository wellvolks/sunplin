
function sunplin(){
  var valid_form = validaForm();
  if( valid_form != "" ){
     document.getElementById('form_main').submit()
  }
}

function updatePageAfterSucess(result){
  var ret = result.split("\n");
  var mat_status = 'error';
  var mat_info = '';
  var tree_status = 'error';
  var tree_info = '';
  var ext_mat = 'txt';
  var ext_tree = 'txt';

  document.getElementById("status_info").innerHTML = 'Sunpling was successful'
  document.getElementById("status_sunplin").className = "status__root___2rxe7 status__success___2asG5";
  document.getElementById("status_sunplin").innerHTML = '<svg width="50" height="50" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"></path><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"></path></svg>';

  for (i = 0; i < ret.length; i++){
    st_inf_ret = ret[i].split(":");
    status = st_inf_ret[0].replace(/[\n\r]+/g, '');

    if (status == 'mat_status'){
      mat_status = st_inf_ret[1].replace(/[\n\r]+/g, '');
    }
    else if (status == 'tree_status'){
      tree_status = st_inf_ret[1].replace(/[\n\r]+/g, '');
    }
    else if (status == 'mat_info'){
      mat_info = '';
      for(var j = 1; j < st_inf_ret.length; j++){
        mat_info += st_inf_ret[j].replace(/[\n\r]+/g, '');
        mat_info += ' ';
      }
    }
    else if (status == 'tree_info'){
      tree_info = '';
      for(var j = 1; j < st_inf_ret.length; j++){
        tree_info += st_inf_ret[j].replace(/[\n\r]+/g, '');
        tree_info += ' ';
      }
    }
    else if (status == 'ext_mat'){
      ext_mat = st_inf_ret[1].replace(/[.\n\r]+/g, '');
    }
    else if (status == 'ext_tree'){
      ext_tree = st_inf_ret[1].replace(/[.\n\r]+/g, '');
    }
    console.log("\n");
    
  }

  var info_fail = '';
  if (tree_status == 'ok'){
    document.getElementById("down_tree").setAttribute( "onClick", 'downloadFile("/download/trees/' + ext_tree + '", "down_tree", "' + ext_tree + '")' );
    document.getElementById("down_tree").className = "button-download-file";
  }
  else if (tree_status == 'fail'){
    info_fail += 'trees fail: ' + tree_info;
    document.getElementById("down_tree").className = "button-download-file-fail";
    document.getElementById("down_tree").setAttribute( "onClick", 'alert("File unavailable. ' + tree_info + '" )');
  }
  else{
    document.getElementById("down_tree").className = "button-download-file-missing";
    document.getElementById("down_tree").setAttribute( "onClick", 'alert("File unavailable. ' + tree_info + '" )');
  }

  if (mat_status == 'ok'){
    document.getElementById("down_mat").setAttribute( "onClick", 'downloadFile("/download/dist/' + ext_mat + '", "down_mat", "' + ext_mat + '")' );
    document.getElementById("down_mat").className = "button-download-file" ;
  }
  else if (mat_status == 'fail'){
    if(info_fail.length > 0){
      info_fail += ' / ';
    }
    info_fail += 'distance matrices fail: ' + mat_info;
    document.getElementById("down_mat").className = "button-download-file-fail" ;
    document.getElementById("down_mat").setAttribute( "onClick", 'alert("File unavailable. ' + mat_info + '" )');
  }
  else{
    document.getElementById("down_mat").className = "button-download-file-missing";
    document.getElementById("down_mat").setAttribute( "onClick", 'alert("File unavailable. ' + mat_info + '" )');
  }

  if (info_fail.length > 0){
    updatePageAfterError(info_fail);
  }
}

function downloadFile(url, elemId, extFile){
  var nameFile = ((elemId == "down_mat") ? ("sunplin-distance-mat.") : ("sunplin-trees.")) + extFile;
  jQuery(function($){
    $.ajax({
        url: url,
        type: "GET",
        success: function(result){
          var link=document.createElement('a');
          link.href=url;
          link.click();
        },
        error: function(error){
          document.getElementById(elemId).className = "button-download-file-fail";
          document.getElementById(elemId).setAttribute( "onClick", 'alert("File unavailable. ' + error + '" )');
        }
      })
    });
}

function updatePageAfterError(error){
  document.getElementById("status_info").innerHTML = 'Sunpling failed! Click <a href="#" onclick="alert(\'' + error + '\')">here</a> for more information.'
  document.getElementById("status_sunplin").className = "status__root___2rxe7 status__failure___1Viva";
  document.getElementById("status_sunplin").innerHTML = '<svg width="50" height="50" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>';

  //document.getElementById("down_tree").className = "button-download-file-fail";
  //document.getElementById("down_tree").setAttribute( "onclick", 'alert("File unavailable." )');
  //document.getElementById("down_mat").className = "button-download-file-fail";
  //document.getElementById("down_mat").setAttribute( "onclick", 'alert("File unavailable." )');
}

jQuery(function($) {
  $('#tr_tree_newick')
    .on('mouseenter', function() { $('#tr_tree_newick').css('background-color','rgb(242,242,242)'); })
    .on('mouseleave', function() { $('#tr_tree_newick').css('background-color','rgb(255,255,255)'); })
  $('#tr_put_mdcc')
    .on('mouseenter', function() { $('#tr_put_mdcc').css('background-color','rgb(242,242,242)'); })
    .on('mouseleave', function() { $('#tr_put_mdcc').css('background-color','rgb(255,255,255)'); })
  $('#tr_based')
    .on('mouseenter', function() { $('#tr_based').css('background-color','rgb(242,242,242)'); })
    .on('mouseleave', function() { $('#tr_based').css('background-color','rgb(255,255,255)'); })
  $('#tr_options')
    .on('mouseenter', function() { $('#tr_options').css('background-color','rgb(242,242,242)'); })
    .on('mouseleave', function() { $('#tr_options').css('background-color','rgb(255,255,255)'); })
});

jQuery(function($) {
  $('#checkbox_node_based').on('change', function(){
    if(this.checked) {
      $('#checkbox_branch_based').removeAttr('checked','checked');
    }
  });
  $('#checkbox_branch_based').on('change', function(){
    if(this.checked) {
      $('#checkbox_node_based').removeAttr('checked','checked');
    }
  });
});

( function($) {
  $('#use_sample_tree').change(function() {
      if(this.checked) {
          $('#form_tree_newick').val('(((A:0.3,B:0.4)C:0.3,D:0.4)E:0.2,(F:0.3,(G:0.3,H:0.4)I:0.4)J:0.5)K;');
          $('#form_tree_newick').attr('readonly', 'readonly');
          $('#file_input_tree_newick').val('')
          $('#button_input_tree_newick').removeClass('button-upload-file')
          $('#button_input_tree_newick').addClass('button-upload-file-disable')
          $('#file_input_tree_newick').attr('disabled', 'disabled')
      }
      else{
        $('#form_tree_newick').val('');
        $('#form_tree_newick').removeAttr('readonly');
        $('#button_input_tree_newick').removeClass('button-upload-file-disable')
        $('#button_input_tree_newick').addClass('button-upload-file')
        $('#file_input_tree_newick').removeAttr('disabled')
      }
  });

  $('#use_sample_species').change(function() {
      if(this.checked) {
          $('#form_put_mdcc').val('PUT1 A\nPUT2 G\nPUT3 E\nPUT4 D\nPUT5 H\nPUT6 A');
          $('#form_put_mdcc').attr('readonly', 'readonly');
          $('#file_input_put_mdcc').val('')
          $('#button_input_put_mdcc').removeClass('button-upload-file')
          $('#button_input_put_mdcc').addClass('button-upload-file-disable')
          $('#file_input_put_mdcc').attr('disabled', 'disabled')
      }
      else{
        $('#form_put_mdcc').val('');
        $('#form_put_mdcc').removeAttr('readonly');
        $('#button_input_put_mdcc').removeClass('button-upload-file-disable')
        $('#button_input_put_mdcc').addClass('button-upload-file')
        $('#file_input_put_mdcc').removeAttr('disabled')
      }
  });

  $("#return_to_home").click(function(){
    $.get("/", function(data, status){
        window.location.href = '/'
    });
  });

} ) ( jQuery );
