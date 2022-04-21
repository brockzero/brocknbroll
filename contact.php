<?php 
$pageTitle = "Obligatory Contact Form";
require "admin/constants.php";
require "header.php";
echo '<div class="view-frame">';
$form_block = '<h2>Obligatory Contact Form</h2>
<p>I get close to six billion fan mails a day so I may not get back to you immediately. Average wait time for a response is 17 years 3 months and 12 days.</p>
<form method="post" action="contact">
<div class="row collapse">
    <div class="large-6 columns">
      <div class="row prefix-radius">
        <div class="small-3 columns">
          <span class="prefix">Name</span>
        </div>
        <div class="small-9 columns">
           <input type="text" name="Name" />
        </div>
      </div>
			<div class="row prefix-radius">
        <div class="small-3 columns">
          <span class="prefix">E-mail</span>
        </div>
        <div class="small-9 columns">
           <input type="text" name="E-mail" />
        </div>
      </div>
			<div class="row prefix-radius">
        <div class="small-3 columns">
          <span class="prefix">Subject</span>
        </div>
        <div class="small-9 columns">
           <input type="text" name="Subject" />
        </div>
      </div>
			<div class="row prefix-radius">
        <div class="small-3 columns">
          <span class="prefix">Comments</span>
        </div>
        <div class="small-9 columns">
            <textarea name="Message" rows="5"></textarea></p>
						<p><input class="button" type="submit" value="Submit E-mail" /></p>
        </div>
      </div>
    </div>	
  </div>
</form>';
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	// they need to see the form
	echo $form_block;
} else {
	$to  = "Robert Brock <robshocka@gmail.com>";
	$from = $_POST['E-mail'];
	$name = $_POST['Name'];
	$subject = $_POST['Subject'];
	
	$to  = trim($to);
	$from = trim($from);
	$name = trim($name);
	$subject = trim($subject);
	$from = urldecode($from);
		if (preg_match("/\r/",$from) || preg_match("/\n/",$from)){
		 die("Why ?? :(");
		}
	$message = "<dl>";
		foreach ($_POST as $key => $value) {  //allows for flexible any size forms
		$message .= "<dt><strong>$key</strong></dt>"; 
		$message .= "<dd>$value</dd>"; 
		}
	$message .= "</dl>";
	$message = trim($message);
	/* To send HTML mail, you can set the Content-type header. This script is set for HTML mail. */
	$headers = "MIME-Version: 1.0\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\n";
	$headers .= "From: $name <$from>";

/* and now mail it */
	if (mail($to, stripslashes($subject), stripslashes($message), $headers)) { 
	echo stripslashes("
	<h2>E-mail Sent.</h2>
	<p>Thanks for sending me an e-mail <strong>$name</strong>, I guess.</p>
	<h4>It's too late to proofread it now, but here's your message. Hopefully it's not wasting valuable kilobytes in my mailbox.</h4>
	<p>$message</p>");
	} else { 
	// This echo's the error message if the email did not send.
	echo stripslashes("
	<h2>E-mail not sent</h2>
	<p>E-mail could not be sent. Chances are it's my fault, but I blame you anyway.</p>
	<p>Go <a href='/contact'>back</a> to the contact page. Sorry for the inconvenience (not really that sorry).</p>");

	} 
}
echo '</div>';
require "footer.php";
?>