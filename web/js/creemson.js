$(function() {
    console.log('creemson.js to the rescue');

    /**
     * Preview file upload
     */
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#preview_img').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(".fileUploader").change(function(){
        readURL(this);
    })

});