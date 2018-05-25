<?php
/*
 * Image Helper Class
 */

class SRImageHelperClass {

	private $container_width;
	private $container_height;
	private $id; // slide/attachment ID
	private $url;
	private $path; // path to attachment on server

	public function __construct($attachment_id, $width, $height) {
		$upload_dir = wp_upload_dir();

		$this->id = $attachment_id;
		$this->url = $upload_dir['baseurl'] . '/' . get_post_meta($attachment_id, '_wp_attached_file', true);
		$this->path = get_attached_file($attachment_id);
		$this->container_width = $width;
		$this->container_height = $height;
	}

	private function get_crop_dimensions($image_width, $image_height) {
		return array('width' => (int)$this->container_width, 'height' => (int)$this->container_height);
	}


	function get_image_url() {
		// Get the image file path
		if (!strlen($this->path)) {
			return $this->url;
		}

		// if the file exists, just return it without going any further
		$dest_file_name = $this->get_destination_file_name(array(
						'width' => $this->container_width,
						'height' => $this->container_height
				)
		);

		if (file_exists($dest_file_name)) {
			return str_replace(basename($this->url), basename($dest_file_name), $this->url);
		}

		// file doesn't exist, detect required size
		$orig_size = $this->get_original_image_dimensions();

		// bail out if we can't find the image dimensions
		if ($orig_size == false) {
			return $this->url;
		}

		// required size
		$dest_size = $this->get_crop_dimensions($orig_size['width'], $orig_size['height']);

		// check if a resize is needed
		if ($orig_size['width'] == $dest_size['width'] && $orig_size['height'] == $dest_size['height']) {
			return $this->url;
		}

		$dest_file_name = $this->get_destination_file_name($dest_size);

		if (file_exists($dest_file_name))
		{
			// good. no need for resize, just return the URL
			$dest_url = str_replace(basename($this->url), basename($dest_file_name), $this->url);
		}
		else
		{
			// resize, assuming we're allowed to use the image editor
			$dest_url = $this->resize_image($orig_size, $dest_size, $dest_file_name);
		}

		return $dest_url;
	}

	private function get_original_image_dimensions() {
		$size = array();

		$meta = wp_get_attachment_metadata($this->id);

		if (isset($meta['width'], $meta['height'])) {
			return $meta;
		}

		$image = wp_get_image_editor($this->path);
		if (!is_wp_error($image)) {
			$size = $image->get_size();
			return $size;
		}

		return false;
	}

	private function get_destination_file_name($dest_size) {
		$info = pathinfo($this->path);
		$dir = $info['dirname'];
		$ext = $info['extension'];
		$name = wp_basename($this->path, ".$ext");
		$dest_file_name = "{$dir}" . '/' . "{$name}-{$dest_size['width']}x{$dest_size['height']}.{$ext}";

		return $dest_file_name;
	}

	/**
	 * Use WP_Image_Editor to create a resized image and return the URL for that image
	 *
	 * @param array $orig_size
	 * @param array $dest_size
	 * @return string
	 */
	private function resize_image($orig_size, $dest_size, $dest_file_name) {
		// load image
		$image = wp_get_image_editor($this->path);

		// editor will return an error if the path is invalid
		if (is_wp_error($image)) {
			if (is_admin()) {
				echo '<div id="message" class="error">';
				echo '<p><strong>' . esc_html__('ERROR', 'inbound') .'</strong> ' . $image->get_error_message() . ' ' . esc_html__('Check file permissions.', 'inbound') . '</p>';
				echo '<button class="toggle">' . esc_html__('Show Details', 'inbound') . '</button>';
				echo '<div class="message" style="display: none;"><br />' . esc_html__('Slide ID: ', 'inbound') . $this->id . '<pre>';
				var_dump($image);
				echo '</pre></div>';
				echo '</div>';
			}
			return $this->url;
		}

		$dims = image_resize_dimensions($orig_size['width'], $orig_size['height'], $dest_size['width'], $dest_size['height'], true);

		if ($dims) {
			list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;
			$image->crop($src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h);
		}

		$saved = $image->save($dest_file_name);

		if (is_wp_error($saved)) {
			return $this->url;
		}

		$backup_sizes = get_post_meta($this->id,'_wp_attachment_backup_sizes',true);

		if (!is_array($backup_sizes)) {
			$backup_sizes = array();
		}

		$backup_sizes["resized-{$dest_size['width']}x{$dest_size['height']}"] = $saved;
		update_post_meta($this->id,'_wp_attachment_backup_sizes', $backup_sizes);

		$url = str_replace(basename($this->url), basename($saved['path']), $this->url);

		return $url;
	}
}

?>