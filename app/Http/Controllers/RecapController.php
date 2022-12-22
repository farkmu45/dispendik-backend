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
        $typeTitle = '';
        $recapTemplate = null;

        $activities = Activity::query();

        if (auth()->user()->isUser()) {
            $activities->where('institution_id', auth()->user()->institution_id);
        }

        $activities->orderBy('date', 'desc');

        if ($type === 'weekly') {
            $typeTitle = 'Mingguan';
            $activities->whereBetween(
                'date',
                [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]
            );
        } elseif ($type === 'monthly') {
            $typeTitle = 'Bulanan';
            $activities->whereBetween(
                'date',
                [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]
            );
        } elseif ($type === 'yearly') {
            $typeTitle = 'Tahunan';
            $activities->whereBetween(
                'date',
                [now()->startOfYear()->format('Y-m-d'), now()->endOfYear()->format('Y-m-d')]
            );
        }

        $data = [
            'institution' => auth()->user()->institution->name ?? null,
            'activities' => $activities->get()->transform(
                fn ($activity, $index) => [
                    'id' => $index + 1,
                    'name' => $activity->name,
                    'date' => $activity->date,
                    'description' => $activity->description,
                    'institution' => $activity->institution->name
                ]
            ),
        ];

        $filename = date('y-m-d', time()) . '_' . Str::random(5) . '.docx';

        if (auth()->user()->isAdmin()) {
            $recapTemplate = storage_path('adminRecap.docx');
        } else {
            $recapTemplate = storage_path('userRecap.docx');
        }

        $templateProcessor = new TemplateProcessor($recapTemplate);

        if (auth()->user()->isUser()) {
            $templateProcessor->setValue('institution', $data['institution']);
        }

        $templateProcessor->setValue('type', $typeTitle);
        $templateProcessor->cloneRowAndSetValues('id', $data['activities']->toArray());
        $file = $templateProcessor->save($filename);

        return response()->download($file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }
}
