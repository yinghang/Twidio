<?php
    // Loads the required files
    require_once('twilio-php-master/Services/Twilio.php'); // Twilio's SDK
    require_once('rdio.php'); // Rdio's SDK
    require_once('rdio-consumer-credentials.php');
    include('MyTXT.php'); // Simple text database
 
    // Account Sid and Auth Token from twilio.com/user/account
    $sid = "AC865b74eadb12a0470328c6fb0291ee3e"; 
    $token = "44e7cb9bb099896e6cd949a682bbffa5"; 
    $client = new Services_Twilio($sid, $token);

    // Requests sender's cell number and song name
    $senderCell = $_REQUEST['From'];
    $songname = $_REQUEST['Body'];


    // OAuth is hardcoded 
    $rdio = new Rdio(array(RDIO_CONSUMER_KEY, RDIO_CONSUMER_SECRET), array("xfhupbha2tfuq3ahhnpfzvkhfen7bne82thwkyj5hc2k7gcw6xxtaakeya7g8xq6",      "tWugNjJBdvaq"));
    $result = $rdio->call("search", array("query" => $songname, "types" => "Track", "count" => 1));
    $tempVar = $result->result->results[0]->key;

    // Check if song has been requested or exists in Rdio's database
    $mytxt = new MyTXT("testdb.txt");
    $songExists = false;
    foreach ($mytxt->rows as $row) {
    if ($row['key'] == (string)$tempVar) {
        $songExists = true;
        $row['count'] = intval($row['count']) + 1;
        $mytxt->save("testdb.txt");
        $mytxt->close();
       }
    }
    
    // Adds the requested song to the playlist
    if ($result->result->number_results != 0 && $songExists != true && $senderCell != +XXXXXXXXXXX) {
    $newPlaylist = $rdio->call("addToPlaylist", array("playlist" => "p8800832", "tracks" => $tempVar));
    $mytxt = new MyTXT("testdb.txt");
    $mytxt->add_row(array((string)$songname, (string)$tempVar, 1));
    $mytxt->save("testdb.txt");
    $mytxt->close();
    } else {
    $tempVar = 0;
    }


    // Responding to the user
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>

<Response>
    <Message>
    <?php
    if ($result->result->number_results != 0 && $songExists != true && $senderCell != +XXXXXXXXXXX) {
    echo "Thanks, $songname has been added to the playlist.";
    } elseif ($songExists == true) {
    echo "Sorry, $songname has already been requested.";
    } elseif ($senderCell == +XXXXXXXXXXX){
    echo "You are banned from using this and you suck!"; // Prevent people from abusing the service
    } else{
    echo "Sorry, $songname is not in Rdio's database.";
    }
    ?>
    </Message>
</Response>