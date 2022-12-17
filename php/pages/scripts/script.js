
function cancel() {
	
	$('#passwordblock form').remove();
	$('#passwordblock h3').remove();
	$('#changepass').css('display', 'flex');

}
$('.target').on('click', function(el){
	href = $(this).attr('id');
	window.location.href = href;
});
$(document).ready(function(){
	let consultant_page = "<div id='/php/consultant/assistent.php'  class='target to_consultant'><p style='font-size:0.70em;'>На страницу консультанта</p></div>";
	$('#top_header_part').append(consultant_page);
	$('.to_consultant').on('click', function(){
		href = $(this).attr('id');
		window.location.href = href;
	});
});