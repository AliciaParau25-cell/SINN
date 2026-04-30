<!DOCTYPE html>
<html>
<head>
    <title>PHP + HTML</title>
</head>
<body>
    <h1>Ejemplo combinado</h1>
    <p>La hora actual es:</p>
    <?php
    echo "<strong>" . date("H:i:s") . "</strong>";
    ?>
</body>
</html>