<?php
	session_start();

	$_SESSION = array();
?>
<link href="css/websat.css" rel="stylesheet" type="text/css" />
<form name="sat_form" action="sat.php" method="POST" enctype="MULTIPART/form-data">
<table width="600"  border="0" cellspacing="0" cellpadding="0">

<tr>
		<td><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="25%" height="20" class="labels"><strong>Motif Length:</strong></td>
            <td width="12%" align="left" class="labels"><label>
              <input name="mono" type="checkbox" id="mono" value="true" checked="checked" />
              Mono</label></td>
            <td width="11%" align="left" class="labels"><label>
              <input name="di" type="checkbox" id="di" value="true" checked="checked" />
              Di</label></td>
            <td width="11%" align="left" class="labels"><label>
              <input name="tri" type="checkbox" id="tri" value="true" checked="checked" />
              Tri</label></td>
            <td width="13%" align="left" class="labels"><label>
              <input name="tetra" type="checkbox" id="tetra" value="true" checked="checked" />
              Tetra</label></td>
            <td width="14%" align="left" class="labels"><label>
              <input name="penta" type="checkbox" id="penta" checked="checked" />
              Penta</label></td>
            <td width="14%" align="left" class="labels"><label>
              <input name="hexa" type="checkbox" id="hexa" checked="checked" />
              Hexa</label></td>
          </tr>
          <tr>
            <td height="20" class="labels"><strong>Repeat Minimum:</strong></td>
            <td align="left" class="labels"><label>
              <input name="mmono" type="text" class="labels" id="mmono" value="6" size="3" maxlength="3" />
            </label></td>
            <td align="left" class="labels"><label>
              <input name="mdi" type="text" class="labels" id="mdi" value="6" size="3" maxlength="3" />
            </label></td>
            <td align="left" class="labels"><label>
              <input name="mtri" type="text" class="labels" id="mtri" value="6" size="3" maxlength="3" />
            </label></td>
            <td align="left" class="labels"><label>
              <input name="mtetra" type="text" class="labels" id="mtetra" value="6" size="3" maxlength="3" />
            </label></td>
            <td align="left" class="labels"><label>
              <input name="mpenta" type="text" class="labels" id="mpenta" value="6" size="3" maxlength="3" />
            </label></td>
            <td align="left" class="labels"><label>
              <input name="mhexa" type="text" class="labels" id="mhexa" value="6" size="3" maxlength="3" />
            </label></td>
          </tr>
        </table></td>
	</tr>
	<tr>
		<td height="50" align="center"><strong><a href="http://www.youtube.com/watch?v=LwWrEaGY1Oc" target="_blank" class="labels">Help: video instructions on how to use WebSat</a></strong></td>
	</tr>
	<tr>
		<td align="center" height="40" class="labels">Enter either
			a raw DNA sequence or a sequence (or multiple sequences)
			in FASTA format (at most 150,000 characters) </td>
	</tr>
	<tr>
		<td align="center"><textarea id="text_area" name="sequence" class="textarea" rows="10" cols="80"></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center">
			<input type="button" name="Button" value="Submit It!" onclick="validaForm();" />
            &nbsp;
            <input type="reset" name="Submit2" value="Reset" />		</td>
	</tr>
	<tr>
		<td align="center"><a href="javascript:sampleSequence();" class="linkSample" title="Click here for a sample sequence.">Use a sample sequence</a></td>
	</tr>
	<tr>
		<td height="30">&nbsp;</td>
	</tr>
</table>
</form>

<form name="upload_form" action="websat/upload.php" method="POST" enctype="MULTIPART/form-data">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="labels" align="center">... or upload a file with the sequences</td>
	</tr>
	<tr>
		<td align="center"><input type="file" name="fileSeqUpload" class="input" size="60" value="" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center">
			<input type="button" name="Button" value="Submit It!" onClick="uploadSequence();" />
            &nbsp;
            <input type="reset" name="Submit2" value="Reset" />
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>
</form>
