<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileLinkRenderingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_page_renders_profile_link_as_unescaped_html_anchor()
    {
        $this->loginAsUser();
        $user = User::factory()->create(['name' => 'Jono']);

        $response = $this->get(route('users.show', $user->id));

        $response->assertStatus(200);
        $response->assertSee('<a href="'.route('users.show', [$user->id]).'">Jono</a>', false);
    }

    /** @test */
    public function family_chart_page_renders_profile_link_as_unescaped_html_anchor()
    {
        $this->loginAsUser();
        $user = User::factory()->create(['name' => 'Jeni']);

        $response = $this->get(route('users.chart', $user->id));

        $response->assertStatus(200);
        $response->assertSee('<a href="'.route('users.chart', [$user->id]).'">Jeni</a>', false);
    }

    /** @test */
    public function profile_page_renders_father_link_as_unescaped_html_anchor()
    {
        $this->loginAsUser();
        $father = User::factory()->male()->create(['name' => 'Budi']);
        $user = User::factory()->create(['name' => 'Jono', 'father_id' => $father->id]);

        $response = $this->get(route('users.show', $user->id));

        $response->assertStatus(200);
        $response->assertSee('<a href="'.route('users.show', [$father->id]).'">Budi</a>', false);
    }

    /** @test */
    public function profile_page_renders_mother_link_as_unescaped_html_anchor()
    {
        $this->loginAsUser();
        $mother = User::factory()->female()->create(['name' => 'Siti']);
        $user = User::factory()->create(['name' => 'Jono', 'mother_id' => $mother->id]);

        $response = $this->get(route('users.show', $user->id));

        $response->assertStatus(200);
        $response->assertSee('<a href="'.route('users.show', [$mother->id]).'">Siti</a>', false);
    }

    /** @test */
    public function profile_page_does_not_render_escaped_father_link_html()
    {
        $this->loginAsUser();
        $father = User::factory()->male()->create(['name' => 'Budi']);
        $user = User::factory()->create(['name' => 'Jono', 'father_id' => $father->id]);

        $response = $this->get(route('users.show', $user->id));

        $response->assertStatus(200);
        $response->assertDontSee('&lt;a href=', false);
    }

    /** @test */
    public function profile_page_does_not_render_escaped_mother_link_html()
    {
        $this->loginAsUser();
        $mother = User::factory()->female()->create(['name' => 'Siti']);
        $user = User::factory()->create(['name' => 'Jono', 'mother_id' => $mother->id]);

        $response = $this->get(route('users.show', $user->id));

        $response->assertStatus(200);
        $response->assertDontSee('&lt;a href=', false);
    }

    /** @test */
    public function profile_link_name_is_escaped_against_html_injection()
    {
        $this->loginAsUser();
        $user = User::factory()->create(['name' => '<script>alert(1)</script>']);

        $response = $this->get(route('users.show', $user->id));

        $response->assertStatus(200);
        $response->assertSee('<a href="'.route('users.show', [$user->id]).'">&lt;script&gt;alert(1)&lt;/script&gt;</a>', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }
}
