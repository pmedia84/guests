<?php
//load active guest list for event from get request
if ((array_key_exists('action', $_GET))) {
    if ($_GET['action'] == "load") {
        include("../connect.php");
        //define event id
        $event_id = $_GET['event_id'];
        //load all invites details
        $guest_allocated_query = ('SELECT invite_id FROM invitations  WHERE event_id=' . $event_id);
        $invites = $db->query($guest_allocated_query);
        $guests_allocated = $invites->num_rows;
        //find additional invites
        $extra_invites_query = ('SELECT guest_list.guest_id, SUM(guest_list.guest_extra_invites) AS extra_inv, invitations.guest_id FROM guest_list NATURAL LEFT JOIN invitations WHERE guest_list.guest_id=invitations.guest_id AND invitations.event_id='.$event_id);
        $extra_invites = $db->query($extra_invites_query);
        $extra_inv = $extra_invites->fetch_array();
        $total_inv = $extra_inv['extra_inv'];
        //
        $invites_sent = ('SELECT invite_id FROM invitations  WHERE event_id=' . $event_id . ' AND invite_status="Sent"');
        $invites = $db->query($invites_sent);
        $invites_sent = $invites->num_rows;
        //find event details

        $event = $db->prepare('SELECT * FROM wedding_events WHERE event_id =' . $event_id);

        $event->execute();
        $event->store_result();
        $event->bind_result($event_id, $event_name, $event_location, $event_address, $event_date, $event_time, $event_notes, $event_capacity);
        $event->fetch();
        $event_time = strtotime($event_time);
        $time = date('H:ia', $event_time);
        $event_date = strtotime($event_date);
        $date = date('D d M Y', $event_date);
    }
}

?>
<?php if (array_key_exists('action', $_GET)) : ?>
    <?php if ($_GET['action'] == "load") : ?>
        <h3>Invite Details</h3>
        <p>Note that the figures below also include guests that can bring others with them.</p>
        <div class="event-card-invites">
            <div class="event-card-invites-textbox">
                <p>Invites Available </p><span><?= $event_capacity - $total_inv - $guests_allocated; ?></span>
            </div>
            <div class="event-card-invites-textbox">
                <?php
                ?>
                <p>Invites Sent </p><span><?= $invites_sent; ?></span>
            </div>

            <div class="event-card-invites-textbox">
                <p>Guests Allocated </p><span><?= $total_inv + $guests_allocated; ?></span>
            </div>
        </div>

        <h3>Guest List</h3>
        <p>To remove a guest from this event, click the minus button beside their name.</p>
        <table class="event-card-guestlist-table ">
            <?php
            $guest_list_query = ('SELECT guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_extra_invites, invitations.event_id, invitations.guest_id, invitations.invite_status, invitations.invite_rsvp_status FROM guest_list NATURAL LEFT JOIN invitations WHERE guest_list.guest_id = invitations.guest_id AND event_id=' . $event_id);
            $guest_list = $db->query($guest_list_query);
            ?>

            <tr>
                <th>Name</th>
                <th>Remove</th>
            </tr>
            <?php foreach ($guest_list as $guest) :
                if ($guest['guest_extra_invites'] >= 1) {
                    $plus = "+" . $guest['guest_extra_invites'];
                } else {
                    $plus = "";
                }
            ?>
                <tr>
                    <td><a href="guest.php?action=view&guest_id=<?= $guest['guest_id']; ?>"><?= $guest['guest_fname'] . " " . $guest['guest_sname'] . ' ' . $plus; ?></a></td>
                    <td>
                        <label class="checkbox-form-control">
                            <button class="btn-primary btn-secondary remove_guest" data-guestid="<?=$guest['guest_id'];?>"><i class="fa-solid fa-user-minus" ></i></button>
                        </label>
                    </td>

                </tr>
            <?php endforeach; ?>


        </table>
        <h3>Guests Available To Assign</h3>
        <label class="checkbox-form-control" for="check_all">
            <input type="checkbox" id="check_all" />
            Assign All Available Guests
        </label>
        <?php
        $available_inv_query = ('SELECT guest_id, guest_fname, guest_sname, guest_extra_invites FROM guest_list WHERE NOT EXISTS(SELECT guest_id, event_id FROM invitations WHERE guest_list.guest_id=invitations.guest_id AND event_id='.$event_id.')');
        $available_inv = $db->query($available_inv_query);
        $available_inv_result = $available_inv->fetch_assoc();
        ?>
        <form action="scripts/event.script.php" method="POST" enctype="multipart/form-data" id="assign_guests">
            <table class="event-card-guestlist-table">
                <tr>
                    <th>Name</th>
                    <th>Assign</th>
                </tr>
                <?php foreach ($available_inv as $inv) :
                    if ($inv['guest_extra_invites'] >= 1) {
                        $plus = "+" . $inv['guest_extra_invites'];
                    } else {
                        $plus = "";
                    }
                ?>
                    <tr>
                        <td><a href="guest.php?action=view&guest_id=<?= $inv['guest_id']; ?>"><?= $inv['guest_fname'] . " " . $inv['guest_sname'] . ' ' . $plus; ?></a></td>
                        <td>
                            <label class="checkbox-form-control" for="gallery">
                                <input class="assign_check" type="checkbox" id="gallery" name="guest_id[]" value="<?= $inv['guest_id']; ?>" />
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div class="button-section my-3">
                <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Assign Selected Guests </button>
            </div>
        </form>
        <script>
            //add a new guest to the event
            $("#assign_guests").submit(function(event) {

                event.preventDefault();

                var formData = new FormData($("#assign_guests").get(0));
                var event_id = <?php echo $event_id; ?>;
                formData.append("action", "assign");
                formData.append("event_id", event_id);
                $.ajax({ //start ajax post
                    type: "POST",
                    url: "scripts/event.script.php",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data, responseText) {
                        $("#active_guest_list").html(data);
                        $("#active_guest_list").fadeIn(500);
                        var event_id = <?php echo $event_id; ?>;
                        url = "scripts/event.script.php?action=load&event_id=" + event_id;
                        $.ajax({ //load active guest list
                            type: "GET",
                            url: url,
                            encode: true,
                            success: function(data, responseText) {
                                $("#active_guest_list").html(data);
                                $("#active_guest_list").fadeIn(500);
                            }
                        });
                    }
                });

            });
        </script>
        <script>
            //remove guests from list
            $(".remove_guest").on("click", function() {
                
                var formData = new FormData();
                var guest_id = $(this).data("guestid");
                var event_id = <?php echo $event_id; ?>;
                formData.append("action", "remove_guest");
                formData.append("event_id", event_id);
                formData.append("guest_id", guest_id);
                $.ajax({ //start ajax post
                    type: "POST",
                    url: "scripts/event.script.php",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data, responseText) {
                        $("#active_guest_list").html(data);
                        $("#active_guest_list").fadeIn(500);
                        var event_id = <?php echo $event_id; ?>;
                        url = "scripts/event.script.php?action=load&event_id=" + event_id;
                        $.ajax({ //load active guest list
                            type: "GET",
                            url: url,
                            encode: true,
                            success: function(data, responseText) {
                                $("#active_guest_list").html(data);
                                $("#active_guest_list").fadeIn(500);
                            }
                        });
                    }
                });
            })
        </script>
        <script>
            //check all check boxes
            $("#check_all").on("click", function() {
                $(".assign_check").not(this).prop('checked', this.checked)
            })
        </script>
    <?php endif; ?>
