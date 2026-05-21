<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,wmv|max:10240', // Max 10MB
        ]);

        // Handle media upload
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $path = $file->store('events', 'public');
            $validated['media_path'] = $path;
            
            // Determine media type
            $mimeType = $file->getMimeType();
            $validated['media_type'] = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
        }

        Event::create($validated);
        return redirect()->route('admin.events.index')->with('success', __('messages.event_created'));
    }

    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,wmv|max:10240',
        ]);

        // Handle media upload
        if ($request->hasFile('media')) {
            // Delete old media if exists
            if ($event->media_path) {
                Storage::disk('public')->delete($event->media_path);
            }
            
            $file = $request->file('media');
            $path = $file->store('events', 'public');
            $validated['media_path'] = $path;
            
            // Determine media type
            $mimeType = $file->getMimeType();
            $validated['media_type'] = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
        }

        $event->update($validated);
        return redirect()->route('admin.events.index')->with('success', __('messages.event_updated'));
    }

    public function destroy(Event $event)
    {
        // Delete associated media if exists
        if ($event->media_path) {
            Storage::disk('public')->delete($event->media_path);
        }
        
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', __('messages.event_deleted'));
    }
}
