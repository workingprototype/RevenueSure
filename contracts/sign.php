<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$contract_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch contract details
$stmt = $conn->prepare("SELECT * FROM contracts WHERE id = :contract_id");
$stmt->bindParam(':contract_id', $contract_id);
$stmt->execute();
$contract = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contract) {
    header("Location: " . BASE_URL . "contracts/manage");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signature'])) {
    $signature_data = $_POST['signature'];
    $user_id = $_SESSION['user_id'];

    // Get user IP address
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'N/A';

    // Get Geolocation using GeoIP (requires PHP extension)
    $geolocation_data = null;
        if(function_exists('geoip_record_by_name')){
            $location = geoip_record_by_name($ip_address);
                 if ($location) {
                        $geolocation_data = json_encode([
                            "latitude" => $location['latitude'],
                             "longitude" => $location['longitude'],
                             "city" => $location['city'],
                            "region" => $location['region'],
                            "country" => $location['country_code']
                       ]);
                }
         }
    // Get timezone
    $timezone = date_default_timezone_get();

    // Get browser and OS details
     $device_info = json_encode(getBrowserInfo());

    $stmt = $conn->prepare("INSERT INTO contract_signatures (contract_id, user_id, signature_data) VALUES (:contract_id, :user_id, :signature_data)");
    $stmt->bindParam(':contract_id', $contract_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':signature_data', $signature_data);
    if ($stmt->execute()) {
          // Update contract to Active.
          $stmt = $conn->prepare("INSERT INTO contract_status (contract_id, status) VALUES (:contract_id, 'Active')");
            $stmt->bindParam(':contract_id', $contract_id);
           $stmt->execute();

        //log action for audit
         $stmt = $conn->prepare("INSERT INTO contract_audit_trail (contract_id, user_id, action, ip_address, geolocation_data, timezone, device_info) VALUES (:contract_id, :user_id, 'Signature Added', :ip_address, :geolocation_data, :timezone, :device_info)");
            $stmt->bindParam(':contract_id', $contract_id);
            $stmt->bindParam(':user_id', $user_id);
           $stmt->bindParam(':ip_address', $ip_address);
            $stmt->bindParam(':geolocation_data', $geolocation_data);
             $stmt->bindParam(':timezone', $timezone);
           $stmt->bindParam(':device_info', $device_info);
         $stmt->execute();
          $success = "Contract signed successfully!";
          header("Location: " . BASE_URL . "contracts/view?id=$contract_id&success=true");
            exit();
    } else {
         $error = "Error saving signature.";
       }
}

function getBrowserInfo(){
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "Unknown";
    $os = "Unknown";

    // Browser detection
    if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
        $browser = 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Chrome/i', $user_agent)) {
       $browser = 'Chrome';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
     } elseif (preg_match('/Opera/i', $user_agent)) {
        $browser = 'Opera';
    }

   // OS detection
    if (preg_match('/Windows/i', $user_agent)) {
        $os = 'Windows';
    } elseif (preg_match('/Macintosh|Mac OS X/i', $user_agent)) {
        $os = 'Mac';
    } elseif (preg_match('/Linux/i', $user_agent)) {
        $os = 'Linux';
    } elseif (preg_match('/Android/i', $user_agent)) {
       $os = 'Android';
    } elseif (preg_match('/iPhone|iPad|iPod/i', $user_agent)) {
       $os = 'iOS';
    }
   return [
     'browser' => $browser,
     'os' => $os
     ];
}


?>
<style>
#signature-pad {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }
</style>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Sign Contract: <?php echo htmlspecialchars($contract['subject']); ?></h1>
          <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <?php echo $success; ?>
                </div>
           <?php endif; ?>
      <div class="bg-white p-6 rounded-lg shadow-md mb-8">
      <p class="text-gray-800 leading-relaxed mb-4"> Please review the contract, and then sign below.</p>
            <div class="border border-gray-200 p-4 rounded-lg">
                <?php echo $contract['contract_text']; ?>
            </div>
       <div class="mt-8">
          <canvas id="signature-pad" width="600" height="300" class="border"></canvas>
            <div class="mt-4 flex justify-end gap-4">
                <button onclick="signaturePad.clear()" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition duration-300" id="clearButton">Clear</button>
            <button onclick="uploadSignature()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Sign Contract</button>
           </div>
         </div>
       </div>
     
    <div class="mt-6">
        <a href="<?php echo BASE_URL; ?>contracts/view?id=<?php echo $contract_id; ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Contract</a>
     </div>
   <form id="signatureForm" method="POST" action="" style="display:none;">
   <?php echo csrfTokenInput(); ?>
        <input type="hidden" id="signature" name="signature">
    </form>

</div>
 <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
    <script>
    const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas);

       function uploadSignature() {
           if (signaturePad.isEmpty()) {
                 alert('Please add a signature');
             }else{
               const signatureImage = signaturePad.toDataURL('image/png');
                document.getElementById('signature').value = signatureImage;
                document.getElementById('signatureForm').submit();
            }
        }
</script>
