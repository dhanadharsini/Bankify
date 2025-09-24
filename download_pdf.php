<?php
require 'db.php';
require_once('tcpdf/tcpdf.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    $userRes = $conn->query("SELECT name FROM users WHERE id = $id");
    if ($userRes->num_rows == 0) {
        die("User not found.");
    }
    $user = $userRes->fetch_assoc()['name'];

    $result = $conn->query("
        SELECT NULL AS tran_date, 'Opening Balance' AS Particulars, opening AS deposit, 0 AS withdraw 
        FROM users WHERE id = $id
        UNION ALL
        SELECT tran_date, Particulars, deposit, withdraw 
        FROM transact WHERE id = $id
        ORDER BY tran_date ASC
    ");

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Bank Application');
    $pdf->SetTitle('Bank Statement');
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 10);

    $html = "<h2 style='text-align:center;'>Bank Statement</h2>";
    $html .= "<p><strong>User:</strong> {$user} (ID: {$id})</p>";

    // Adjusted widths (in %), more in line with typical data sizes:
    // Date - 20%, Particulars - 40%, Deposit - 13%, Withdraw - 13%, Balance - 14%
    $html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse: collapse;">
        <thead>
            <tr style="background-color: #004080; color: white; font-weight: bold; text-align: center;">
                <th width="20%" style="padding: 8px;">Transaction Date</th>
                <th width="20%" style="padding: 8px; text-align: left;">Particulars</th>
                <th width="20%" style="padding: 8px; text-align: right;">Deposit (₹)</th>
                <th width="20%" style="padding: 8px; text-align: right;">Withdraw (₹)</th>
                <th width="20%" style="padding: 8px; text-align: right;">Balance (₹)</th>
            </tr>
        </thead>
        <tbody>';

    $balance = 0;
    while ($row = $result->fetch_assoc()) {
        $deposit = (float)$row['deposit'];
        $withdraw = (float)$row['withdraw'];
        $balance += $deposit - $withdraw;

        $tranDate = $row['tran_date'] ?? '-';
        $particulars = htmlspecialchars($row['Particulars']);

        $html .= "<tr>
                    <td style='text-align: center; padding: 6px;'>{$tranDate}</td>
                    <td style='padding-left: 15px; text-align: left;'>{$particulars}</td>
                    <td style='text-align: right; padding: 6px;'>₹" . number_format($deposit, 2) . "</td>
                    <td style='text-align: right; padding: 6px;'>₹" . number_format($withdraw, 2) . "</td>
                    <td style='text-align: right; padding: 6px;'>₹" . number_format($balance, 2) . "</td>
                </tr>";
    }

    $html .= "</tbody></table>";

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output("bank_statement_user_{$id}.pdf", 'D');
} else {
    echo "Invalid request.";
}

$conn->close();
?>
