<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\TimerSetting;
use Illuminate\Http\Request;

class TimerSettingController extends Controller
{
    public function index()
    {
        return view('admin.timer', ['settings' => TimerSetting::orderBy('key')->get()]);
    }

    public function update(Request $request, string $key)
    {
        $request->validate(['duration_minutes' => 'required|integer|min:5|max:180']);
        $s = TimerSetting::where('key', $key)->firstOrFail();
        $s->update(['duration_minutes' => $request->duration_minutes, 'updated_by' => auth()->id()]);
        return back()->with('success', "Timer \"{$s->label}\" diubah ke {$request->duration_minutes} menit.");
    }
}
