<?php
// ============================================================
// inaffi.com — Image Upload & Compression Handler
// ============================================================
// Uses PHP GD library (built into Hostinger PHP)
// Compresses uploaded images and saves to uploads/ folder
//
// Usage:
//   require_once '../includes/upload.php';
//   $path = save_image($_FILES['outfit_image'], 'outfits');
//   // $path = "uploads/outfits/a3f8c2d1b7e9f042.jpg"  (store this in DB)
//   // Returns null if no file uploaded (field was empty)
//   // Throws RuntimeException on validation/processing error
// ============================================================

/**
 * Validate, compress, and save an uploaded image file.
 *
 * @param array  $file    Entry from $_FILES (e.g. $_FILES['outfit_image'])
 * @param string $folder  Sub-folder in uploads/: 'outfits' | 'products' | 'profiles'
 * @return string|null    Relative path to saved file, or null if no file uploaded
 * @throws RuntimeException on invalid file or processing error
 */
function save_image(array $file, string $folder): ?string {
    // No file uploaded — field was left empty (not an error)
    if (empty($file['tmp_name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // ── Upload Error Check ────────────────────────────────────
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server maximum size.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form maximum size.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'No temporary folder available.',
            UPLOAD_ERR_CANT_WRITE => 'Could not write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'Upload blocked by server extension.',
        ];
        throw new RuntimeException($errors[$file['error']] ?? 'Unknown upload error.');
    }

    // ── File Size Check ───────────────────────────────────────
    $max_bytes = defined('MAX_UPLOAD_MB') ? MAX_UPLOAD_MB * 1024 * 1024 : 10 * 1024 * 1024;
    if ($file['size'] > $max_bytes) {
        $mb = defined('MAX_UPLOAD_MB') ? MAX_UPLOAD_MB : 10;
        throw new RuntimeException("Image must be under {$mb}MB.");
    }

    // ── MIME Type Validation (by content, not extension) ─────
    $allowed_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo        = new finfo(FILEINFO_MIME_TYPE);
    $mime         = $finfo->file($file['tmp_name']);

    if (!in_array($mime, $allowed_mime, true)) {
        throw new RuntimeException('Only JPG, PNG, WebP, or GIF images are allowed.');
    }

    // ── Determine Max Dimensions ──────────────────────────────
    $max_px = match($folder) {
        'outfits'  => defined('OUTFIT_IMG_MAX_PX')  ? OUTFIT_IMG_MAX_PX  : 800,
        'products' => defined('PRODUCT_IMG_MAX_PX') ? PRODUCT_IMG_MAX_PX : 400,
        'profiles' => defined('PROFILE_IMG_MAX_PX') ? PROFILE_IMG_MAX_PX : 300,
        default    => 800,
    };
    $quality = defined('IMAGE_QUALITY') ? IMAGE_QUALITY : 85;

    // ── Load Image with GD ────────────────────────────────────
    $source = match($mime) {
        'image/jpeg' => @imagecreatefromjpeg($file['tmp_name']),
        'image/png'  => @imagecreatefrompng($file['tmp_name']),
        'image/webp' => @imagecreatefromwebp($file['tmp_name']),
        'image/gif'  => @imagecreatefromgif($file['tmp_name']),
        default      => false,
    };

    if ($source === false) {
        throw new RuntimeException('Could not process the image. Please try a different file.');
    }

    // ── Calculate New Dimensions (maintain aspect ratio) ──────
    $orig_w = imagesx($source);
    $orig_h = imagesy($source);

    if ($orig_w > $max_px || $orig_h > $max_px) {
        $ratio  = min($max_px / $orig_w, $max_px / $orig_h);
        $new_w  = (int) round($orig_w * $ratio);
        $new_h  = (int) round($orig_h * $ratio);
    } else {
        $new_w  = $orig_w;
        $new_h  = $orig_h;
    }

    // ── Create Resized Canvas ─────────────────────────────────
    $dest = imagecreatetruecolor($new_w, $new_h);

    // Preserve transparency for PNG / WebP
    if (in_array($mime, ['image/png', 'image/webp'], true)) {
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
        imagefilledrectangle($dest, 0, 0, $new_w, $new_h, $transparent);
    }

    imagecopyresampled($dest, $source, 0, 0, 0, 0, $new_w, $new_h, $orig_w, $orig_h);
    imagedestroy($source);

    // ── Generate Unique Filename ──────────────────────────────
    // Random hash — never expose original filename (security)
    $filename  = bin2hex(random_bytes(8)) . '.jpg';  // always save as JPEG
    $rel_path  = 'uploads/' . $folder . '/' . $filename;
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $rel_path;

    // ── Ensure Directory Exists ───────────────────────────────
    $dir = dirname($full_path);
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        imagedestroy($dest);
        throw new RuntimeException("Could not create upload directory: {$folder}");
    }

    // ── Save Compressed JPEG ──────────────────────────────────
    // Always output as JPEG regardless of input format (strips EXIF, WebP metadata)
    $saved = imagejpeg($dest, $full_path, $quality);
    imagedestroy($dest);

    if (!$saved) {
        throw new RuntimeException('Could not save the image. Check uploads/ folder permissions.');
    }

    return $rel_path;   // e.g. "uploads/outfits/a3f8c2d1b7e9f042.jpg"
}


/**
 * Delete an uploaded image file from disk (when outfit/product is deleted).
 *
 * @param string|null $rel_path  Relative path stored in DB (e.g. "uploads/outfits/abc.jpg")
 */
function delete_image(?string $rel_path): void {
    if (empty($rel_path)) return;

    // Safety: only delete files inside uploads/ directory
    if (!str_starts_with($rel_path, 'uploads/')) return;

    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $rel_path;
    if (is_file($full_path)) {
        @unlink($full_path);
    }
}