<?php endif; ?>

<?php 

if (array_key_exists('action', $_POST)) {
    if ($_POST['action'] == "assign") {
        include("..//connect.php");
        //declare event_id
        $event_id = $_POST['event_id'];
        //insert into guest group tables
        $invite = $db->prepare('INSERT INTO invitations (guest_id, event_id) VALUES (?,?)');
        foreach ($_POST['guest_id'] as $guest_id) {
            $invite->bind_param('ii', $guest_id, $event_id);
            $invite->execute();
        }
        $invite->close();
    }

    if ($_POST['action'] == "remove_guest") {
        include("..//connect.php");
        //declare event_id and guest_id
        $event_id = $_POST['event_id'];
        $guest_id = $_POST['guest_id'];
        //insert into guest group tables
        $invite = $db->prepare('DELETE FROM  invitations  WHERE guest_id=? AND event_id=?');
        $invite->bind_param('ii', $guest_id, $event_id);
        $invite->execute();
        $invite->close();
    }
    if ($_POST['action'] == "edit_event") {
        include("..//connect.php");
        //declare variables
        $event_id = $_POST['event_id'];
        $event_name = mysqli_real_escape_string($db, $_POST['event_name']);
        $event_location = mysqli_real_escape_string($db, $_POST['event_location']);
        $event_address = mysqli_real_escape_string($db, $_POST['event_address']);
        $event_date = mysqli_real_escape_string($db, $_POST['event_date']);
        $event_time = mysqli_real_escape_string($db, $_POST['event_time']);
        $event_notes= mysqli_real_escape_string($db, $_POST['event_notes']);
        $event_capacity= mysqli_real_escape_string($db, $_POST['event_capacity']);
        //insert into guest group tables
        $event = $db->prepare('UPDATE wedding_events SET event_name=?, event_location=?, event_address=?, event_date=?, event_time=?, event_notes=?, event_capacity=?  WHERE event_id =?');
        $event->bind_param('sssssssi',$event_name, $event_location, $event_address, $event_date, $event_time, $event_notes, $event_capacity, $event_id);
        $event->execute();
        $event->close();
    }
    if ($_POST['action'] == "add_event") {
        include("..//connect.php");
        //declare variables
        $event_name = mysqli_real_escape_string($db, $_POST['event_name']);
        $event_location = mysqli_real_escape_string($db, $_POST['event_location']);
        $event_address = mysqli_real_escape_string($db, $_POST['event_address']);
        $event_date = mysqli_real_escape_string($db, $_POST['event_date']);
        $event_time = mysqli_real_escape_string($db, $_POST['event_time']);
        $event_notes= mysqli_real_escape_string($db, $_POST['event_notes']);
        $event_capacity= mysqli_real_escape_string($db, $_POST['event_capacity']);
        //insert into events table
        $new_event = $db->prepare('INSERT INTO wedding_events (event_name, event_location, event_address, event_date, event_time, event_notes, event_capacity ) VALUES (?,?,?,?,?,?,?)');
        $new_event->bind_param('ssssssi',$event_name, $event_location, $event_address, $event_date, $event_time, $event_notes, $event_capacity);
        $new_event->execute();
        $new_event->close();
    }
}
?>