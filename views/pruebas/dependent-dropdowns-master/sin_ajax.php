<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
} catch (PDOException $exception) {
    die($exception->getMessage());
}

$sql = 'SELECT co.id AS CountryID, co.name AS CountryName, ci.id AS CityID, ci.name AS CityName FROM countries co INNER JOIN cities ci ON ci.country_id = co.id ORDER BY co.name, ci.name';

try {
    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    die($exception->getMessage());
}

$countryNames = array_unique(array_column($data, 'CountryName'));
$countryIds = array_unique(array_column($data, 'CountryID'));
?>
<html>
    <body>
        <form action="process.php" method="post">
            <select name="country" id="country">
                <option value="-1"></option>
                <?php
                foreach ($countryNames as $k => $countryName) {
                    ?>
                    <option value="<?php echo $countryIds[$k]; ?>"><?php echo $countryName; ?></option>
                    <?php
                }
                ?>
            </select>
            <select name="city" id="city"></select>
        </form>
    </body>
<script type="application/javascript">
    const cities = Array();
    <?php
    foreach($countryIds as $countryId) {
        $cities = array_values(array_filter($data, function($row) use ($countryId) {
            return $row['CountryID'] === $countryId;
        } ));
        ?>
    cities[<?php echo $countryId;?>] = [ <?php
        for ($i = 0; $i < count($cities) - 1; $i++ ) {
            ?>{ id: <?php echo $cities[$i]['CityID']; ?>, name: "<?php echo $cities[$i]['CityName']; ?>" }, <?php
        }
        ?>{ id: <?php echo $cities[$i]['CityID']; ?>, name: "<?php echo $cities[$i]['CityName']; ?>" } ];
    <?php
    }
    ?>

    document.getElementById('country').addEventListener('change', function(e) {
        let ownCities = cities[e.target.value];

        let cityDropdown = document.getElementById('city');
        cityDropdown.innerText = null;

        ownCities.forEach( function(c) {
            var option = document.createElement('option');
            option.text = c.name;
            option.value = c.id;
            cityDropdown.appendChild(option);
        } )
    });
</script>
</html>