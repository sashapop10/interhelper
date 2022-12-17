//loading helper to page

var InterHelperButton = $('<div id="InterHelper_head"></div>');
var InterHelperButtonSvg = $('<svg id="Capa_9" enable-background="new 0 0 512.005 512.005" height="512" viewBox="0 0 512.005 512.005" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m511.658 51.675c2.496-11.619-8.895-21.416-20.007-17.176l-482 184c-5.801 2.215-9.638 7.775-9.65 13.984-.012 6.21 3.803 11.785 9.596 14.022l135.403 52.295v164.713c0 6.948 4.771 12.986 11.531 14.593 6.715 1.597 13.717-1.598 16.865-7.843l56.001-111.128 136.664 101.423c8.313 6.17 20.262 2.246 23.287-7.669 127.599-418.357 122.083-400.163 122.31-401.214zm-118.981 52.718-234.803 167.219-101.028-39.018zm-217.677 191.852 204.668-145.757c-176.114 185.79-166.916 176.011-167.684 177.045-1.141 1.535 1.985-4.448-36.984 72.882zm191.858 127.546-120.296-89.276 217.511-229.462z"/></g></svg>');
var InterHelperName = $('<h2>InterHelper!</h2>');
var InterHelperName2 = $('<h2>InterHelper!</h2>');
var InterHelperForm = $('<form id="InterHelper_window" action="jivo.php" method="post"></form>');
var InterHelperHead = $('<div id="head_of_InterHelper_window"><div id="exit_InterHelper_window"></div></div>');
var InterHelperBody = $('<div id="body_of_InterHelper_window"><h2 class ="message_by_us">Оставьте свое сообщение в этой форме, мы ответим на вашу почту в ближайшее время.</h2></div>');
var InterHelperFoot = $('<div id="foot_of_InterHelper_window"><textarea autocomplete="off" type ="text" name="message" id="message_input_of_InterHelper_window"  placeholder = "Print here"></textarea><button id="message_send_button"  type="button"></button></div>');
$(document).ready(function(){
	$('body').append(InterHelperButton);
	InterHelperButton.append(InterHelperButtonSvg);
	InterHelperButton.append(InterHelperName);
	$('body').append(InterHelperForm);
	InterHelperForm.append(InterHelperHead);
	InterHelperHead.append(InterHelperName2);
	InterHelperForm.append(InterHelperBody);
	InterHelperForm.append(InterHelperFoot);

//diolog_window_script

var InterHelper_head = $('#InterHelper_head');
var InterHelper_window = $('#InterHelper_window');
var message_send_button = $('#message_send_button');
var exit_InterHelper_window = $('#exit_InterHelper_window');
var body_of_InterHelper_window = $('#body_of_InterHelper_window');
var mail_message;
// open window
InterHelper_head.on('click', function() {
	exit_InterHelper_window.css('transition', '1s');
	exit_InterHelper_window.css('visibility', 'visible');
	InterHelper_window.css('visibility', 'visible');
	InterHelper_window.css('transition', '0.35s');
	InterHelper_window.css('bottom', '0');
	InterHelper_window.css('right', '40px');
	InterHelper_head.css('transition', '0.35s');
	InterHelper_head.css('right', '-40px');
});
// close window
exit_InterHelper_window.on('click', function() {
	exit_InterHelper_window.css('transition', '0s');
	exit_InterHelper_window.css('visibility', 'hidden');
	InterHelper_window.css('transition', '0s');
	InterHelper_window.css('visibility', 'hidden');
	InterHelper_window.css('bottom', '170px');
	InterHelper_window.css('right', '0px');
	InterHelper_head.css('transition', '0.35s');
	InterHelper_head.css('right', '0px');
});
var eror_trys = 0;

// send first message
message_send_button.on('click', function() {
if (first_message == true ) {
	if ($('#message_input_of_InterHelper_window').val() != '') {
		let text_message_by_client = $('#message_input_of_InterHelper_window').val();
		let message_by_client = $('<h2>'+text_message_by_client+'</h2>');
		message_by_client.addClass('message_by_client');

		message_by_client.appendTo(body_of_InterHelper_window);
		$('#message_input_of_InterHelper_window').val('');
		let scrollTop = body_of_InterHelper_window.scrollTop();
		
		eror_trys = 0;
		first_message = false;
		$('#message_input_of_InterHelper_window').prop('disabled', true);
		$('#message_send_button').prop('disabled', true);
		mail_message = $('<div id= "mail_block"><input type ="mail" name="mail" id="write_your_mail" placeholder = "Ваше имя или email"><button id="send_mail" type="submit">отправить</button></div>');
		mail_message.appendTo(body_of_InterHelper_window);
	
		body_of_InterHelper_window.scrollTop(scrollTop += 1000);
		
	}
	else{
	
		if(eror_trys != 1){
		
		let sys_message = 'Not all fields are filled';
		let message_by_system = $('<h2>'+sys_message+'</h2>');
		message_by_system.addClass('message_by_system');
		message_by_system.appendTo(body_of_InterHelper_window);
		eror_trys = 1;
		let scrollTop = body_of_InterHelper_window.scrollTop();
		body_of_InterHelper_window.scrollTop(scrollTop += 1000);
		}
		
}
}
// send other messages
else{
	if ($('#message_input_of_InterHelper_window').val() != '') {
		let text_message_by_client = $('#message_input_of_InterHelper_window').val();
		let message_by_client = $('<h2>'+text_message_by_client+'</h2>');
		message_by_client.addClass('message_by_client');
		$('#message_send_button').prop('type', 'submit');
		message_by_client.appendTo(body_of_InterHelper_window);
		
		let scrollTop = body_of_InterHelper_window.scrollTop();
		body_of_InterHelper_window.scrollTop(scrollTop += 1000);
		eror_trys = 0;
		
	}
	else{
	
		if(eror_trys != 1){
	
		let sys_message = 'заполнены не все поля';
		let message_by_system = $('<h2>'+sys_message+'</h2>');
		message_by_system.addClass('message_by_system');
		message_by_system.appendTo(body_of_InterHelper_window);
		eror_trys = 1;
		let scrollTop = body_of_InterHelper_window.scrollTop();
		body_of_InterHelper_window.scrollTop(scrollTop += 1000);
		}
		else{
		
		}
		
	}
}
//after user send info 
$('#send_mail').on('click', function(){
	if ($('#write_your_mail').val() != '') {
	first_message = false;
	$('#send_mail').prop('disabled', true);
	$('#message_input_of_InterHelper_window').prop('disabled', false);
	$('#message_send_button').prop('disabled', false);
	$('#message_send_button').prop('type', 'submit');
	$('#send_mail').html('Спасибо!')
	let sys_message = 'Теперь ваши сообщения не потеряются. Ответ операторов придет в чат и по указанным контактам.';
		let message_by_system = $('<h2>'+sys_message+'</h2>');
		message_by_system.addClass('message_by_system');
		message_by_system.appendTo(body_of_InterHelper_window);
		$.cookie('mail', $('#write_your_mail').val());
		let scrollTop = body_of_InterHelper_window.scrollTop();
		body_of_InterHelper_window.scrollTop(scrollTop += 1000);
	}
	else{
		if(eror_trys != 1){
	
		let sys_message = 'заполнены не все поля';
		let message_by_system = $('<h2>'+sys_message+'</h2>');
		message_by_system.addClass('message_by_system');
		message_by_system.appendTo(body_of_InterHelper_window);
		eror_trys = 1;
		let scrollTop = body_of_InterHelper_window.scrollTop();
		body_of_InterHelper_window.scrollTop(scrollTop += 1000);
		}
		else{
		
		}
	}
});
});
});
