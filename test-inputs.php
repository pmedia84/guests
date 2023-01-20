<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>

    <form action="test.php" method="post" id="test">
        <div class="" id="testrow">

        </div>


        <button type="submit">Submit Information</button>
        <button id="add" type="button">add field</button>
        <button class='btn-primary remove' type=button>Remove</button>
        <div id="response" class="d-none">
        </div>
    </form>
    </form>
<?php 
    $max_inputs = "5";
?>
    <script>
        $("#test").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#test").get(0));
            $.ajax({ //start ajax post
                type: "POST",
                url: "test.php",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() { //animate button
                    $("#loading-icon").show(400);
                },
                complete: function() {
                    $("#loading-icon").hide(400);
                },
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                }
            });
        })
    </script>

    <script>
        var arrcount=0;
        var max = <?php echo $max_inputs;?>;
        var error = $("error");
        $("#add").on("click", function() {
            if(arrcount < max){
                var inputs = $("<div class='form-input-wrapper d-none inputs'><input class=' ' type='text' name='cars["+arrcount+"][name]' placeholder='Enter car name'><input class=''  type='number' name='cars["+arrcount+"][year]' placeholder='Enter car year'>");
            $("#testrow").append(inputs);
            $(".inputs").slideDown(400);
            arrcount++;
                
            }
        });

    </script>


</body>

</html>