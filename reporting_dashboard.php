<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch data for reporting
// 1. Lead Conversion Rate
$stmt = $conn->prepare("SELECT 
    COUNT(*) as total_leads, 
    SUM(CASE WHEN status = 'Converted' THEN 1 ELSE 0 END) as converted_leads 
    FROM leads");
$stmt->execute();
$conversion_data = $stmt->fetch(PDO::FETCH_ASSOC);
$conversion_rate = $conversion_data['total_leads'] > 0 ? 
    round(($conversion_data['converted_leads'] / $conversion_data['total_leads']) * 100, 2) : 0;

// 2. Lead Source Analysis
$stmt = $conn->prepare("SELECT source, COUNT(*) as lead_count FROM leads GROUP BY source");
$stmt->execute();
$lead_sources = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Sales Performance
$stmt = $conn->prepare("SELECT 
    users.username, 
    COUNT(leads.id) as total_leads, 
    SUM(CASE WHEN leads.status = 'Converted' THEN 1 ELSE 0 END) as converted_leads 
    FROM leads 
    JOIN users ON leads.assigned_to = users.id 
    GROUP BY users.username");
$stmt->execute();
$sales_performance = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Reporting & Analytics</h1>

<!-- Lead Conversion Rate -->
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Lead Conversion Rate</h2>
    <p class="text-2xl font-bold text-blue-600"><?php echo $conversion_rate; ?>%</p>
    <p class="text-gray-600 mt-2">Percentage of leads converted to customers.</p>
</div>

<!-- Lead Source Analysis -->
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Lead Source Analysis</h2>
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-2">Source</th>
                <th class="px-4 py-2">Number of Leads</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($lead_sources): ?>
                <?php foreach ($lead_sources as $source): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($source['source']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($source['lead_count']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" class="px-4 py-2 text-center text-gray-600">No lead sources found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Sales Performance -->
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Sales Performance</h2>
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-2">Salesperson</th>
                <th class="px-4 py-2">Total Leads</th>
                <th class="px-4 py-2">Converted Leads</th>
                <th class="px-4 py-2">Conversion Rate</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($sales_performance): ?>
                <?php foreach ($sales_performance as $performance): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($performance['username']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($performance['total_leads']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($performance['converted_leads']); ?></td>
                        <td class="px-4 py-2">
                            <?php 
                            $rate = $performance['total_leads'] > 0 ? 
                                round(($performance['converted_leads'] / $performance['total_leads']) * 100, 2) : 0;
                            echo $rate . '%';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-600">No sales performance data found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include footer
require 'footer.php';
?>