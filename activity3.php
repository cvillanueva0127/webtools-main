<!DOCTYPE html>
<html>
<head>
    <title>Factorial Calculator</title>
</head>
<body>

<form method="post">
    Enter a number:
    <input type="number" name="number">
    <input type="submit" value="Calculate">
</form>

<?php

if (isset($_POST['number'])) {

    $number = $_POST['number'];

    echo "<h3>Factorial of $number :</h3>";

    // FOR LOOP
    $fact = 1;

    for ($i = 1; $i <= $number; $i++) {
        $fact *= $i;
    }

    echo "For Loop:<br>";
    echo "$number! = $fact<br><br>";

    // WHILE LOOP
    $fact2 = 1;
    $x = 1;

    while ($x <= $number) {
        $fact2 *= $x;
        $x++;
    }

    echo "While Loop:<br>";
    echo "$number! = $fact2";
}

?>

</body>
</html>