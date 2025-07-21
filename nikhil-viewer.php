<?php
/**
 * Plugin Name:       Nikhil Resume Viewer
 * Description:       Upload and display a resume PDF on your WordPress site using a shortcode.
 * Version:           1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Nikhil Wagh
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       nikhil-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Admin Menu
add_action('admin_menu', 'nikhil_resume_viewer_menu');
function nikhil_resume_viewer_menu() {
	add_menu_page('Resume Viewer', 'Resume Viewer', 'manage_options', 'nikhil-resume-viewer', 'nikhil_resume_viewer_page');
}

// Admin Page UI
function nikhil_resume_viewer_page() {
	echo '<div class="wrap"><h1>Upload Your Resume (PDF)</h1>';

	if (isset($_POST['submit_resume']) && !empty($_FILES['resume_file']['name'])) {
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		$upload_id = media_handle_upload('resume_file', 0);
		if (!is_wp_error($upload_id)) {
			update_option('nikhil_resume_pdf_url', wp_get_attachment_url($upload_id));
			echo '<p><strong>Resume uploaded successfully!</strong></p>';
		} else {
			echo '<p><strong>Error uploading file.</strong></p>';
		}
	}

	$resume_url = get_option('nikhil_resume_pdf_url');

	echo '<form method="post" enctype="multipart/form-data">';
	echo '<input type="file" name="resume_file" accept="application/pdf" required />';
	echo '<input type="submit" name="submit_resume" class="button button-primary" value="Upload Resume" />';
	echo '</form>';

	if ($resume_url) {
		echo '<hr><h2>Current Resume:</h2>';
		echo "<iframe src='$resume_url' width='100%' height='600px'></iframe>";
	}

	echo '</div>';
}

// Shortcode
add_shortcode('nikhil_resume', 'nikhil_resume_shortcode');
function nikhil_resume_shortcode() {
	$resume_url = get_option('nikhil_resume_pdf_url');
	if ($resume_url) {
		return "<iframe src='$resume_url' width='100%' height='600px'></iframe>";
	}
	return '<p>No resume uploaded yet.</p>';
}
