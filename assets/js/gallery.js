//load gallery on document load
$(document).ready(function(){
  $.ajax({ //start ajax post
    type: "GET",
    url: "scripts/gallery_script.php?action=load_doc",
    success: function (data, responseText) {
        ///need script to catch errors
        if(responseText === "success"){
            $("#gallery").html(data);

            $(".gallery-wrapper .loader").fadeOut(400);
            $("#gallery").fadeIn(500);
        }
    }
});
})

///gallery script
// Open the Modal
function openModal() {
  $(".gallery-modal").toggleClass("modal-active");
}

// Close the Modal
function closeModal() {
  $(".gallery-modal").toggleClass('modal-active');
}