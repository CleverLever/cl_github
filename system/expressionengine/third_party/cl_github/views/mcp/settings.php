<style>
#mainContent .pageContents h2 { margin-bottom: 10px; }
#mainContent .pageContents table.mainTable { margin-bottom: 20px; }
.editAccordion td h4 { font-size: 13px; }
</style>
<script type="text/javascript">
$(function () {

});
</script>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_github'.AMP.'method=settings')?>
<h2>Global Settings</h2>
<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
	<tr><th colspan="2">Global Settings</th></th></tr>
	<tr>
		<td><label>Personal Access Token</label><div class="subtext">Found at <a href="https://github.com/settings/applications">https://github.com/settings/applications</a>.</div></td>
		<td>
			<input type="text" name="settings[api_access_token]" value="<?php echo @$settings->get('api_access_token'); ?>">
		</td>
	</tr>
</table>


<div class="tableFooter">
	<div class="tableSubmit">
		<?php echo form_submit(array('name' => 'submit', 'value' => lang('save'), 'class' => 'submit'));?>
	</div>
</div>	
<?php echo form_close()?>