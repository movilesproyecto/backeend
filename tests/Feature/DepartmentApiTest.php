<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Department;

class DepartmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_filters_and_pagination()
    {
        Department::factory()->count(30)->create();
        $res = $this->getJson('/api/departments?per_page=5');
        $res->assertStatus(200)->assertJsonStructure(['data','links','meta']);
    }

    public function test_favorite_requires_auth()
    {
        $dept = Department::factory()->create();
        $res = $this->postJson('/api/departments/'.$dept->id.'/favorite');
        $res->assertStatus(401);
    }
}
