<?php require_once '../app/Components/UserTableComponent.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h1><?php echo $message; ?></h1>
    <?php echo \App\Components\UserTableComponent::generateTable(); ?>
</body>
</html>