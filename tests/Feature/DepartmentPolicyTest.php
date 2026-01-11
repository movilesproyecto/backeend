<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DepartmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_update_department()
    {
        $user = User::create(['name' => 'User', 'email' => 'user@demo.com', 'password' => bcrypt('secret')]);

        $deptId = DB::table('departments')->insertGetId([
            'name' => 'Dept test',
            'description' => 'desc',
            'created_at' => now(),
            'updated_at' => now(),
            'published' => true,
        ]);

        $resp = $this->actingAs($user, 'sanctum')->putJson("/api/departments/{$deptId}", ['name' => 'Updated']);
        $resp->assertStatus(403);
    }

    public function test_admin_can_update_department()
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@demo.com', 'password' => bcrypt('secret')]);

        $deptId = DB::table('departments')->insertGetId([
            'name' => 'Dept test',
            'description' => 'desc',
            'created_at' => now(),
            'updated_at' => now(),
            'published' => true,
        ]);

        $resp = $this->actingAs($admin, 'sanctum')->putJson("/api/departments/{$deptId}", ['name' => 'Updated']);
        $resp->assertSuccessful();
    }
}
