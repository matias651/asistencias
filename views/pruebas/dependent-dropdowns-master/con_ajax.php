<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
} catch (PDOException $exception) {
    die($exception->getMessage());
}

$sql = 'SELECT co.id AS CountryID, co.name AS CountryName FROM countries co ORDER BY co.name';

try {
    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    die($exception->getMessage());
}
?>
<html>
    <body>
        <form action="process.php" method="post">
            <select name="country" id="country">
                <option value="-1"></option>
                <?php
                foreach ($data as $country) {
                    ?>
                    <option value="<?php echo $country['CountryID']; ?>"><?php echo $country['CountryName']; ?></option>
                    <?php
                }
                ?>
            </select>
            <select name="city" id="city"></select>
        </form>
    </body>
<script type="application/javascript">
    document.getElementById('country').addEventListener('change', function(e) {
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                let ownCities = JSON.parse(this.responseText);

                let cityDropdown = document.getElementById('city');
                cityDropdown.innerText = null;

                ownCities.forEach(function (c) {
                    var option = document.createElement('option');
                    option.text = c.name;
                    option.value = c.id;
                    cityDropdown.appendChild(option);
                });
            }
        };
        xhttp.open("GET", "get_cities.php?country_id=" + e.target.value, true);
        xhttp.send();
    });
</script>
</html>