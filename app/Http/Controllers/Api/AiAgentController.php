<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class AiAgentController extends Controller
{
    /**
     * The base URL for the AI Agent service.
     */
    private string $agentBaseUrl;

    public function __construct()
    {
        $this->agentBaseUrl = env('AI_AGENT_URL', 'http://localhost:8002');
    }

    /**
     * Forward a chat message to the AI Agent and return the response.
     *
     * POST /api/ai/chat
     * Body: { "message": "how many customers?", "session_id": "optional-session-id" }
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:100',
        ]);

        try {
            $response = Http::timeout(30)->post("{$this->agentBaseUrl}/api/chat", [
                'message' => $request->input('message'),
                'session_id' => $request->input('session_id'),
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'AI Agent returned an error',
                'details' => $response->json(),
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to connect to AI Agent',
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Clear the AI conversation history for a session.
     *
     * POST /api/ai/clear-session
     * Body: { "session_id": "session-id-to-clear" }
     */
    public function clearSession(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string|max:100',
        ]);

        try {
            $response = Http::timeout(10)->post("{$this->agentBaseUrl}/api/clear-session", [
                'session_id' => $request->input('session_id'),
            ]);

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to connect to AI Agent',
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Get AI Agent health status.
     *
     * GET /api/ai/health
     */
    public function health(): JsonResponse
    {
        try {
            $response = Http::timeout(5)->get("{$this->agentBaseUrl}/api/health");
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unavailable',
                'message' => $e->getMessage(),
            ], 503);
        }
    }
}
