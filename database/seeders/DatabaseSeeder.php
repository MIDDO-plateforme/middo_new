<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DevSeeder::class);
        echo "\n🧪 Création des utilisateurs...\n";
        
        User::firstOrCreate(['email'=>'test@middo.com'],['name'=>'Test MIDDO','password'=>Hash::make('password123'),'phone'=>'+243 900 000 001','country'=>'RDC','sector'=>'Tech','bio'=>'Utilisateur de test','status'=>'active']);
        User::firstOrCreate(['email'=>'entrepreneur@middo.com'],['name'=>'Entrepreneur Test','password'=>Hash::make('password123'),'phone'=>'+243 900 000 002','country'=>'RDC','sector'=>'Agriculture','bio'=>'Entrepreneur agricole','status'=>'active']);
        User::firstOrCreate(['email'=>'investisseur@middo.com'],['name'=>'Investisseur Test','password'=>Hash::make('password123'),'phone'=>'+243 900 000 003','country'=>'France','sector'=>'Finance','bio'=>'Investisseur international','status'=>'active']);
        User::firstOrCreate(['email'=>'dev@middo.com'],['name'=>'Développeur Principal','password'=>Hash::make('dev123'),'phone'=>'+243 900 000 100','country'=>'RDC','sector'=>'Tech','bio'=>'Dev principal','status'=>'active']);
        User::firstOrCreate(['email'=>'admin@middo.com'],['name'=>'Admin Système','password'=>Hash::make('admin123'),'phone'=>'+243 900 000 101','country'=>'RDC','sector'=>'Tech','bio'=>'Admin système','status'=>'active']);
        User::firstOrCreate(['email'=>'qa@middo.com'],['name'=>'QA Tester','password'=>Hash::make('qa123'),'phone'=>'+243 900 000 102','country'=>'RDC','sector'=>'Tech','bio'=>'QA Tests','status'=>'active']);
        
        echo "✅ ".User::count()." utilisateurs!\n📧 Tests: test@middo.com (password123)\n👨‍💻 Devs: dev@middo.com (dev123), admin@middo.com (admin123), qa@middo.com (qa123)\n";
    }
}