<div class="pagetitle">
      <h1>Certificate</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Certificate Participant Wise</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('certificate/certificate/get_certificate_pdf_participant_wise', array('id' => 'formIRL')); ?>
<div id="content_id">
    <?php $this->load->view('certificate/participant_item_details', $Contents); ?>
</div>
<?php
//itemwise_report_interface
echo form_close();
?>