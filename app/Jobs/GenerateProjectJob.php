<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use App\Models\User;
use App\Models\Project;

class GenerateProjectJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;

    protected $projectId;
    protected $targetPath;
    protected $config;
    protected $userId;

    public function __construct($projectId, $targetPath, $config, $userId)
    {
        $this->projectId = $projectId;
        $this->targetPath = $targetPath;
        $this->config = $config;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $logFile = storage_path('logs/project-build-' . $this->projectId . '.log');

        File::append($logFile, "Initializing installation environment...\n");

        if (File::exists($this->targetPath)) {
            File::deleteDirectory($this->targetPath);
        }

        // Run Composer Create Project
        $process = Process::fromShellCommandline('composer create-project laravel/laravel "' . $this->targetPath . '"');
        $process->setTimeout(300);

        File::append($logFile, "Executing: composer create-project laravel/laravel\n\n");

        $process->run(function ($type, $buffer) use ($logFile) {
            File::append($logFile, $buffer);
        });

        if (!$process->isSuccessful()) {
            File::append($logFile, "\n\n[ERROR] Failed to generate project.\n");
            File::append($logFile, $process->getErrorOutput());
            File::append($logFile, "\n\n[DONE] Error.");
            return;
        }

        File::append($logFile, "\n\nComposer installation completed successfully. Formatting configurations...\n");

        // Update ENV
        $this->updateEnvironmentFile($this->targetPath, $this->config);

        File::append($logFile, "Updating Environment attributes...\n");

        // Save History Data
        $user = User::find($this->userId);
        if ($user) {
            $user->projects()->create([
                'id' => $this->projectId,
                'name' => $this->config['project_name'],
                'description' => $this->config['description'] ?? null,
                'db_connection' => $this->config['db_connection']
            ]);
        }

        File::append($logFile, "Project history database entries secured.\n");
        File::append($logFile, "\n\nSUCCESS! System will redirect you shortly.\n");
        File::append($logFile, "[DONE]");
    }

    private function updateEnvironmentFile($projectPath, $config)
    {
        $envPath = $projectPath . DIRECTORY_SEPARATOR . '.env';

        if (!File::exists($envPath)) {
            $envExamplePath = $projectPath . DIRECTORY_SEPARATOR . '.env.example';
            if (File::exists($envExamplePath)) {
                File::copy($envExamplePath, $envPath);
            } else {
                return;
            }
        }

        $envContent = File::get($envPath);

        // App Name Update
        $envContent = preg_replace('/^APP_NAME=(.*)$/m', 'APP_NAME="' . $config['project_name'] . '"', $envContent);

        // Clean out any DB_* configs
        $envContent = preg_replace('/^#?\s*DB_[A-Z0-9_]+(=.*)?(\r\n|\r|\n|$)/m', '', $envContent);
        $envContent = preg_replace('/\n{3,}/', "\n\n", $envContent);

        $dbMappings = [
            'db_connection' => 'DB_CONNECTION',
            'db_host'       => 'DB_HOST',
            'db_port'       => 'DB_PORT',
            'db_database'   => 'DB_DATABASE',
            'db_username'   => 'DB_USERNAME',
            'db_password'   => 'DB_PASSWORD',
        ];

        $envContent .= "\n\n# Database Configurations\n";

        foreach ($dbMappings as $requestKey => $envKey) {
            if (isset($config[$requestKey]) && $config[$requestKey] !== '') {
                $value = $config[$requestKey];

                if (str_contains($value, ' ')) {
                    $value = '"' . $value . '"';
                }

                $envContent .= $envKey . '=' . $value . "\n";
            } elseif ($requestKey === 'db_password') {
                $envContent .= "DB_PASSWORD=\n";
            }
        }

        File::put($envPath, trim($envContent) . "\n");
    }
}
