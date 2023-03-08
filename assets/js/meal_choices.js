const tabs = $(".form-tab");
const form = document.getElementById("meal_choices");
let pos = form.offsetTop;
//add the amount of steps required
for (let i = 0; i < tabs.length; i++) {
    $("#progress").append("<span class='step'></span>");
}
const steps = $(".step");
var currentTab = 0;
var stepCount = 0;
tabs[currentTab].classList.add("form-tab-active");
if (currentTab == tabs.length - 1) {
    $("#btn-text").html("Submit");
}
//set the progress steps
steps[stepCount].classList.add("active");
if (currentTab == 0) {
    $("#prev").hide();
}

//hide the first tab and show the next one
$("#next").on("click", function () {
    if (currentTab == tabs.length - 1) {
        //determine if the guest has selected an option from each course
        let inputs_chk = tabs[currentTab].querySelectorAll('input[type="radio"]:checked');
        let num_courses = tabs[currentTab].getElementsByClassName("meal-choice-card");
        console.log("Number of courses", num_courses.length);
        if (inputs_chk.length < num_courses.length) {
            let missing = num_courses.length - inputs_chk.length;
            console.log("missing " + missing + " Options");
            $("#response-card-text").html("You have not selected " + missing + " of your options, please try again.");
            $(".response-card").addClass("error-card");
            $("#response-card-wrapper").fadeIn(400);
            $("#response-card-wrapper").delay(3000).fadeOut(400);
            window.scrollTo(top);
            return false
        }
        //submit form data if reached the end of the form
        let formData = new FormData($("#meal_choices").get(0));
        let action = "set_choices";
        let guest_name = $('#meal_choices').data("guest_name");
        let guest_id = $('#meal_choices').data("guest_id");
        formData.append("action", action);
        formData.append("guest_name", guest_name);
        formData.append("guest_id", guest_id);
        $.ajax({ //start ajax post
            type: "POST",
            url: "scripts/meal_choices.script.php",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $("#loading-icon").show(400);
            },
            success: function (data, responseText) {
                if (data === '1') {
                    $("#loading-icon").hide(400);
                    //window.location.replace("meal_choices")
                }


            }
        });
        return false
    } else {
        //determine if the guest has selected an option from each course
        let inputs_chk = tabs[currentTab].querySelectorAll('input[type="radio"]:checked');
        let num_courses = tabs[currentTab].getElementsByClassName("meal-choice-card");
        console.log("Number of courses", num_courses.length);
        if (inputs_chk.length < num_courses.length) {
            let missing = num_courses.length - inputs_chk.length;
            console.log("missing " + missing + " Options");
            $("#response-card-text").html("You have not selected " + missing + " of your options, please try again.");
            $(".response-card").addClass("error-card");
            $("#response-card-wrapper").fadeIn(400);
            $("#response-card-wrapper").delay(3000).fadeOut(400);
            window.scrollTo(top);
            return false
        }
        tabs[currentTab].classList.remove("form-tab-active");
        currentTab++;
        tabs[currentTab].classList.add("form-tab-active");
        steps[currentTab].classList.add("active");
        window.scrollTo(top);
        //if the end of tabs has been reached
        if (currentTab > 0) {
            $("#prev").show();
        }
        if (currentTab == tabs.length - 1) {
            $("#btn-text").html("Save Choices");
        }
    }
})

$("#prev").on("click", function () {
    //hide the current tab and show the next one
    tabs[currentTab].classList.remove("form-tab-active");
    steps[currentTab].classList.remove("active");
    currentTab--;
    tabs[currentTab].classList.add("form-tab-active");
    if (currentTab == 0) {
        $("#prev").hide();
    }
    if (currentTab <= tabs.length - 1) {
        $("#btn-text").html("Next");
    }
})


