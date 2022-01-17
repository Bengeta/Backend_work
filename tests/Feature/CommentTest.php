<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
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
        $post = Post::factory(1)->has(Comment::factory(5))->create()->first();
        $response = $this->getJson("/api/posts/{$post->slug}/comments");
        $response->assertJsonStructure([
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
        ]);
    }


    public function test_index_with_wrong_slug()
    {
        $response = $this->getJson("/api/posts/wrong_slug/comments");
        $response->assertStatus(404);
    }


    public function test_store_with_user()
    {
        $post = Post::factory(1)->create()->first();
        $user = User::factory()->make()->first();
        $comment = [
            'text' => 'new comment'
        ];

        $response = $this->actingAs($user)->postJson("/api/posts/{$post->slug}/comments", $comment);
        $response->assertStatus(201);
        $response->assertJson($comment);
    }


    public function test_store_without_user()
    {
        $post = Post::factory(1)->create()->first();
        $comment = [
            'text' => 'new comment'
        ];
        $response = $this->postJson("/api/posts/{$post->slug}/comments", $comment);
        $response->assertStatus(401);
        $response->assertExactJson([
            'message' => "Unauthenticated."
        ]);
    }


    public function test_store_validation()
    {
        $post = Post::factory(1)->create()->first();
        $user = User::factory()->make()->first();
        $comment = [];

        $response = $this->actingAs($user)->postJson("/api/posts/{$post->slug}/comments", $comment);
        $response->assertStatus(422);
    }


    public function test_update_by_author()
    {
        $user = User::factory()->create()->first();
        $post = Post::factory()->count(1)->has(Comment::factory(1)->for($user))->create()->first();
        $comment = $post->comments()->first();
        $new_comment = [
            'text' => 'new comment',
        ];

        $response = $this->actingAs($user)->putJson("/api/posts/{$post->slug}/comments/{$comment->id}", $new_comment);
        $response->assertStatus(200);
        $response->assertJson([
            'text' => 'new comment',
        ]);
    }

    public function test_update_by_admin()
    {
        $post = Post::factory()->count(1)->has(Comment::factory(1))->create()->first();
        $comment = $post->comments()->first();
        $new_comment = [
            'text' => 'new comment',
        ];
        $user = User::factory()->create()->first();
        $user->role = Role::ADMIN;

        $response = $this->actingAs($user)->putJson("/api/posts/{$post->slug}/comments/{$comment->id}", $new_comment);
        $response->assertStatus(200);
        $response->assertJson([
            'text' => 'new comment',
        ]);
    }

    public function test_update_no_by_author()
    {
        $post = Post::factory()->count(1)->has(Comment::factory(1))->create()->first();
        $comment = $post->comments()->first();
        $new_comment = [
            'text' => 'new comment',
        ];
        $user = User::factory()->create()->first();
        $response = $this->actingAs($user)
            ->putJson("/api/posts/{$post->slug}/comments/{$comment->id}", $new_comment);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This action is unauthorized.'
        ]);
    }

    public function test_show_with_right_id()
    {
        $post = Post::factory()->count(1)->has(Comment::factory(1))->create()->first();
        $comment = $post->comments()->first();
        $user = User::query()->where('id', $comment->user_id)->first();

        $response = $this->getJson("/api/posts/{$post->slug}/comments/{$comment->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'author' => [
                'login' => $user->login,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'text' => $comment->text,
        ]);
    }

    public function test_show_with_wrong_id()
    {
        $post = Post::factory(1)->create()->first();
        $response = $this->getJson("/api/posts/{$post->slug}/comments/1000000");
        $response->assertStatus(404);
        $response->assertExactJson([
            'message' => 'Post or comment not found'
        ]);
    }

    public function test_delete_by_admin()
    {
        $post = Post::factory(1)->has(Comment::factory(1))->create()->first();
        $user = User::factory(1)->create()->first();
        $user->role = Role::ADMIN;
        $comment = $post->comments()->first();

        $response = $this->actingAs($user)->deleteJson("/api/posts/{$post->slug}/comments/{$comment->id}");
        $response->assertExactJson([
            'message' => 'Comment removed successfully',
        ]);
    }


    public function test_delete_by_moderator()
    {
        $post = Post::factory(1)->has(Comment::factory(1))->create()->first();
        $user = User::factory(1)->create()->first();
        $user->role = Role::MODERATOR;
        $comment = $post->comments()->first();

        $response = $this->actingAs($user)->deleteJson("/api/posts/{$post->slug}/comments/{$comment->id}");
        $response->assertExactJson([
            'message' => 'Comment removed successfully',
        ]);
    }

    public function test_delete_not_by_moderator()
    {
        $post = Post::factory(1)->has(Comment::factory(1))->create()->first();
        $comment = $post->comments()->first();
        $user = User::factory(1)->create()->first();
        $response = $this->actingAs($user)->deleteJson("/api/posts/{$post->slug}/comments/{$comment->id}");
        $response->assertStatus(403);
    }


    public function test_delete_by_author()
    {
        $user = User::factory(1)->create()->first();
        $post = Post::factory(1)->has(Comment::factory(1)->for($user))->create()->first();
        $comment = $post->comments()->first();
        $response = $this->actingAs($user)->deleteJson("/api/posts/{$post->slug}/comments/{$comment->id}");
        $response->assertExactJson([
            'message' => 'Comment removed successfully',
        ]);
    }

    public function test_delete_no_by_author()
    {
        $post = Post::factory(1)->has(Comment::factory(1))->create()->first();
        $comment = $post->comments()->first();
        $user = User::factory(1)->create()->first();
        $response = $this->actingAs($user)->deleteJson("/api/posts/{$post->slug}/comments/{$comment->id}");
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This action is unauthorized.'
        ]);
    }


}
