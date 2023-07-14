<?php
require "vendor/autoload.php";
//ignore deprecated warning, because we dont se emojis in description
error_reporting(E_ALL ^ E_DEPRECATED);

$CSVvar = fopen("dataset-gymbeam-product-descriptions-eng.csv", "r");
use Sentiment\Analyzer;
$analyzer = new Analyzer();

if ($CSVvar !== FALSE) {
    ?>
    <html>

    <head>
        <style>
            table,
            th,
            td {
                border: 1px solid black;
            }
        </style>
    </head>

    <body>
        <table style="border:1px solid black">
            <thead>
                <tr>
                    <th><b>Most</b></th>
                    <th><b>Positivity Coef</b></th>
                    <th><b>Name</b></th>
                    <th><b>Description</b></th>
                </tr>
            </thead>
            <?php
            $mostNegativeByCompound = array();
            $mostPositiveByCompound = array();
            $isFirstObj = true;
            while (!feof($CSVvar)) {
                $data = fgetcsv($CSVvar, 1000, ",");
                if (!empty($data)) {
                    $output_text = $analyzer->getSentiment(strip_tags($data[1]));
                    if ($isFirstObj) {
                        //setting properties for both arrays if array from csv is first
                        $mostNegativeByCompound["product"] = $data;
                        $mostNegativeByCompound["coef"] = $output_text["compound"];
                        $mostPositiveByCompound["product"] = $data;
                        $mostPositiveByCompound["coef"] = $output_text["compound"];
                        $isFirstObj = false;
                    }
                    if ($output_text["compound"] < $mostNegativeByCompound["coef"]) {
                        $mostNegativeByCompound["product"] = $data;
                        $mostNegativeByCompound["coef"] = $output_text["compound"];
                    }
                    if ($output_text["compound"] > $mostPositiveByCompound["coef"]) {
                        $mostPositiveByCompound["product"] = $data;
                        $mostPositiveByCompound["coef"] = $output_text["compound"];
                    }

                }
            }
            ?>
            <tr>
                <td>Most Negative</td>
                <td>
                    <?php echo $mostNegativeByCompound["coef"] ?>
                </td>
                <td>
                    <?php echo $mostNegativeByCompound["product"][0] ?>
                </td>
                <td>
                    <?php echo $mostNegativeByCompound["product"][1] ?>
                </td>
            </tr>
            <tr>
                <td>Most positive</td>
                <td>
                    <?php echo $mostPositiveByCompound["coef"] ?>
                </td>
                <td>
                    <?php echo $mostPositiveByCompound["product"][0] ?>
                </td>
                <td>
                    <?php echo $mostPositiveByCompound["product"][1] ?>
                </td>
            </tr>
            <?php
}
?>
    </table>
</body>

</html>
<?php
fclose($CSVvar);
?>