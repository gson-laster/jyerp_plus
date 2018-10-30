
function checkboxLabel(o){
	$(o).children('i').toggleClass('none');
}
$(".radioLabel").click(function(){
	var t = $(this);
	t.parents('.inp').find('i').addClass('none');
	t.children('i').removeClass('none');
	
})

$(".status").click(function(){
	var d = $(this).children('div');
	var inp = $(this).children('input');
	if(d.hasClass('active')){
		d.removeClass('active').addClass('cancelActive');
		inp.val(0)
	} else {
		d.removeClass('cancelActive').addClass('active');
		inp.val(1)
		
	}
})

function  addLabel(o) {
	var o = $(o);
	var v = o.val();
	if (v != '') {
		o.before('<span>' + o.val() + '<i onclick="remove(this)">X</i></span>');
		o.val('')
	}
}

function addLabelUp(event, o) {
	var code = event.keyCode;
	if(code == 13) {
		addLabel(o);
	}
	if (code == 8) {
		var t = $(o)
		if ( t.val() == '') {
			t.prev().remove();
		}
	}
}

function inpFocus(o){
	$(o).find('input').focus();
}

function remove(o){
	$(o).parent('span').remove();
}


