<?php
namespace App\Http\Controllers\Supervisor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class SpecialCaseController extends Controller { public function index() { return view('admin.coming-soon', ['page' => 'SpecialCaseController']); } public function store(Request $r){} public function show($id){} public function approve($id){} public function returnToRa($id){} public function update(Request $r, $id){} public function resolve($id){} }
