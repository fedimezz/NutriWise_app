<?php
// Exemple: $logs doit venir du controller
// Si tu veux tester sans DB, décommente ça :

/*
$logs = [
    [
        'prenom' => 'Ali',
        'nom' => 'Ben',
        'action' => 'login',
        'description' => 'User logged in',
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'prenom' => 'Sara',
        'nom' => 'Khaled',
        'action' => 'delete',
        'description' => 'Deleted a user',
        'created_at' => date('Y-m-d H:i:s')
    ]
];
*/
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Activity Feed</title>

<style>

/* ===== PAGE ===== */
body {
    margin: 0;
    background: #f5f7f6;
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

/* ===== CONTAINER ===== */
.container {
    padding: 30px;
}

/* ===== CARD ===== */
.card {
    background: #fff;
    border-radius: 18px;
    padding: 20px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}

/* ===== HEADER ===== */
.card-header h2 {
    color: #1f6b3b;
    margin-bottom: 15px;
}

/* ===== TABLE ===== */
.activity-table {
    width: 100%;
    border-collapse: collapse;
}

.activity-table thead {
    background: #eaf4ee;
}

.activity-table th {
    padding: 14px;
    text-align: left;
    color: #1f6b3b;
}

.activity-table td {
    padding: 14px;
    border-bottom: 1px solid #eee;
}

/* Hover */
.activity-table tr:hover {
    background: #f8fbf9;
}

/* ===== BADGES ===== */
.badge {
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

/* Colors */
.badge-login {
    background: #d4edda;
    color: #155724;
}

.badge-delete {
    background: #f8d7da;
    color: #721c24;
}

.badge-create {
    background: #d1ecf1;
    color: #0c5460;
}

.badge-update {
    background: #fff3cd;
    color: #856404;
}

/* Default */
.badge-default {
    background: #e2e3e5;
    color: #333;
}

/* Empty */
.empty {
    text-align: center;
    padding: 20px;
    color: #888;
}

</style>
</head>

<body>

<div class="container">
    <div class="card">

        <div class="card-header">
            <h2>📊 Activity Feed</h2>
        </div>

        <table class="activity-table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>

                        <?php
                        $fullname = ($log['prenom'] ?? '') . ' ' . ($log['nom'] ?? '');
                        $actionClass = 'badge-' . ($log['action'] ?? 'default');
                        ?>

                        <tr>
                            <td>
                                <?php echo htmlspecialchars(trim($fullname) ?: 'Unknown'); ?>
                            </td>

                            <td>
                                <span class="badge <?php echo $actionClass; ?>">
                                    <?php echo htmlspecialchars($log['action'] ?? 'unknown'); ?>
                                </span>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($log['description'] ?? '-'); ?>
                            </td>

                            <td>
                                <?php
                                echo isset($log['created_at'])
                                    ? date('d/m/Y H:i', strtotime($log['created_at']))
                                    : '-';
                                ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty">
                            Aucun log trouvé
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>