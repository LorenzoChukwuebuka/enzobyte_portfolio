<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('full_description')->nullable();
            $table->string('client_name')->nullable();
            $table->string('project_url')->nullable();
            $table->string('thumbnail');              // Main project image
            $table->json('gallery')->nullable();      // Array of additional images
            $table->json('technologies')->nullable(); // Array of tech stack
            $table->enum('category', ['web_development', 'mobile_app', 'ui_ux_design', 'branding', 'consulting', 'other']);
            $table->date('completion_date')->nullable();
            $table->integer('duration_days')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->text('full_description')->nullable();
            $table->string('icon')->nullable();          // SVG icon or icon class
            $table->json('features')->nullable();        // Array of service features
            $table->string('pricing_model')->nullable(); // 'fixed', 'hourly', 'project_based'
            $table->decimal('base_price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->string('email')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('github')->nullable();
            $table->json('skills')->nullable(); // Array of skills
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text');     // text, textarea, image, json, boolean
            $table->string('group')->default('general'); // general, contact, social, seo
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_position')->nullable();
            $table->string('client_company')->nullable();
            $table->text('testimonial');
            $table->integer('rating')->default(5); // 1-5 star rating
            $table->string('client_photo')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->enum('service_needed', [
                'web_development',
                'mobile_app',
                'ui_ux_design',
                'branding',
                'consulting',
                'maintenance',
                'other',
            ])->nullable();
            $table->text('message');
            $table->enum('budget_range', [
                'not_specified',
                'under_500k',
                '500k_1m',
                '1m_2m',
                '2m_5m',
                '5m_plus',
            ])->default('not_specified');

            $table->enum('status', ['new', 'in_progress', 'contacted', 'qualified', 'converted', 'closed'])->default('new');
            $table->text('internal_notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('team_members')->onDelete('set null');
            $table->json('tags')->nullable(); // Array of tags
            $table->enum('category', ['technology', 'design', 'business', 'tutorials', 'case_studies', 'news']);
            $table->integer('read_time_minutes')->nullable();
            $table->integer('views')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('site_settings')->insert([

            /*
    |--------------------------------------------------------------------------
    | General Settings
    |--------------------------------------------------------------------------
    */
            [
                'key'        => 'site_name',
                'value'      => 'Enzobyte Technology',
                'type'       => 'text',
                'group'      => 'general',
                'label'      => 'Site Name',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'site_tagline',
                'value'      => 'Innovative Tech Solutions for Modern Businesses',
                'type'       => 'text',
                'group'      => 'general',
                'label'      => 'Site Tagline',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'site_logo',
                'value'      => '/images/enzobyte_logo.png',
                'type'       => 'image',
                'group'      => 'general',
                'label'      => 'Site Logo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'homepage_intro',
                'value'      => 'We build scalable software solutions, modern web platforms, and enterprise systems tailored to your business needs.',
                'type'       => 'textarea',
                'group'      => 'general',
                'label'      => 'Homepage Introduction',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    */
            [
                'key'        => 'contact_email',
                'value'      => 'hello@enzobyte.tech',
                'type'       => 'text',
                'group'      => 'contact',
                'label'      => 'Contact Email',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'contact_phone',
                'value'      => null,
                'type'       => 'text',
                'group'      => 'contact',
                'label'      => 'Contact Phone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'contact_address',
                'value'      => null,
                'type'       => 'textarea',
                'group'      => 'contact',
                'label'      => 'Office Address',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            /*
    |--------------------------------------------------------------------------
    | Social Media (Not configured yet)
    |--------------------------------------------------------------------------
    */
            [
                'key'        => 'social_linkedin',
                'value'      => null,
                'type'       => 'text',
                'group'      => 'social',
                'label'      => 'LinkedIn URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'social_twitter',
                'value'      => null,
                'type'       => 'text',
                'group'      => 'social',
                'label'      => 'Twitter URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'social_github',
                'value'      => null,
                'type'       => 'text',
                'group'      => 'social',
                'label'      => 'GitHub URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */
            [
                'key'        => 'meta_description',
                'value'      => 'Enzobyte Technology delivers cutting-edge web development, SaaS platforms, enterprise systems, and digital transformation solutions.',
                'type'       => 'textarea',
                'group'      => 'seo',
                'label'      => 'Meta Description',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'meta_keywords',
                'value'      => 'software development, SaaS, web development, enterprise software, Nigeria tech company',
                'type'       => 'text',
                'group'      => 'seo',
                'label'      => 'Meta Keywords',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
        Schema::dropIfExists('services');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('site_settings');
    }
};