<?php
namespace app\wx\controller;

class Wxauth extends Base {
	public function index() {
		var_dump(session('userinfo'));
	}
}