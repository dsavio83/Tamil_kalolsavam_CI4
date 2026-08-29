<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
.kalolsavam-wrapper{padding:20px}
.form-card{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:20px;padding:30px;box-shadow:0 20px 60px rgba(102,126,234,0.4);color:white;margin-bottom:30px;animation:slideDown 0.4s ease-out}
@keyframes slideDown{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
.form-card h3{color:white;margin-bottom:25px;font-weight:600}
.form-card .form-label{color:rgba(255,255,255,0.95);font-weight:500;margin-bottom:8px;display:block}
.form-card .form-control{background:rgba(255,255,255,0.95);border:none;border-radius:10px;padding:12px 15px;width:100%;box-sizing:border-box}
.form-card .form-control:focus{background:white;outline:none;box-shadow:0 0 0 3px rgba(255,255,255,0.3)}
.date-wrapper{position:relative}
.date-wrapper input{padding-right:45px}
.cal-icon{position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:20px}
.logo-preview{background:rgba(255,255,255,0.1);border-radius:15px;padding:20px;text-align:center}
.logo-preview img{max-width:200px;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.3)}
.btn-gradient{background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);border:none;padding:12px 30px;border-radius:25px;color:white;font-weight:600;cursor:pointer;box-shadow:0 5px 15px rgba(245,87,108,0.4)}
.btn-gradient:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(245,87,108,0.6)}
.btn-cancel{background:rgba(255,255,255,0.2);border:2px solid white;padding:12px 30px;border-radius:25px;color:white;font-weight:600;cursor:pointer}
.btn-cancel:hover{background:white;color:#667eea}
.grid-container{display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:25px;margin-top:30px}
.kalolsavam-card{background:white;border-radius:20px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,0.1);transition:all 0.4s}
.kalolsavam-card:hover{transform:translateY(-10px);box-shadow:0 20px 60px rgba(102,126,234,0.3)}
.card-header-gradient{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:25px;color:white}
.card-year{font-size:2.5rem;font-weight:700;margin:0;text-shadow:2px 2px 4px rgba(0,0,0,0.2)}
.card-name{font-size:1.1rem;margin-top:5px}
.card-body-content{padding:25px}
.info-row{display:flex;align-items:center;gap:12px;margin-bottom:15px;color:#555}
.info-icon{width:40px;height:40px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:18px}
.info-label{font-size:0.75rem;color:#999;text-transform:uppercase}
.info-value{font-size:0.95rem;font-weight:600;color:#333}
.card-footer{padding:20px 25px;background:#f8f9fa;text-align:right}
.btn-edit{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border:none;padding:10px 25px;border-radius:20px;color:white;font-weight:600;cursor:pointer;text-decoration:none;display:inline-block}
.btn-edit:hover{transform:scale(1.05);box-shadow:0 5px 20px rgba(102,126,234,0.4);color:white}
.flatpickr-calendar{border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.2);border:none}
.flatpickr-months{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:15px 15px 0 0}
.flatpickr-current-month{color:white}
.flatpickr-day{border-radius:10px}
.flatpickr-day:hover{background:#667eea;color:white}
.flatpickr-day.selected{background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);border:none}
.flatpickr-day.today{border:2px solid #667eea;font-weight:700}
</style>

<div id="divEntryForm">
<?php echo form_open('', array('id' => 'resultEntry', 'name' => 'resultEntry')); ?>
<div class="pagetitle">
<h1>Settings</h1>
<nav>
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome">Home</a></li>
<li class="breadcrumb-item active">Kalolsavam Details</li>
</ol>
</nav>
</div>

<div class="kalolsavam-wrapper">
<?php
if (@$add_edit_kalolsavam != 'no') {
echo form_open_multipart('admin/kalolsavam/add_kalolsavam', array('id' => 'formKalolsavam'));
?>

<div class="form-card" id="formCard" style="<?php echo (count(@$selected_kalolsavam) > 0) ? '' : 'display:none;'; ?>">
<h3>🎭 <?php echo (count(@$selected_kalolsavam) > 0) ? 'Edit Kalolsavam' : 'Add New Kalolsavam'; ?></h3>

<div class="row">
<div class="col-md-8">
<div class="row mb-3">
<div class="col-md-6">
<label class="form-label">Kalolsavam Name</label>
<?php echo form_input("txtKalolsavamName", @$selected_kalolsavam[0]['kalolsavam_name'], 'class="form-control" id="txtKalolsavamName"'); ?>
</div>
<div class="col-md-6">
<label class="form-label">Kalolsavam Year</label>
<?php echo form_input("txtKalolsavamYear", @$selected_kalolsavam[0]['kalolsavam_year'], 'class="form-control" id="txtKalolsavamYear"'); ?>
</div>
</div>

<div class="mb-3">
<label class="form-label">Venue</label>
<?php echo form_input("txtKalolsavamVenue", @$selected_kalolsavam[0]['venue'], 'class="form-control" id="txtKalolsavamVenue"'); ?>
</div>

<div class="row mb-3">
<div class="col-md-6">
<label class="form-label">Start Date</label>
<div class="date-wrapper">
<?php
$start_date = (@$selected_kalolsavam[0]['start_date'] == '0000-00-00') ? '' : @$selected_kalolsavam[0]['start_date'];
echo form_input("txtStartDate", $start_date, 'class="form-control datepicker" id="txtStartDate"');
?>
<span class="cal-icon">📅</span>
</div>
</div>
<div class="col-md-6">
<label class="form-label">End Date</label>
<div class="date-wrapper">
<?php
$end_date = (@$selected_kalolsavam[0]['end_date'] == '0000-00-00') ? '' : @$selected_kalolsavam[0]['end_date'];
echo form_input("txtEndDate", $end_date, 'class="form-control datepicker" id="txtEndDate"');
?>
<span class="cal-icon">📅</span>
</div>
</div>
</div>

<div class="mb-3">
<label class="form-label">Upload Logo</label>
<?php echo form_upload("kalolsavamLogo", '', 'class="form-control" id="kalolsavamLogo" accept="image/*"'); ?>
<small style="opacity:0.8;display:block;margin-top:5px">(.jpg, .gif, .png)</small>
</div>
</div>

<div class="col-md-4">
<div class="logo-preview">
<label class="form-label">Current Logo</label>
<?php display_kalolsavam_logo('state', @$selected_kalolsavam[0]['logo_name']); ?>
</div>
</div>
</div>

<div class="text-center mt-4">
<?php
if (count(@$selected_kalolsavam) > 0) {
echo '<input type="button" value="Update Kalolsavam" class="btn-gradient" id="update_kalolsavam" onclick="fncUpdateKalolsavam(\'state\',\'' . $selected_kalolsavam[0]['kalolsavam_id'] . '\');return false;" />';
echo '&nbsp;&nbsp;';
echo '<input type="button" value="Cancel" class="btn-cancel" onclick="fncCancelKalolsavam();return false;" />';
} else {
echo '<input type="button" value="Save Kalolsavam" class="btn-gradient" id="save_kalolsavam" onclick="fncSaveKalolsavam();" />';
}
?>
</div>
</div>

<?php if (isset($kalolsavam_id) && !empty($kalolsavam_id)) { ?>
<input type="hidden" name="kalolsavam_id" id="kalolsavam_id" value="<?php echo $kalolsavam_id; ?>" />
<?php } ?>

<?php
echo form_close();
}
?>

<?php echo form_open('user/admin_users/add_admin', array('id' => 'list_formKalolsavam')); ?>

<div id="kalolsavamList" style="<?php echo (count(@$selected_kalolsavam) > 0) ? 'display:none;' : ''; ?>">
<h2 style="margin-bottom:20px;color:#333;font-weight:600">📋 Kalolsavam List</h2>

<?php if (count($kalolsavam_details) > 0) { ?>
<div class="grid-container">
<?php foreach ($kalolsavam_details as $kalolsavam) { ?>
<div class="kalolsavam-card">
<div class="card-header-gradient">
<h2 class="card-year"><?php echo $kalolsavam['kalolsavam_year']; ?></h2>
<p class="card-name"><?php echo $kalolsavam['kalolsavam_name']; ?></p>
</div>

<div class="card-body-content">
<div class="info-row">
<div class="info-icon">📍</div>
<div>
<div class="info-label">Venue</div>
<div class="info-value"><?php echo $kalolsavam['venue']; ?></div>
</div>
</div>

<div class="info-row">
<div class="info-icon">📅</div>
<div>
<div class="info-label">Duration</div>
<div class="info-value">
<?php
echo ($kalolsavam['start_date'] == '0000-00-00') ? 'Not Set' : date("j M Y", strtotime($kalolsavam['start_date']));
echo ' - ';
echo ($kalolsavam['end_date'] == '0000-00-00') ? 'Not Set' : date("j M Y", strtotime($kalolsavam['end_date']));
?>
</div>
</div>
</div>
</div>

<div class="card-footer">
<?php if ($kalolsavam['status'] == 'O') { ?>
<a href="javascript:void(0)" class="btn-edit" onclick="fncEditKalolsavam('<?php echo $kalolsavam['kalolsavam_id'] ?>');return false;">
✏️ Edit
</a>
<?php } ?>
</div>
</div>
<?php } ?>
</div>
<?php } else { ?>
<div style="text-align:center;padding:60px 20px;color:#999">
<div style="font-size:4rem;margin-bottom:20px;opacity:0.3">🎭</div>
<h3>No Kalolsavam Records Found</h3>
</div>
<?php } ?>
</div>

<input type="hidden" name="sel_kalolsavam_id" id="sel_kalolsavam_id" />
<?php echo form_close(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded',function(){
flatpickr(".datepicker",{
dateFormat:"Y-m-d",
allowInput:true,
altInput:true,
altFormat:"F j, Y",
monthSelectorType:"dropdown",
yearSelectorType:"dropdown"
});

document.querySelectorAll('.cal-icon').forEach(function(icon){
icon.addEventListener('click',function(){
var input=this.previousElementSibling;
if(input&&input._flatpickr)input._flatpickr.open();
});
});
});
</script>
