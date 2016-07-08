<?php

session_start();
if(isset($_POST['submit']) && isset($_POST['message']) ) {
	$input = mysql_escape_string($_POST['message']);
	if(isset($_SESSION['question'])) {
		$question = mysql_escape_string($_SESSION['question']);
		$AI = new Rose();
		$AI->start($input, $question);
	} else {
		$AI = new Rose();
		$AI->start($input);
	}
} else {
	out("opinions expressed here are not those of the owner");
}

class Rose {
	private $input;
	private $question;

	public function start($input, $question = "0") {
		if($question == "0") {
			$this->respond($input);
		} else {
			$this->anwser($input, $question);
		}
	}
	public function respond($input) {
		require "dbconn.php";
		$query = "select * from input where question='$input'";
		$result = $mysqli->query($query);
		$result->data_seek(0);
		while ($row = $result->fetch_assoc()) {
			$anwser = $row['anwser'];
			$done = $row['done'];
		}
		if($done == '1') {
			echo $anwser;
		} elseif ($done == '0') {
			$this->question();
		} else {
			$query = "INSERT INTO input(question) VALUES ('$input')";
			$result = $mysqli->query($query);
			$this->question();
		}
	}
	public function question() {
		require "dbconn.php";
		$query = "select * from input where done='0'";
		$result = $mysqli->query($query);
		$result->data_seek(0);
		while ($row = $result->fetch_assoc()) {
			$question = $row['question'];
		}
		$_SESSION['question'] = $question;
		echo "I don't know, Can you tell me the anwser to this question: " . $question;
	}
	public function anwser($input, $question) {
		require "dbconn.php";
		$query = "UPDATE input SET anwser='$input',done='1' WHERE question='$question'";
		$result = $mysqli->query($query);
		unset($_SESSION['question']);
		echo "Thanks for the information.";
	}

}
function out($a) {
	exec("sudo espeak $a -w hi.wav");
	echo $a;
}
?>


<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>heh</title>

    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../jumbotron.css" rel="stylesheet">

  </head>
<body>

<div class="col-md-4">
<form action="index.php" method="post">

<div class="input-group">
  <span class="input-group-addon" id="basic-addon1">Input:</span>
  <input type="text" class="form-control" placeholder="Hello" aria-describedby="basic-addon1" name="message">
</div>

<button type="submit" class="btn btn-default btn-lg" value="submit" name="submit"> 
<span class="glyphicon glyphicon-ok" aria-hidden="true">Interact</span>
</button>

</form>
</div>

<audio controls>
  <source src="hi.wav" type="audio/wav">
</audio> 

</div>
</html>
