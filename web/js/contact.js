$(function() {
    console.log('creemson.js to the rescue')
    $('#contactForm').submit(function(e){
    	e.preventDefault();
		$('.alert').fadeOut(100);

    	data = $(this).serialize();
	    $.ajax({
	        url: Routing.generate('sendEmail'),
	        type:'POST',
	        data:data,
	        success: function(response){
	            if(response.error){
	            	if(response.error.email){
	            		$('#contactForm #email').before(' <div class="alert alert-warning">' + response.error.email + '</div>');
	            	}
	            	if(response.error.null){
	            		$('#contactForm').before(' <div class="alert alert-warning">' + response.error.null + '</div>');
	            	}
	            }else{
	            	$('#contactForm').after(' <div class="alert alert-success">' + response.success + '</div>');	            	
		            $('.alert').delay(1000).fadeOut(500);
	            }

	        },
	        error: function(error){
	            //console.log(error);
	            //animation.hide();
	            //pageTitle.after('error');

	        }
	    })
    })
})