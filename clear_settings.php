<?php
require_once 'config/database.php';

try {
    // Keep the ID 1 but clear the sensitive/school specific fields
    // Keeping app_name as a generic default if needed, or clear it too. 
    // User said "kosongkan pengaturan identitas sekolah". 
    // I will clear everything except ID.
    $sql = "UPDATE settings SET 
            app_name = '', 
            school_name = '', 
            headmaster_name = '', 
            headmaster_nip = '', 
            npsn = '', 
            address = '', 
            website = '', 
            graduation_date = NULL, 
            letter_number = '',
            logo = NULL,
            favicon = NULL -- In case it still exists in DB structure
            WHERE id = 1";
            
    // Note: 'favicon' column might exist or not depending on previous steps (I didn't drop the column in DB, just removed code)
    // To be safe against column not found error if I try to update 'favicon' and it was dropped?
    // Actually I never dropped the column in DB, just removed the code usage. 
    // But verify_db showed it added.
    // However, if I want to be super safe:
    
    $stmt = $pdo->prepare("UPDATE settings SET 
            app_name = '', 
            school_name = '', 
            headmaster_name = '', 
            headmaster_nip = '', 
            npsn = '', 
            address = '', 
            website = '', 
            graduation_date = NULL, 
            letter_number = '',
            logo = NULL
            WHERE id = 1");
            
    $stmt->execute();
    echo "Pengaturan identitas sekolah berhasil dikosongkan.";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
