<!DOCTYPE html>

<style>
.captioncol{
width:50%;
}
table{
border:1px solid;
}
H2 {
	font-family : sans-serif;
	font-size : small;
}
p {
	font-family : sans-serif;
	font-size : x-small;
}
td 
{

	font-family : sans-serif;
	font-size : x-small;
}
</style>

<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
jQuery(function($) {
  $('div.btn-group[data-toggle-name=*]').each(function(){
    var group   = $(this);
    var form    = group.parents('form').eq(0);
    var name    = group.attr('data-toggle-name');
    var hidden  = $('input[name="' + name + '"]', form);
    $('button', group).each(function(){
      var button = $(this);
      button.live('click', function(){
          hidden.val($(this).val());
      });
      if(button.val() == hidden.val()) {
        button.addClass('active');
      }
    });
  });
});
$('.btn').button('toggle');
</script>
</head>
<body <?print $bodyparams;?>>
		<div  class="row-fluid">

		<div class="content span12">
		<? if ($GLOBALS['DB_DEBUG']===true){print "<p>Contents of POSTDATA <pre>";print_r($_POST);print "\n Contents of GETDATA \n"; print_r($_GET);print "</pre></p>";}?>