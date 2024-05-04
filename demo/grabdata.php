<html lang="en">
        <head>
                <meta charset="UTF-8">
                <title>Contact Form Response</title>
</head>
<body>
    <h1>
<?php
        print("Thanks very much");
        ?>
</h1>

<h2>
<?php
        print("Here is the information your have submitted:");
        ?>
</h2>

<ol>
        <li><em>Name:</em> <?php echo $_POST["name"]?></li>
        <li><em>Email: </em> <?php echo $_POST["email"]?></li>
        <li><em>Subject: </em> <?php echo $_POST["subject"]?></li>
        <li><em>Message: </em> <?php echo $_POST["message"]?></li>
</ol>
</body>
        </html>