//load gallery on document load
$(document).ready(function () {
  $.ajax({ //start ajax post
    type: "GET",
    url: "scripts/gallery_script.php?action=load_doc",
    success: function (data, responseText) {
      ///need script to catch errors
      if (responseText === "success") {
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

//check all check boxes
$("#guest_images").on("click", "#check_all", function () {
  $("#guest_images .guest-img-select").each(function () {
    if ($(this).prop("checked")) {
      $(this).parent().removeClass("guest-img-checked");
      $(this).prop('checked', false);
    } else {
      $(this).parent().addClass("guest-img-checked");
      $(this).prop('checked', true);
    }
  })

})

//add and remove selected class with checkboxes
$("#guest_images").on("change", ".guest-img-select", function () {
  $(this).parent().toggleClass("guest-img-checked");
})

//toggle the upload modal
$("#guest_images").on("click", "#upload-img", function () {
  $("#upload-modal").addClass("modal-active");
})
$("#close-upload").on("click touchstart", function () {
  $("#upload-modal").removeClass("modal-active");
})

//hide the upload modal when tapping off it

var modal = document.getElementById('upload-modal');
window.onclick = function (event) {
  if (event.target == modal) {
    modal.classList.remove("modal-active");
  }
}
///show the toolbar for small screens
const guest_gallery_top = document.getElementById("gallery-top");
let guest_pos = guest_gallery_top.offsetTop;
window.onscroll = function () {
  if (window.scrollY >= guest_pos) {

    $(".actions-bar").addClass("visible");

  } else {
    $(".actions-bar").removeClass("visible");

  }
}

//? Uploading images. Use AJAX request

$("#upload").on("submit", function (e) {
  e.preventDefault();
  //show an error message of no images have been selected and stop the script
  if (!$("#gallery_img").val()) {
    let errmsg = "Error, no images have been selected for upload.";
    $("#response-card-text").text(errmsg);
    $("#response-card-title").text("Error");
    
    $(".response-card").addClass("error-card");
    $(".response-card-wrapper").fadeIn(400);
    $(".response-card-wrapper").delay(5000).fadeOut(400);
    return false
  }
  var formData = new FormData($("#upload").get(0));
  let action = $(this).data("action");
  formData.append("action", action);
  $.ajax({ //start ajax post
    type: "POST",
    url: "scripts/gallerycrud.php",
    data: formData,
    contentType: false,
    processData: false,
    beforeSend: function () {
      $("#loading-icon").show(400);
    },
    success: function (data, responseText) {
      //on success, show a message with the amount of images uploaded etc
      const response = JSON.parse(data);
      if (response.response_code == 400) {
        $(".response-card-wrapper").fadeIn(400);
        $(".response-card").addClass("error-card");
        $(".response-card-wrapper").delay(5000).fadeOut(400);
        $("#loading-icon").hide(400);
      }
      if (response.response_code == 200) {
        $(".response-card").removeClass("error-card");
        $(".response-card-wrapper").fadeIn(400);
        $("#upload-modal").removeClass("modal-active");
        $(".response-card-wrapper").delay(5000).fadeOut(400);
        $("#loading-icon").hide(400);
        $("#guest_images").load("scripts/gallery_script.php?action=load_guest_images");
        document.getElementById("upload").reset();
      }
      $("#response-card-title").text(response.img_success_amt + " of " + response.img_total + " images were uploaded");
      $("#response-card-text").text(response.message);
    }
  });
})

//?Deleting Images
//! Confirm delete pop up need to show to confirm that all images will be deleted.
$("#guest_images").on("click", "#delete-img", function () {
  var formData = new FormData($("#guest-gallery").get(0));
  let action = $(this).data("action");
  let key = 0;
  $("#guest_images .guest-img-select").each(function () {
    let filename = $(this).data("image_filename");
    if ($(this).prop("checked")) {
      formData.append("gallery_img[" + key + "][image_filename]", filename);
    }
    key++;
  })
  formData.append("action", action);
  $.ajax({ //start ajax post
    type: "POST",
    url: "scripts/gallerycrud.php",
    data: formData,
    contentType: false,
    processData: false,
    success: function (data, responseText) {
      //on success, show a message with the amount of images deleted etc
      const response = JSON.parse(data);
      if (response.response_code == 400) {
        $(".response-card-wrapper").fadeIn(400);
        $(".response-card").addClass("error-card");
        $(".response-card-wrapper").delay(5000).fadeOut(400);
        $("#loading-icon").hide(400);
      }
      if (response.response_code == 200) {
        $(".response-card").removeClass("error-card");
        $(".response-card-wrapper").fadeIn(400);
        $(".response-card-wrapper").delay(5000).fadeOut(400);
        $("#guest_images").load("scripts/gallery_script.php?action=load_guest_images");
      }
      $("#response-card-title").text(response.img_success_amt + " of " + response.img_total + " images were deleted");
      $("#response-card-text").text(response.message);
    }
  });
})