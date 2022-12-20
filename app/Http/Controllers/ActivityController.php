<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Resources\ActivityCollection;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\User;
use App\Notifications\ActivityCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Activity::class);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $activities = Activity::query();

        if ($user->isUser()) {
            $activities->where('institution_id', $user->institution_id);
        }

        if ($request->query('created_at')) {
            return new ActivityCollection(
                $activities->whereDate('created_at', $request->query('created_at'))
                    ->latest()
                    ->paginate(15, ['id', 'name'])
            );
        }


        if ($request->query('date')) {
            return new ActivityCollection(
                $activities->whereDate('date', $request->query('date'))
                    ->latest()
                    ->paginate(15, ['id', 'name'])
            );
        }

        return new ActivityCollection(
            $activities->latest()
                ->paginate(15, ['id', 'name'])
        );
    }

    public function store(CreateActivityRequest $request)
    {
        $data = $request->validated();
        $url = $request->file('picture')->store('public/activity_pictures');
        $data['picture'] = str_replace('public/', '', $url);
        $data['institution_id'] = $request->user()->institution_id;

        $users = User::where('institution_id', $request->user()->institution_id)->get();
        Notification::send($users, new ActivityCreated());
        return new ActivityResource(Activity::create($data));
    }

    public function show(Activity $activity)
    {
        return new ActivityResource($activity);
    }

    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $data = $request->validated();
        if ($request->file('picture')) {
            $url = $request->file('picture')->store('public/activity_pictures');
            $data['picture'] = str_replace('public/', '', $url);
        }

        $activity->update($request->validated());
        return new ActivityResource($activity);
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return new ActivityResource($activity);
    }
}
