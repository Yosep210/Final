<?php

$content = file_get_contents('C:\Users\LENOVO\Project\Latihan\pengenalan_rp\source\dhaapp\models\Model_Member.php');
$lines = explode("\n", $content);

foreach ($lines as $i => $line) {
    if (preg_match('/function\s+\w*reward\w*/i', $line)) {
        echo ($i + 1).': '.trim($line)."\n";
    }
}
