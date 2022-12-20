<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class RecapController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'weekly');

        $activities = Activity::query()
            ->where('institution_id', auth()->user()->institution_id)
            ->orderBy('date', 'desc');

        if ($type === 'weekly') {
            $activities->whereBetween(
                'date',
                [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]
            );
        } elseif ($type === 'monthly') {
            $activities->whereBetween(
                'date',
                [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]
            );
        } elseif ($type === 'yearly') {
            $activities->whereBetween(
                'date',
                [now()->startOfYear()->format('Y-m-d'), now()->endOfYear()->format('Y-m-d')]
            );
        }

        $data = [
            'institution' => auth()->user()->institution->name,
            'activities' => $activities->get()->transform(
                fn ($activity, $index) => [
                    'id' => $index + 1,
                    'name' => $activity->name,
                    'date' => $activity->date,
                    'description' => $activity->description,
                ]
            ),
        ];

        $filename = date('y-m-d', time()).'_'.Str::random(5).'.docx';

        $templateProcessor = new TemplateProcessor(storage_path($type.'Recap.docx'));
        $templateProcessor->setValue('institution', $data['institution']);
        $templateProcessor->cloneRowAndSetValues('id', $data['activities']->toArray());
        $file = $templateProcessor->save($filename);

        return response()->download($file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }
}
