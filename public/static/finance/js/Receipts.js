$(function(){  
	//��ͬ�������ᣬ����				
$('#title').change(function(){
	var title = $('#title').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/finance/Receipts/getDetail/title/"+title,
			success: function(data){
			console.log(data)
        	$('#nail').val(data.nail);
        	$('#money').val(data.money);
       
			}
		});    	
})
});
        		
