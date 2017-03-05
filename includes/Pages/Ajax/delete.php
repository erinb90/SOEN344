<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

if(\Stark\DeleteReservationSession::delete($_REQUEST['reid']))
{

    ?>
    <div class="alert alert-success ">

        You have successfully canceled your reservation!
    </div>
    <script>
        $(function(){
            userReservations.ajax.reload(function (json)
            {
                // add the close button
                $('#cancelMessage').dialog({
                    title : "Delete Successful",
                    buttons : {
                        "Close" : function ()
                        {
                            $(this).dialog("destroy");
                        }
                    }
                });
            }, false);

        });
    </script>
    <?php
}
else
{

}