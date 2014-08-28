jQuery(document).ready(function() {
    console.log('js test');

	var rowNum = 1;
	var row = '<tr><td><label for="tablecell">Server Name</label><input name="server_name[]" type="text" value="" class="all-options" /></td><td><label for="tablecell">Server IP</label><input name="server_ip[]" type="text" value="" class="all-options" /></td></tr><tr><td><label>Short description</label><input name="short_description[]" type="text" value="Short server description..." class="large-text" /></td><td><input type="button" value="Remove" class="creepymc_remove_server button-secondary"></td></tr>';
	//jQuery('#creepymc_servers').append(row);

    jQuery(".creepymc_add_server").live("click",function() {
        jQuery('#creepymc_servers').append(row);
    });


    jQuery(".creepymc_remove_server").live("click",function() {
        var tr = jQuery(this).closest('tr');
        tr.prev().remove();
        tr.remove();
       // console.log('remove');
    });

});
