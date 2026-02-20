<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class ProjectGeneratorController extends Controller
{
    private $baseBoilerplatePath;
    private $generatedProjectsPath;

    public function __construct()
    {
        // Define paths
        $this->baseBoilerplatePath = storage_path('app/laravel-boilerplate');
        $this->generatedProjectsPath = storage_path('app/generated_projects');

        // Ensure generated projects directory exists
        if (!File::exists($this->generatedProjectsPath)) {
            File::makeDirectory($this->generatedProjectsPath, 0755, true);
        }
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'db_connection' => 'required|in:mysql,pgsql,sqlite,sqlsrv',
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        $projectId = Str::uuid()->toString();
        $targetPath = $this->generatedProjectsPath . DIRECTORY_SEPARATOR . $projectId;
        $logFile = storage_path('logs/project-build-' . $projectId . '.log');

        // Create log file to prepare for streaming
        File::put($logFile, "[START] Initializing project builder...\n");

        try {
            // Dispatch the job to run composer in background
            \App\Jobs\GenerateProjectJob::dispatch($projectId, $targetPath, $validated, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Project generation started in background.',
                'project_id' => $projectId,
                'log_path' => 'project-build-' . $projectId . '.log'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to queue project generation: ' . $e->getMessage()], 500);
        }
    }

    public function streamLog($id)
    {
        $logFile = storage_path('logs/project-build-' . $id . '.log');

        if (!File::exists($logFile)) {
            return response()->json(['log' => 'Awaiting build sequence...', 'done' => false]);
        }

        $logContent = File::get($logFile);
        $done = str_contains($logContent, '[DONE]');

        return response()->json([
            'log' => $logContent,
            'done' => $done
        ]);
    }

    public function getTree($id)
    {
        $targetPath = $this->generatedProjectsPath . DIRECTORY_SEPARATOR . $id;

        if (!File::exists($targetPath)) {
            return response()->json(['error' => 'Project not found.'], 404);
        }

        $tree = $this->buildTree($targetPath);

        return response()->json([
            'id' => $id,
            'tree' => $tree
        ]);
    }

    public function handleAiChat(Request $request, $id)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'active_file_path' => 'required|string'
        ]);

        $projectRoot = realpath($this->generatedProjectsPath . DIRECTORY_SEPARATOR . $id);

        if (!$projectRoot) {
            return response()->json(['error' => 'Project not found.'], 404);
        }

        $requestedPath = realpath($this->generatedProjectsPath . DIRECTORY_SEPARATOR . $validated['active_file_path']);

        // Sandbox Validation
        if (!$requestedPath || !str_starts_with($requestedPath, $projectRoot) || !File::isFile($requestedPath)) {
            return response()->json(['error' => 'Invalid or unauthorized file access.'], 403);
        }

        $currentFileContent = File::get($requestedPath);
        $userPrompt = $validated['prompt'];
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API Key is not configured on the server.'], 500);
        }

        $systemInstruction = "You are a professional AI software engineer modifying a codebase. The user wants you to modify the following file. ONLY return the modified code of the entire file. Do NOT use markdown code block wrappers (like ```php). Just return raw text data of the final edited file. If the request is impossible, reply with ERROR: <reason>.";

        $fullPrompt = "System Instruction: {$systemInstruction}\n\nFile Path: {$validated['active_file_path']}\n\n[FILE CONTENT START]\n{$currentFileContent}\n[FILE CONTENT END]\n\nUser Prompt: {$userPrompt}\n\nRemember: return ONLY the completed, raw, editable code.";

        try {
            $response = \Illuminate\Support\Facades\Http::post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $fullPrompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1, // extremely low temperature for precision in code writing
                    ]
                ]
            );

            $result = $response->json();

            if (isset($result['error'])) {
                return response()->json(['error' => 'Gemini API Error: ' . $result['error']['message']], 500);
            }

            if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return response()->json(['error' => 'Invalid response from model.'], 500);
            }

            $aiGeneratedCode = trim($result['candidates'][0]['content']['parts'][0]['text']);

            // Cleanup potential markdown blocks if AI hallucinates despite instructions
            if (str_starts_with($aiGeneratedCode, '```')) {
                $lines = explode("\n", $aiGeneratedCode);
                array_shift($lines); // remove first line (e.g. ```php)
                if (str_ends_with(trim(end($lines)), '```')) {
                    array_pop($lines); // remove last block
                }
                $aiGeneratedCode = implode("\n", $lines);
            }

            if (str_starts_with($aiGeneratedCode, 'ERROR:')) {
                return response()->json(['error' => $aiGeneratedCode], 400);
            }

            // Save the newly modified code completely overriding the file
            File::put($requestedPath, $aiGeneratedCode);

            return response()->json([
                'success' => true,
                'message' => 'Successfully modified the document based on your prompt!',
                'edited_content' => $aiGeneratedCode,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to connect to AI server: ' . $e->getMessage()], 500);
        }
    }

    private function buildTree($dir)
    {
        $result = [];
        $items = scandir($dir);

        // Sort: directories first, then files
        usort($items, function ($a, $b) use ($dir) {
            if ($a === '.' || $a === '..') return -1;
            if ($b === '.' || $b === '..') return 1;
            $aIsDir = is_dir($dir . DIRECTORY_SEPARATOR . $a);
            $bIsDir = is_dir($dir . DIRECTORY_SEPARATOR . $b);
            if ($aIsDir && !$bIsDir) return -1;
            if (!$aIsDir && $bIsDir) return 1;
            return strcasecmp($a, $b);
        });

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            // Exclude some items to make it cleaner / safer
            if (in_array($item, ['.git', 'vendor', 'node_modules', 'storage'])) {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            $isDir = is_dir($path);

            $node = [
                'name' => $item,
                'type' => $isDir ? 'directory' : 'file',
                // Keep path relative to project root for security
                'path' => str_replace($this->generatedProjectsPath . DIRECTORY_SEPARATOR, '', $path),
            ];

            if ($isDir) {
                $node['children'] = $this->buildTree($path);
            }

            $result[] = $node;
        }

        return $result;
    }

    public function getFile(Request $request, $id)
    {
        $relativePath = $request->query('path');

        if (!$relativePath) {
            return response()->json(['error' => 'Path is required'], 400);
        }

        $projectRoot = realpath($this->generatedProjectsPath . DIRECTORY_SEPARATOR . $id);

        if (!$projectRoot || !File::exists($projectRoot)) {
            return response()->json(['error' => 'Project not found.'], 404);
        }

        // Construct requested path and resolve its real path
        $requestedPath = realpath($this->generatedProjectsPath . DIRECTORY_SEPARATOR . $relativePath);

        // Sandbox Validation: Ensure the requested path is INSIDE the project root
        if (!$requestedPath || !str_starts_with($requestedPath, $projectRoot)) {
            return response()->json(['error' => 'Invalid or unauthorized path access.'], 403);
        }

        if (!File::isFile($requestedPath)) {
            return response()->json(['error' => 'File not found or is a directory.'], 404);
        }

        // Check file size (e.g. max 2MB) to prevent memory issues with massive files
        if (File::size($requestedPath) > 2 * 1024 * 1024) {
            return response()->json(['error' => 'File is too large to display.'], 400);
        }

        // Check if file is readable text (simple check)
        $extension = pathinfo($requestedPath, PATHINFO_EXTENSION);
        $binaryExtensions = ['jpg', 'jpeg', 'png', 'gif', 'ico', 'pdf', 'zip', 'tar', 'gz', 'mp4', 'mp3', 'woff', 'woff2', 'ttf', 'eot'];

        if (in_array(strtolower($extension), $binaryExtensions)) {
            return response()->json(['error' => 'Cannot view binary files.'], 400);
        }

        $content = File::get($requestedPath);

        return response()->json([
            'path' => $relativePath,
            'content' => $content
        ]);
    }
}
