<?php
if(isset($_POST['imageData']) && isset($_POST['customizationData'])) {
    $imageData = $_POST['imageData'];
    $customizationData = $_POST['customizationData'];

    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);
    $decodedImage = base64_decode($imageData);

    $folderPath = "customizations/";
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0777, true);
    }

    $timestamp = time();
    $imageFileName = $folderPath . "custom_shoe_" . $timestamp . ".png";
    $jsonFileName  = $folderPath . "custom_shoe_" . $timestamp . ".json";

    $imageSaved = file_put_contents($imageFileName, $decodedImage);
    $jsonSaved = file_put_contents($jsonFileName, $customizationData);

    if($imageSaved && $jsonSaved) {
        echo "Customization saved successfully!<br>";
        echo "<img src='$imageFileName' alt='Customized Shoe' style='max-width:300px;'/><br>";
        echo "<a href='view_customization2.php?json=$jsonFileName' target='_blank'>View 3D Customization</a>";
    } else {
        echo "Error saving the customization.";
    }
} else {
    echo "No image or customization data received.";
}
?>
