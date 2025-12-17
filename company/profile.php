<?php
require_once '../includes/config.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: ../login.php');
    exit();
}

$company_id = $_SESSION['company_id'];
$success = '';
$error = '';

// Helper function for flash messages
function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

// 1. FETCH CURRENT COMPANY DATA
$fetch_query = "SELECT * FROM companies WHERE id = '$company_id'";
$fetch_result = mysqli_query($conn, $fetch_query);

if (mysqli_num_rows($fetch_result) == 0) {
    // Should not happen if login is successful
    session_destroy();
    header('Location: ../login.php');
    exit();
}

$company_data = mysqli_fetch_assoc($fetch_result);

// 2. HANDLE FORM SUBMISSION (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    // NOTE: 'contact_person' must exist in the database (as discussed in previous step)
    $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']); 
    $new_password = $_POST['new_password'];
    $current_logo_path = $company_data['logo_path'];
    $logo_path_update = '';

    // Handle Logo Upload (Optional)
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['logo']['tmp_name'];
        $file_name = basename($_FILES['logo']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Create unique file name
        $new_file_name = 'company_' . $company_id . '_logo.' . $file_ext;
        $upload_dir = '../assets/images/logos/';
        
        // Ensure directory exists
        if (!is_dir($upload_dir)) {
             mkdir($upload_dir, 0777, true);
        }

        $target_file = $upload_dir . $new_file_name;
        $logo_path_db = 'assets/images/logos/' . $new_file_name; // Path stored in DB

        // Check file extension
        if (!in_array($file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $error = "Only JPG, PNG, and WEBP images are allowed for the logo.";
        }
        
        // Move file
        if (empty($error)) {
            if (move_uploaded_file($file_tmp, $target_file)) {
                $logo_path_update = ", `logo_path` = '$logo_path_db'";
            } else {
                 $error = "Failed to upload logo. Check directory permissions.";
            }
        }
    }

    if (empty($error)) {
        // Build the Update Query
        $update_password = '';
        if (!empty($new_password)) {
            // NOTE: Use a strong hashing function like password_hash() in production
            $hashed_password = md5($new_password); 
            $update_password = ", `password` = '$hashed_password'";
        }

        $update_query = "UPDATE `companies` SET 
                            `company_name` = '$company_name',
                            `description` = '$description',
                            `email` = '$email',
                            `phone` = '$phone',
                            `address` = '$address',
                            `contact_person` = '$contact_person'
                            $logo_path_update
                            $update_password
                        WHERE `id` = '$company_id'";

        if (mysqli_query($conn, $update_query)) {
            set_flash_message("Profile updated successfully!", 'success');
            // Refresh company data after successful update
            $fetch_result = mysqli_query($conn, $fetch_query);
            $company_data = mysqli_fetch_assoc($fetch_result);
        } else {
            $error = "Failed to update profile: " . mysqli_error($conn);
            set_flash_message($error, 'danger');
        }
    } else {
        set_flash_message($error, 'danger');
    }

    // Redirect to self to prevent form resubmission
    header("Location: profile.php");
    exit();
}


// --- FLASH MESSAGE LOGIC AND FUNCTION DEFINITION ---
// Flash Message Display
$flash_message = $_SESSION['flash_message'] ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';

// Helper function for message styling (Defined before its use in the HTML section)
function get_message_styles($style) {
    return match ($style) {
        'success' => 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;',
        'danger' => 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;',
        default => 'background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;',
    };
}

if (isset($_SESSION['flash_message'])) {
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}
// -----------------------------------------------------------------


$pageTitle = 'Company Profile';
include_once '../includes/header.php';
?>

<div class="container" style="max-width: 800px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    
    <a href="dashboard.php" style="display: inline-block; margin-bottom: 20px; color: #4f46e5; text-decoration: none; font-weight: bold; border: 1px solid #4f46e5; padding: 5px 10px; border-radius: 4px;">
        &laquo; Back to Dashboard
    </a>
    
    <h1 style="font-size: 28px; color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 30px;">
        Company Profile Settings
    </h1>

    <?php if ($flash_message): ?>
        <div style="padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; <?php echo get_message_styles($flash_type); ?>">
            <?php echo $flash_message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="profile.php" enctype="multipart/form-data">

        <div style="margin-bottom: 20px;">
            <label for="company_name" style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Company Name</label>
            <input type="text" name="company_name" id="company_name" class="form-control" value="<?php echo htmlspecialchars($company_data['company_name']); ?>" required
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Company Description</label>
            <textarea name="description" id="description" class="form-control" rows="5" required
                      style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"><?php echo htmlspecialchars($company_data['description']); ?></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="contact_person" style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Contact Person</label>
            <input type="text" name="contact_person" id="contact_person" class="form-control" value="<?php echo htmlspecialchars($company_data['contact_person']); ?>" required
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="flex: 1;">
                <label for="email" style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($company_data['email']); ?>" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <label for="phone" style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Phone</label>
                <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($company_data['phone']); ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="address" style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Address</label>
            <input type="text" name="address" id="address" class="form-control" value="<?php echo htmlspecialchars($company_data['address']); ?>"
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="logo" style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Update Logo (JPG/PNG)</label>
            <div style="display: flex; align-items: center; gap: 15px;">
                <input type="file" name="logo" id="logo" class="form-control" accept=".jpg, .jpeg, .png, .webp"
                       style="padding: 10px 0;">
                
                <?php if (!empty($company_data['logo_path'])): ?>
                    <img src="<?php echo BASE_URL . htmlspecialchars($company_data['logo_path']); ?>" alt="Current Logo" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 1px solid #eee;">
                <?php else: ?>
                     <span style="color: #6c757d;">No logo uploaded</span>
                <?php endif; ?>
            </div>
        </div>

        <div style="border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
            <h3 style="font-size: 1.2rem; color: #4f46e5; margin-bottom: 15px;">Change Password (Optional)</h3>
            <div style="margin-bottom: 20px;">
                <label for="new_password" style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control"
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <small style="color: #888;">Leave blank to keep current password.</small>
            </div>
        </div>
        
        <button type="submit" 
                style="width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background-color 0.3s;">
            Update Profile
        </button>

    </form>
</div>

<?php include_once '../includes/footer.php'; ?>