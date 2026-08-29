<div class="pagetitle">
      <h1>Certificate</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Certificate Template</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('certificate/certificate/save_certificate_template/', array('id' => 'formCertificate'));
?>
<div class="row justify-content-center">
            <div class="col-md-12 col-sm-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title">Certificate</h5>
                        <div class="row">
                            <div class="col-md-12">
<div class="table-responsive">
<table width="100%"cellpadding="4"cellspacing="0"border="0"class="">
     <tr>
        <td colspan="2">  &nbsp;&nbsp;&nbsp;&nbsp;Fest Name</td>
        <td colspan="4">
        <?php echo form_input('txtTitle', @$certificate_template[0]['title'], 'id="txtTitle" class="form-control" ') ?>
        </td>
    </tr>
    <tr>
        <th align="left" width="2%">&nbsp; </th>
        <th align="left" width="15%">Cordinates </th>
        <th align="center" width="15%"> From Left </th>
        <th align="center" width="15%"> From Top </th>
        <th align="center" width="15%" > Font </th>
        <th align="center" width="15%" > Size </th>
        <th align="left" width="23%">&nbsp;</th>
    </tr>
   
    <tr>
        <td align="left">&nbsp;</td>
        <td align="left">
            Title 
        </td>
        <td align="center">
            <?php echo form_input('txtTitleX', @$certificate_template[0]['title_x'], 'id="txtTitleX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtTitleY', @$certificate_template[0]['title_y'], 'id="txtTitleY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboTitleFont', $font_array, @$certificate_template[0]['title_font'], 'id="cboTitleFont"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboTitleSize', $font_size_array, @$certificate_template[0]['title_size'], 'id="cboTitleSize"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>    

    <tr>
        <td align="left">&nbsp;</td>
        <td align="left">
            Name 
        </td>
        <td align="center">
            <?php echo form_input('txtNameX', @$certificate_template[0]['name_x'], 'id="txtNameX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtNameY', @$certificate_template[0]['name_y'], 'id="txtNameY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboNameFont', $font_array, @$certificate_template[0]['name_font'], 'id="cboNameFont"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboNameSize', $font_size_array, @$certificate_template[0]['name_size'], 'id="cboNameSize"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Item
        </td>
        <td align="center">
            <?php echo form_input('txtItemX', @$certificate_template[0]['item_x'], 'id="txtItemX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtItemY', @$certificate_template[0]['item_y'], 'id="txtItemY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboItemFont', $font_array, @$certificate_template[0]['item_font'], 'id="cboItemFont"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboItemSize', $font_size_array, @$certificate_template[0]['item_size'], 'id="cboItemSize"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Category
        </td>
        <td align="center">
            <?php echo form_input('txtCategoryX', @$certificate_template[0]['category_x'], 'id="txtCategoryX"maxlength="3"class="form-control"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtCategoryY', @$certificate_template[0]['category_y'], 'id="txtCategoryY"maxlength="3"class="form-control"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboCategoryFont', $font_array, @$certificate_template[0]['category_font'], 'id="cboCategoryFont"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboCategorySize', $font_size_array, @$certificate_template[0]['category_size'], 'id="cboCategorySize"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Grade
        </td>
        <td align="center">
            <?php echo form_input('txtGradeX', @$certificate_template[0]['grade_x'], 'id="txtGradeX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtGradeY', @$certificate_template[0]['grade_y'], 'id="txtGradeY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboGradeFont', $font_array, @$certificate_template[0]['grade_font'], 'id="cboGradeFont"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboGradeSize', $font_size_array, @$certificate_template[0]['grade_size'], 'id="cboGradeSize"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Class
        </td>
        <td align="center">
            <?php echo form_input('txtClassX', @$certificate_template[0]['class_x'], 'id="txtClassX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtClassY', @$certificate_template[0]['class_y'], 'id="txtClassY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboClassFont', $font_array, @$certificate_template[0]['class_font'], 'id="cboClassFont"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboClassSize', $font_size_array, @$certificate_template[0]['class_size'], 'id="cboClassSize"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            School
        </td>
        <td align="center">
            <?php echo form_input('txtSchoolX', @$certificate_template[0]['school_x'], 'id="txtSchoolX"maxlength="3"class="form-control"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtSchoolY', @$certificate_template[0]['school_y'], 'id="txtSchoolY"maxlength="3"class="form-control"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboSchoolFont', $font_array, @$certificate_template[0]['school_font'], 'id="cboSchoolFont"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboSchoolSize', $font_size_array, @$certificate_template[0]['school_size'], 'id="cboSchoolSize"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Subdistrict
        </td>
        <td align="center">
            <?php echo form_input('txtSubdistrictX', @$certificate_template[0]['sub_dist_x'], 'id="txtSubdistrictX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtSubdistrictY', @$certificate_template[0]['sub_dist_y'], 'id="txtSubdistrictY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboSubDistFont', $font_array, @$certificate_template[0]['sub_dist_font'], 'id="cboSubDistFont"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboSubDistSize', $font_size_array, @$certificate_template[0]['sub_dist_size'], 'id="cboSubDistSize"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

 <tr>
    <td align="left" >&nbsp;</td>
    <td align="left">
            District
    </td>
    <td align="center">
    <?php echo form_input('txtDistrictX', @$certificate_template[0]['dist_x'], 'id="txtDistrictX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
    </td>
    <td align="center">
    <?php echo form_input('txtDistrictY', @$certificate_template[0]['dist_y'], 'id="txtDistrictY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
    </td>
    <td align="center">
    <?php echo form_dropdown('cboDistFont', $font_array, @$certificate_template[0]['dist_font'], 'id="cboDistFont"maxlength="3"class="form-control"') ?>
    </td>
     <td align="center">
    <?php echo form_dropdown('cboDistSize', $font_size_array, @$certificate_template[0]['dist_size'], 'id="cboDistSize"maxlength="3"class="form-control"') ?>
    </td>
    <td align="left">&nbsp;</td>
</tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Date
        </td>
        <td align="center">
            <?php echo form_input('txtDateX', @$certificate_template[0]['date_x'], 'id="txtDateX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtDateY', @$certificate_template[0]['date_y'], 'id="txtDateY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboDateFont', $font_array, @$certificate_template[0]['date_font'], 'id="cboDateFont"maxlength="3"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboDateSize', $font_size_array, @$certificate_template[0]['date_size'], 'id="cboDateSize"maxlength="3"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Place
        </td>
        <td align="center">
            <?php echo form_input('txtPlaceX', @$certificate_template[0]['place_x'], 'id="txtPlaceX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtPlaceY', @$certificate_template[0]['place_y'], 'id="txtPlaceY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboPlaceFont', $font_array, @$certificate_template[0]['place_font'], 'id="cboPlaceFont"maxlength="3"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboPlaceSize', $font_size_array, @$certificate_template[0]['place_size'], 'id="cboPlaceSize"maxlength="3"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>
    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Eligible For Higher Level</td>
        <td align="center">
            <?php echo form_input('txtehsX', @$certificate_template[0]['ehs_X'], 'id="txtehsX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_input('txtehsY', @$certificate_template[0]['ehs_Y'], 'id="txtehsY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboehsFont', $font_array, @$certificate_template[0]['ehs_font'], 'id="cboPlaceFont"maxlength="3"class="form-control"') ?>
        </td>
        <td align="center">
            <?php echo form_dropdown('cboehsSize', $font_size_array, @$certificate_template[0]['ehs_size'], 'id="cboehsSize"maxlength="3"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>
    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Photo</td>
        <td align="center"> &nbsp;
            <?php echo form_input('photoX', @$certificate_template[0]['photoX'], 'id="txtehsX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center"> &nbsp;
            <?php echo form_input('photoY', @$certificate_template[0]['photoY'], 'id="txtehsY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">Width - 
            <?php echo form_input('photoWidth', @$certificate_template[0]['photoWidth'], 'id="txtehsX"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>
        <td align="center">Hight - 
            <?php echo form_input('photoHight', @$certificate_template[0]['photoHight'], 'id="txtehsY"class="form-control"maxlength="3"onkeypress="javascript:return numbersonly(this, event, false);"') ?>
        </td>

        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Page Style
        </td>
        <td align="left" colspan="4">
            <?php echo form_dropdown('cboPageStyle', array('P' => 'Portrait', 'L' => 'Landscape'), @$certificate_template[0]['page_style'], 'id="cboPageStyle"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Line Height
        </td>
        <td align="left" colspan="4">
            <?php echo form_dropdown('cboLineHeight', $line_height_array, @$certificate_template[0]['line_height'], 'id="cboLineHeight"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Top Margin
        </td>
        <td align="left" colspan="4">
            <?php echo form_dropdown('cboTopMargin', $line_height_array, @$certificate_template[0]['top_margin'], 'id="cboTopMargin"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>  
    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Left Margin
        </td>
        <td align="left" colspan="4">
            <?php echo form_dropdown('cboLeftMargin', $line_height_array, @$certificate_template[0]['left_margin'], 'id="cboLeftMargin"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Right Margin
        </td>
        <td align="left" colspan="4">
            <?php echo form_dropdown('cboRightMargin', $line_height_array, @$certificate_template[0]['right_margin'], 'id="cboRightMargin"class="form-control"') ?>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Type of Certificate
        </td>
        <td align="left" colspan="4">
            <?php echo form_dropdown('cboCtType', $certificate_type_array, @$certificate_template[0]['type_id'], 'id="cboCtType"class="form-control"') ?>
        </td>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left">
            Label Print
        </td>
        <td align="left" colspan="4">
            <?php echo form_dropdown('cboLabelPrint', array('N' => 'No', 'Y' => 'Yes'), @$certificate_template[0]['label_print'], 'id="cboLabelPrint"class="form-control"') ?>
        </td>
        </td>
        <td align="left">&nbsp;</td>
    </tr>

    <tr>
        <td align="left" >&nbsp;</td>
        <td align="left"colspan="6" >
            <?php echo form_submit('Save', 'Save' , 'class="btn btn-primary"') ?>
            &nbsp;&nbsp;
            <a href="<?php echo base_url() . 'test/' ?>"target="_blank">Test X - Y Cordinates</a>
        </td>
    </tr>
    <?php if ($this->General_Model->is_certificate_template_set()) { ?>
        <tr>
            <td align="left" >&nbsp;</td>
            <td align="left"colspan="6" >
                <a href="<?php echo base_url() . 'certificate/certificate/test_certificate/' ?>"target="_blank">Preview With Graph</a>
                &nbsp;&nbsp;
                <a href="<?php echo base_url() . 'certificate/certificate/test_certificare_withoutgraph/' ?>"target="_blank">Preview Without Graph</a>
            </td>            	
        </tr>
    <?php } ?>
</table>
</div></div></div></div></div></div>
</div>
<?php
echo form_close();
?>
