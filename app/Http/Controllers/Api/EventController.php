<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Get all active events
     */
    public function index()
    {
        $events = Event::active()
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'media_url' => $event->media_path ? asset('storage/' . $event->media_path) : null,
                    'media_type' => $event->media_type,
                    'status' => $event->status,
                    'start_date' => $event->start_date,
                    'end_date' => $event->end_date,
                    'created_at' => $event->created_at,
                    'updated_at' => $event->updated_at,
                ];
            });

        return response()->json($events);
    }

    /**
     * Get single event details
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);

        return response()->json([
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'media_url' => $event->media_path ? asset('storage/' . $event->media_path) : null,
            'media_type' => $event->media_type,
            'status' => $event->status,
            'start_date' => $event->start_date,
            'end_date' => $event->end_date,
            'created_at' => $event->created_at,
            'updated_at' => $event->updated_at,
        ]);
    }
}
