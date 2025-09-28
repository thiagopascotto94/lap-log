<?php
// Function to load the JSON data
function loadData() {
    return json_decode(file_get_contents('../db/laps.json'), true);
}
?>
