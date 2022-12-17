
function cancel() {
	
	$('#passwordblock form').remove();
	$('#passwordblock h3').remove();
	$('#changepass').css('display', 'flex');

}
$('.target').on('click', function(el){
	href = $(this).attr('id');
	window.location.href = href;
});
