<?php
namespace App\Http\Controllers\Shared;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class RoomController extends Controller { public function index() { return view('admin.coming-soon', ['page' => 'RoomController']); } public function raIndex(){} public function raShow($id){} public function updateStatus(Request $r, $id){} public function logs(){} }
