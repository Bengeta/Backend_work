<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function tearDown(): void
    {
        $this->artisan('migrate:reset');
    }

    public function test_index()
    {
        $response = $this->getJson('/api/posts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'title',
                'slug',
                'text',
                'created_at',
                'updated_at',
                'author' => [
                    'login',
                    'name',
                    'email'
                ],
                'comments' => [
                    '*' => [
                        'id',
                        'text',
                        'created_at',
                        'updated_at',
                        'author' => [
                            'name',
                            'login',
                            'email'
                        ],
                    ]
                ],

            ]
        ]);
    }


    public function test_store_with_user()
    {
        $post = [
            'title' => 'title',
            'text' => 'text'
        ];
        $user = User::factory()->make()->first();

        $response = $this->actingAs($user)->postJson('/api/posts', $post);
        $response->assertStatus(201);
        $response->assertJson($post);
    }

    public function test_store_without_user()
    {
        $post = [
            'title' => 'title',
            'text' => 'text'
        ];
        $response = $this->postJson('/api/posts', $post);
        $response->assertStatus(401);
        $response->assertExactJson([
            'message' => "Unauthenticated."
        ]);
    }

    public function test_store_validation()
    {
        $post = [
            'title' => 'title',
        ];
        $user = User::factory()->make()->first();
        $response = $this->actingAs($user)->postJson('/api/posts', $post);
        $response->assertStatus(422);
        $response->assertExactJson([
            'The text field is required.',
        ]);
    }

    public function test_update_by_admin() {
        $post = Post::factory()->create()->first();
        $new_post = [
            'title' => 'title',
        ];
        $user = User::factory()->create()->first();
        $user->role = Role::ADMIN;

        $response = $this->actingAs($user)->putJson('/api/posts/'.$post->slug, $new_post);
        $response->assertStatus(200);
        $response->assertJson([
            'title' => 'title',
            'text' => $post->text,
        ]);
    }


    public function test_update_by_author()
    {
        $user = User::factory()->create()->first();
        $post = Post::factory()->count(1)->for($user)->create()->first();
        $new_post = [
            'title' => 'title',
        ];

        $response = $this->actingAs($user)->putJson('/api/posts/' . $post->slug, $new_post);
        $response->assertStatus(200);
        $response->assertJson([
            'title' => 'title',
            'text' => $post->text,
        ]);
    }

    public function test_update_no_by_author()
    {
        $post = Post::factory()->count(1)->create()->first();
        $new_post = [
            'title' => 'title',
        ];
        $user = User::factory()->create()->first();
        $response = $this->actingAs($user)->putJson('/api/posts/' . $post->slug, $new_post);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This action is unauthorized.'
        ]);
    }


    public function test_show_with_right_slug()
    {
        $post = Post::all()->first();
        $author = User::query()->where('id', $post->user_id)->first();
        $response = $this->getJson('/api/posts/' . $post->slug);
        $response->assertStatus(200);
        $response->assertJson([
            'title' => $post->title,
            'slug' => $post->slug,
            'text' => $post->text,
            'author' => [
                'login' => $author->login,
                'name' => $author->name,
                'email' => $author->email,
            ]
        ]);
    }

    public function test_show_with_wrong_slug()
    {
        $response = $this->getJson('/api/posts/10000');
        $response->assertStatus(404);
        $response->assertExactJson([
            'message' => 'Post not found'
        ]);
    }

    public function test_delete_by_moderator()
    {
        $post = Post::factory(1)->create()->first();
        $user = User::factory(1)->create()->first();
        $user->role = Role::MODERATOR;

        $response = $this->actingAs($user)->deleteJson('/api/posts/' . $post->slug);
        $response->assertExactJson([
            'message' => 'Post removed successfully',
        ]);
    }

    public function test_delete_by_admin()
    {
        $post = Post::factory(1)->create()->first();
        $user = User::factory(1)->create()->first();
        $user->role = Role::ADMIN;

        $response = $this->actingAs($user)->deleteJson('/api/posts/' . $post->slug);
        $response->assertExactJson([
            'message' => 'Post removed successfully',
        ]);
    }

    public function test_delete_by_author()
    {
        $user = User::factory(1)->create()->first();
        $post = Post::factory(1)->for($user)->create()->first();
        $response = $this->actingAs($user)->deleteJson('/api/posts/' . $post->slug);
        $response->assertExactJson([
            'message' => 'Post removed successfully',
        ]);
    }

    public function test_delete_no_by_author()
    {
        $post = Post::all()->first();
        $user = User::factory(1)->create()->first();
        $response = $this->actingAs($user)->deleteJson('/api/posts/' . $post->slug);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This action is unauthorized.'
        ]);
    }

    public function test_delete_by_author_with_his_comments()
    {
        $user = User::factory(1)->create()->first();
        $post = Post::factory(1)->has(Comment::factory(1)->for($user))->for($user)->create()->first();
        $response = $this->actingAs($user)->deleteJson('/api/posts/' . $post->slug);
        $response->assertExactJson([
            'message' => 'Post removed successfully',
        ]);
    }

    public function test_delete_by_author_with_no_his_comments()
    {
        $user = User::factory(1)->create()->first();
        $other_user = User::factory(1)->create()->first();
        $post = Post::factory(1)->has(Comment::factory(1)->for($other_user))->for($user)->create()->first();
        $response = $this->actingAs($user)->deleteJson('/api/posts/' . $post->slug);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This action is unauthorized.'
        ]);
    }


}
