<?php
    // Loads the required files
    require_once('twilio-php-master/Services/Twilio.php');
    require_once('rdio.php');
    require_once('rdio-consumer-credentials.php');
 
    // Your Account Sid and Auth Token from twilio.com/user/account
    $sid = "AC865b74eadb12a0470328c6fb0291ee3e"; 
    $token = "44e7cb9bb099896e6cd949a682bbffa5"; 
    $client = new Services_Twilio($sid, $token);
    $songname = $_REQUEST['Body'];
    $rdio = new Rdio(array(RDIO_CONSUMER_KEY, RDIO_CONSUMER_SECRET), array("xfhupbha2tfuq3ahhnpfzvkhfen7bne82thwkyj5hc2k7gcw6xxtaakeya7g8xq6",      "tWugNjJBdvaq"));
    $result = $rdio->call("search", array("query" => $songname, "types" => "Track", "count" => 1));
    if ($result->result->number_results != 0) {
    $tempVar = $result->result->results[0]->key;
    $newPlaylist = $rdio->call("addToPlaylist", array("playlist" => "p8800832", "tracks" => $tempVar));
    } else {
    $tempVar = "The song does not exist in the database";
    }
    
    
    // Responding to the user
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>

<Response>
    <Message>
    <?php
    if ($result->result->number_results != 0) {
    echo "Thanks, $songname has been added to the playlist.";
    } else {
    echo "Sorry, $songname does not exist in Rdio's database.";
    }
    ?>
    </Message>
</Response>