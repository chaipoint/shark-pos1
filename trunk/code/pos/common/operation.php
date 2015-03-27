<style>
#wrapper {hight:none !important;}
</style>
<div class="padded menu_div">
	<button class="btn <?php echo ((MODE=='ppa') ? 'active3' : '');?> operation" id="ppa_operation" >PPA</button>
	<button class="btn <?php echo ((MODE=='ppc') ? 'active3' : '');?> operation" id="ppc_operation" >PPC</button>
	<button class="btn <?php echo ((MODE=='report') ? 'active3' : '');?> operation" id="reprint_operation" >Re-Print Bill</button>
	<button class="btn operation" id="download_couch"  >Download Sales Data</button>
</div>