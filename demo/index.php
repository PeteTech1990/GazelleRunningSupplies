<html>
<body>
    <h1>
<?php
        print("Welcome to this PHP form");
        ?>
</h1>

<h2>
<?php
        print("Contact Us");
        ?>
</h2>

<form action="grabdata.php" method="post">

<p>
        <label for "inputName">Name:<label>
        <input type="text" id="inputName" name="name">
</p>
<p>
        <label for "inputEmail">Email:<sup>*</sup><label>
        <input type="text" id="inputEmail" name="email">
</p>
<p>
        <label for "inputMessage">Message:<label>
        <input type="text" id="inputMessage" name="message">
</p>
<p>
        <label for "inputSubject">Subject:<label>
        <input type="text" id="inputSubject" name="subject">
</p>
<input type="submit" value="Submit">
<input type="reset" value="Reset">

</form>
</body>
        </html>