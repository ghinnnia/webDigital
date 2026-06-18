<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\OvertimeSetting;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeSettingController extends Controller
{
    public function index()
    {
        $settings = OvertimeSetting::with('division')->get();
        $divisions = Divisi::all();
        
        $defaultSetting = OvertimeSetting::whereNull('division_id')->first();
        
        return view('finance.overtime_settings', compact('settings', 'divisions', 'defaultSetting'));
    }
    
    public function updateDefault(Request $request)
    {
        $request->validate([
            'default_rate' => 'required|numeric|min:0',
            'max_rate' => 'required|numeric|min:0|gte:default_rate',
        ]);
        
        $setting = OvertimeSetting::firstOrCreate(
            ['division_id' => null],
            ['default_rate' => 0, 'max_rate' => 0]
        );
        
        $setting->update([
            'default_rate' => $request->default_rate,
            'max_rate' => $request->max_rate,
            'updated_by' => Auth::id(),
        ]);
        
        return redirect()->back()->with('success', 'Setting default berhasil diupdate');
    }
    
    public function updateDivision(Request $request, $divisionId)
    {
        $request->validate([
            'default_rate' => 'required|numeric|min:0',
            'max_rate' => 'required|numeric|min:0|gte:default_rate',
        ]);
        
        $setting = OvertimeSetting::updateOrCreate(
            ['division_id' => $divisionId],
            [
                'default_rate' => $request->default_rate,
                'max_rate' => $request->max_rate,
                'is_active' => $request->has('is_active'),
                'updated_by' => Auth::id(),
            ]
        );
        
        return redirect()->back()->with('success', 'Setting divisi berhasil diupdate');
    }
    
    public function resetDivision($divisionId)
    {
        OvertimeSetting::where('division_id', $divisionId)->delete();
        return redirect()->back()->with('success', 'Setting divisi direset ke default');
    }
}