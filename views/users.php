<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h1><?php 
    foreach ($users as $user) {
        echo $user['name'];
        echo "<br>";
        # code...
    } ?></h1>
</body>
</html>