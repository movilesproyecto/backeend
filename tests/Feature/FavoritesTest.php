<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite_requires_auth()
    {
        $deptId = DB::table('departments')->insertGetId([
            'name' => 'FavDept',
            'description' => 'x',
            'created_at' => now(),
            'updated_at' => now(),
            'published' => true,
        ]);

        $resp = $this->postJson("/api/departments/{$deptId}/favorite");
        $resp->assertStatus(401);
    }

    public function test_authenticated_user_can_favorite()
    {
        $user = User::create(['name' => 'FavUser', 'email' => 'fav@demo.com', 'password' => bcrypt('secret')]);

        $deptId = DB::table('departments')->insertGetId([
            'name' => 'FavDept',
            'description' => 'x',
            'created_at' => now(),
            'updated_at' => now(),
            'published' => true,
        ]);

        $resp = $this->actingAs($user, 'sanctum')->postJson("/api/departments/{$deptId}/favorite");
        $resp->assertStatus(200);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'department_id' => $deptId,
        ]);
    }
}
