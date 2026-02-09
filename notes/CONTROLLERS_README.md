# Enzobyte Technologies - Controllers Documentation

## Overview
Simple, straightforward controllers for a portfolio website with admin and public-facing APIs.

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ProjectController.php          # Public projects
│   │   ├── ServiceController.php          # Public services
│   │   ├── TeamController.php             # Public team members
│   │   ├── BlogController.php             # Public blog posts
│   │   ├── TestimonialController.php      # Public testimonials
│   │   ├── ContactController.php          # Contact form submission
│   │   └── Admin/
│   │       ├── ProjectController.php      # Admin CRUD for projects
│   │       ├── ServiceController.php      # Admin CRUD for services
│   │       ├── TeamMemberController.php   # Admin CRUD for team
│   │       ├── BlogPostController.php     # Admin CRUD for blog
│   │       ├── TestimonialController.php  # Admin CRUD for testimonials
│   │       └── ContactInquiryController.php # Manage contact inquiries
│   └── Resources/
│       ├── ProjectResource.php
│       ├── ServiceResource.php
│       ├── TeamMemberResource.php
│       ├── BlogPostResource.php
│       ├── TestimonialResource.php
│       └── ContactInquiryResource.php
```

## Public API Endpoints

### Projects
- `GET /api/v1/projects` - List published projects (with pagination)
  - Query params: `category`, `featured`
- `GET /api/v1/projects/featured` - Get featured projects
- `GET /api/v1/projects/{slug}` - Get single project by slug

### Services
- `GET /api/v1/services` - List active services
- `GET /api/v1/services/{slug}` - Get single service by slug

### Team
- `GET /api/v1/team` - List active team members
- `GET /api/v1/team/{id}` - Get single team member

### Blog
- `GET /api/v1/blog` - List published blog posts (with pagination)
  - Query params: `category`, `tag`
- `GET /api/v1/blog/recent/{limit?}` - Get recent posts (default: 5)
- `GET /api/v1/blog/{slug}` - Get single blog post (increments views)

### Testimonials
- `GET /api/v1/testimonials` - List published testimonials
- `GET /api/v1/testimonials/featured` - Get featured testimonials

### Contact
- `POST /api/v1/contact` - Submit contact inquiry

## Admin API Endpoints (Protected)

All admin routes require authentication (`auth:sanctum` middleware).

### Projects
- `GET /api/admin/projects` - List all projects
- `POST /api/admin/projects` - Create new project
- `GET /api/admin/projects/{id}` - Get single project
- `PUT /api/admin/projects/{id}` - Update project
- `DELETE /api/admin/projects/{id}` - Delete project (soft delete)

### Services
- `GET /api/admin/services` - List all services
- `POST /api/admin/services` - Create new service
- `GET /api/admin/services/{id}` - Get single service
- `PUT /api/admin/services/{id}` - Update service
- `DELETE /api/admin/services/{id}` - Delete service

### Team Members
- `GET /api/admin/team-members` - List all team members
- `POST /api/admin/team-members` - Create new team member
- `GET /api/admin/team-members/{id}` - Get single team member
- `PUT /api/admin/team-members/{id}` - Update team member
- `DELETE /api/admin/team-members/{id}` - Delete team member

### Blog Posts
- `GET /api/admin/blog-posts` - List all blog posts
- `POST /api/admin/blog-posts` - Create new blog post
- `GET /api/admin/blog-posts/{id}` - Get single blog post
- `PUT /api/admin/blog-posts/{id}` - Update blog post
- `DELETE /api/admin/blog-posts/{id}` - Delete blog post (soft delete)

### Testimonials
- `GET /api/admin/testimonials` - List all testimonials
- `POST /api/admin/testimonials` - Create new testimonial
- `GET /api/admin/testimonials/{id}` - Get single testimonial
- `PUT /api/admin/testimonials/{id}` - Update testimonial
- `DELETE /api/admin/testimonials/{id}` - Delete testimonial

### Contact Inquiries
- `GET /api/admin/inquiries` - List all inquiries
  - Query params: `status`
- `GET /api/admin/inquiries/stats` - Get inquiry statistics
- `GET /api/admin/inquiries/{id}` - Get single inquiry
- `PUT /api/admin/inquiries/{id}` - Update inquiry (status, notes)
- `DELETE /api/admin/inquiries/{id}` - Delete inquiry

## Key Features

### Public Controllers
- Simple, read-only operations
- Use scopes for filtering (published, featured, active)
- Pagination where appropriate
- Auto-increment blog post views
- Clean validation on contact form

### Admin Controllers
- Full CRUD operations
- Validation on all inputs
- Auto-generate slugs from titles
- Soft deletes for projects and blog posts
- Auto-set `published_at` when publishing blog posts
- Auto-set `contacted_at` when marking inquiry as contacted
- Statistics endpoint for contact inquiries

### API Resources
- Consistent JSON formatting
- Eager loading relationships (`whenLoaded`)
- Clean date formatting
- No sensitive data exposed

## Usage Examples

### Submit Contact Form (Public)
```javascript
fetch('/api/v1/contact', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    name: 'John Doe',
    email: 'john@example.com',
    message: 'I need a website',
    service_needed: 'web_development',
    budget_range: '10k_25k'
  })
})
```

### Create Project (Admin)
```javascript
fetch('/api/admin/projects', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  },
  body: JSON.stringify({
    title: 'E-commerce Website',
    description: 'Modern online store',
    thumbnail: '/images/project-1.jpg',
    category: 'web_development',
    technologies: ['Laravel', 'Vue.js', 'Tailwind'],
    featured: true,
    is_published: true
  })
})
```

### Update Inquiry Status (Admin)
```javascript
fetch('/api/admin/inquiries/1', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  },
  body: JSON.stringify({
    status: 'contacted',
    internal_notes: 'Called client, scheduled meeting for next week'
  })
})
```

## Installation Steps

1. Copy all controller files to `app/Http/Controllers/`
2. Copy all resource files to `app/Http/Resources/`
3. Update `routes/api.php` with the provided routes
4. Run migrations (already done)
5. Set up authentication (Laravel Sanctum recommended)

## Notes

- All controllers are kept simple with minimal business logic
- Validation is done inline with `Validator::make()`
- File uploads should be handled separately (not included)
- Email notifications are commented out but can be added
- Admin routes require authentication middleware
- Perfect for a portfolio/agency website
