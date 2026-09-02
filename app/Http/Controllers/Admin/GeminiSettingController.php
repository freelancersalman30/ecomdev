<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;

class GeminiSettingController extends Controller
{
    public function index(GeminiAiService $geminiAiService)
    {
        $apiKey = $geminiAiService->getApiKey();
        $model = Setting::get('gemini_model', 'gemini-1.5-flash');
        $temperature = Setting::get('gemini_temperature', '0.4');
        $autoSeo = Setting::get('gemini_auto_seo', '1');

        $availableModels = [
            'gemini-1.5-flash' => [
                'name' => 'Gemini 1.5 Flash (Recommended)',
                'description' => 'Ultra-fast, high accuracy, highly cost-effective & free tier available on Google AI Studio.',
                'tag' => 'Fast & Recommended',
            ],
            'gemini-2.0-flash' => [
                'name' => 'Gemini 2.0 Flash (Next-Gen Preview)',
                'description' => 'Google\'s newest flagship lightweight model with superior multimodal reasoning.',
                'tag' => 'Cutting Edge',
            ],
            'gemini-1.5-pro' => [
                'name' => 'Gemini 1.5 Pro',
                'description' => 'Deep reasoning for complex hardware datasheets, extensive pinouts, and elaborate copywriting.',
                'tag' => 'High Reasoning',
            ],
        ];

        return view('admin.settings.gemini', compact('apiKey', 'model', 'temperature', 'autoSeo', 'availableModels'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'gemini_api_key' => 'nullable|string|max:255',
            'gemini_model' => 'required|string|max:50',
            'gemini_temperature' => 'required|numeric|min:0|max:1',
            'gemini_auto_seo' => 'nullable|boolean',
        ]);

        Setting::set('gemini_api_key', trim($request->input('gemini_api_key', '')), 'gemini');
        Setting::set('gemini_model', $request->input('gemini_model', 'gemini-1.5-flash'), 'gemini');
        Setting::set('gemini_temperature', (string) $request->input('gemini_temperature', '0.4'), 'gemini');
        Setting::set('gemini_auto_seo', $request->has('gemini_auto_seo') ? '1' : '0', 'gemini');

        return redirect()->route('admin.settings.gemini')->with('success', 'Google Gemini AI Settings saved successfully!');
    }

    public function testConnection(Request $request, GeminiAiService $geminiAiService)
    {
        $apiKey = $request->input('api_key') ?: $geminiAiService->getApiKey();
        $model = $request->input('model') ?: $geminiAiService->getModel();

        $result = $geminiAiService->testConnection($apiKey, $model);

        return response()->json($result);
    }
}
