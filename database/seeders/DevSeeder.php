<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        $this->info("🔧 DevSeeder: Auto-correction et structuration...");
        $this->ensureBaseController();
        $this->ensureDirectories();
        $this->ensureConfigFiles();
        $this->cleanTempFiles();
        $this->info("✅ DevSeeder: Tous les fichiers sont correctement structurés!");
    }

    protected function ensureBaseController(): void
    {
        $controllerPath = app_path('Http/Controllers/Controller.php');
        if (!File::exists($controllerPath)) {
            $content = "<?php\n\nnamespace App\Http\Controllers;\n\nabstract class Controller\n{\n    //\n}\n";
            File::put($controllerPath, $content);
            $this->info("  ✅ Controller de base créé");
        } else {
            $this->info("  ✓ Controller de base existe");
        }
    }

    protected function ensureDirectories(): void
    {
        $directories = [
            resource_path('views'), resource_path('views/auth'), resource_path('views/layouts'),
            resource_path('views/components'), resource_path('css'), resource_path('js'),
            storage_path('framework/cache/data'), storage_path('framework/sessions'),
            storage_path('framework/views'), storage_path('logs'), app_path('Http/Controllers/Auth'),
        ];
        foreach ($directories as $dir) {
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->info("  ✅ Dossier créé: " . basename($dir));
            }
        }
    }

    protected function ensureConfigFiles(): void
    {
        $packagePath = base_path('package.json');
        if (!File::exists($packagePath)) {
            $content = json_encode(["private"=>true,"type"=>"module","scripts"=>["dev"=>"vite","build"=>"vite build"],"devDependencies"=>["@tailwindcss/forms"=>"^0.5.9","alpinejs"=>"^3.14.8","autoprefixer"=>"^10.4.21","axios"=>"^1.7.9","laravel-vite-plugin"=>"^1.1.1","postcss"=>"^8.4.49","tailwindcss"=>"^3.4.17","vite"=>"^6.0.5"]], JSON_PRETTY_PRINT);
            File::put($packagePath, $content);
            $this->info("  ✅ package.json créé");
        }
    }

    protected function cleanTempFiles(): void
    {
        $patterns = [storage_path('framework/cache/data/*'), storage_path('logs/*.log')];
        $cleaned = 0;
        foreach ($patterns as $pattern) {
            foreach (glob($pattern) as $file) {
                if (is_file($file)) { @unlink($file); $cleaned++; }
            }
        }
        if ($cleaned > 0) $this->info("  ✅ $cleaned fichiers temporaires nettoyés");
    }

    protected function info(string $message): void { echo $message . "\n"; }
}