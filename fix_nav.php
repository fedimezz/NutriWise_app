<?php
$dir = 'c:/Users/hp/Desktop/web/NutriWise-main/views/back/';
$files = glob($dir . '*.php');

$newNav = <<<EOT
<nav>
                <a href="index.php?page=admin_dashboard" class="<?= (\$page=='admin_dashboard') ? 'active' : '' ?>">📊 Dashboard</a>
                <a href="index.php?page=admin_users" class="<?= (strpos(\$page, 'admin_user') !== false || strpos(\$page, 'user') !== false && \$page != 'profile') ? 'active' : '' ?>">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments" class="<?= (strpos(\$page, 'aliment') !== false) ? 'active' : '' ?>">🥗 Aliments</a>
                <a href="index.php?page=admin_suivis" class="<?= (strpos(\$page, 'suivi') !== false) ? 'active' : '' ?>">📈 Suivis</a>
                <a href="#">📖 Recettes</a>
                <a href="#">📅 Plans</a>
            </nav>
EOT;

$count = 0;
foreach($files as $f) {
    $content = file_get_contents($f);
    // Find the <nav> block
    if(preg_match('/<nav>.*?<\/nav>/s', $content, $matches)) {
        $content = str_replace($matches[0], $newNav, $content);
        file_put_contents($f, $content);
        $count++;
    }
}
echo "Fixed $count files.";
?>
