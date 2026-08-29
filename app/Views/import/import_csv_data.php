<div class="pagetitle">
      <h1>Settings</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Import CSV Data</li>
        </ol>
      </nav>
</div>
<div align="center" class="heading_gray">
    <h3></h3>
</div>
<br/>
<?php
if ($import_completed == 'NO') {
    echo form_open_multipart('import/import_data', array('id' => 'formKalolsavam'));
    ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
        <div class="col-md-12 col-xl-4">
            <div class="card">
                <div class="card-body">
        <tr>
            <th align="left" colspan="4">Import Kalolsavam CSV Data</th>
        </tr>
        <tr>
            <td align="left" class="table_row_first">Upload Kalolsavam CSV File</td>
            <td align="left" class="table_row_first">
                <?php echo form_upload("kalolsavamCSV", 'class="form-control" id="kalolsavamCSV" '); ?>
                <span class="guide_line"></span>
            </td>
            <td align="left" colspan="2" class="table_row_first">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                <?php echo form_submit('save_kalolsavam', 'Save', 'id="save_kalolsavam" onClick="javascript:fncSaveKalolsavamCSV();" class="btn btn-primary"'); ?>
            </td>
        </tr>
    </table>
</div>
</div>
</div>
    <?php
    echo form_close();
}
?>