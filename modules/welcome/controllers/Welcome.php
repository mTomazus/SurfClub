<?php
class Welcome extends Trongate {

	/**
	 * Renders the (default) homepage for public access.
	 *
	 * @return void
	 */

	
	public function welcome(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'welcome';
		$this->template('public', $data);
	}

	public function home(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'home';
		$this->template('public', $data);
	}

	public function index(): void {
		$this->module('trongate_pages');
		$this->trongate_pages->display();
	}

	public function varzybos(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'varzybos';
		$this->template('public', $data);
	}

}